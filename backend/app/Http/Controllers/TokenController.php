<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\MoralisService;

class TokenController extends Controller
{
    // -------------------------------------------------------
    // GET /api/new-launches
    // Returns latest token launches for mobile app
    // -------------------------------------------------------
    public function newLaunches(Request $request)
    {
        try {
            $limit  = $request->get('limit', 20);
            $offset = $request->get('offset', 0);

            $tokens = DB::table('tokens')
                ->orderBy('detected_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get([
                    'token_address', 'pair_address', 'name', 'symbol',
                    'decimals', 'logo', 'initial_price', 'price_currency',
                    'detected_at', 'tx_hash', 'source_type', 'trade_mode',
                    'status', 'is_tradeable',
                ]);

            $addresses  = $tokens->pluck('token_address')->toArray();

            // Fire Moralis + DexScreener in parallel
            $livePrices = $this->fetchPricesParallel($addresses);

            $enriched = $tokens->map(function ($token) use ($livePrices) {
                $row  = (array) $token;
                $addr = strtolower($token->token_address);
                if (isset($livePrices[$addr])) {
                    $row['initial_price']  = $livePrices[$addr];
                    $row['price_currency'] = 'USD';
                }
                return $row;
            });

            return response()->json([
                'status' => 'success',
                'count'  => count($enriched),
                'data'   => $enriched,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // -------------------------------------------------------
    // GET /api/token/{address}
    // Returns single token detail with live price
    // -------------------------------------------------------
    public function getTokenDetail(string $address)
    {
        try {
            $token = DB::table('tokens')
                ->where('token_address', strtolower($address))
                ->first();

            if (!$token) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Token not found',
                ], 404);
            }

            $row    = (array) $token;
            $prices = $this->fetchPricesParallel([$address]);
            $addr   = strtolower($address);

            if (isset($prices[$addr])) {
                $row['initial_price']  = $prices[$addr];
                $row['price_currency'] = 'USD';
            }

            return response()->json([
                'status' => 'success',
                'data'   => $row,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // -------------------------------------------------------
    // Fire Moralis + DexScreener simultaneously, merge results
    // Moralis takes priority; DexScreener fills the gaps
    // -------------------------------------------------------
    private function fetchPricesParallel(array $addresses): array
    {
        if (empty($addresses)) return [];

        $dexChunks = array_chunk($addresses, 30);

        // Build all requests: 1 Moralis + N DexScreener chunks
        $responses = Http::pool(function ($pool) use ($addresses, $dexChunks) {
            $requests = [];

            // Moralis batch price endpoint
            $tokens = array_map(fn($a) => ['token_address' => strtolower($a)], $addresses);
            $requests[] = $pool->as('moralis')
                ->withHeaders(['X-API-Key' => $this->moralisKey()])
                ->timeout(6)
                ->post('https://deep-index.moralis.io/api/v2.2/erc20/prices?chain=0x38', [
                    'tokens' => $tokens,
                ]);

            // DexScreener (one request per chunk of 30)
            foreach ($dexChunks as $i => $chunk) {
                $requests[] = $pool->as("dex_{$i}")
                    ->timeout(6)
                    ->get('https://api.dexscreener.com/latest/dex/tokens/' . implode(',', $chunk));
            }

            return $requests;
        });

        $prices = [];

        // Parse Moralis response
        try {
            $moralisResp = $responses['moralis'];
            if ($moralisResp->successful()) {
                foreach ($moralisResp->json() as $item) {
                    $addr     = strtolower($item['tokenAddress'] ?? '');
                    $usdPrice = (float)($item['usdPrice'] ?? 0);
                    if ($addr && $usdPrice > 0) {
                        $prices[$addr] = $this->formatPrice($usdPrice);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Moralis parallel response error: ' . $e->getMessage());
        }

        // Parse DexScreener responses — only fill gaps Moralis missed
        foreach (array_keys($dexChunks) as $i) {
            try {
                $dexResp = $responses["dex_{$i}"];
                if (!$dexResp->successful()) continue;
                foreach ($dexResp->json()['pairs'] ?? [] as $pair) {
                    $addr  = strtolower($pair['baseToken']['address'] ?? '');
                    $price = (float)($pair['priceUsd'] ?? 0);
                    if ($addr && $price > 0 && !isset($prices[$addr])) {
                        $prices[$addr] = $this->formatPrice($price);
                    }
                }
            } catch (\Exception $e) {
                Log::warning("DexScreener chunk {$i} error: " . $e->getMessage());
            }
        }

        return $prices;
    }

    private function moralisKey(): string
    {
        return (new MoralisService())->getPublicApiKey();
    }

    private function formatPrice(float $price): string
    {
        if ($price <= 0)        return '0';
        if ($price < 0.000001) return number_format($price, 10, '.', '');
        if ($price < 0.0001)   return number_format($price, 8, '.', '');
        if ($price < 0.01)     return number_format($price, 6, '.', '');
        if ($price < 1)        return number_format($price, 4, '.', '');
        return number_format($price, 2, '.', '');
    }
}
