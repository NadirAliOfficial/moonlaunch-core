<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
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
                    'token_address',
                    'pair_address',
                    'name',
                    'symbol',
                    'decimals',
                    'logo',
                    'initial_price',
                    'price_currency',
                    'detected_at',
                    'tx_hash',
                    'source_type',
                    'trade_mode',
                    'status',
                    'is_tradeable',
                ]);

            // Fetch live USD prices from Moralis for all tokens
            $addresses  = $tokens->pluck('token_address')->toArray();
            $livePrices = (new MoralisService())->getTokenPrices($addresses);

            // Find tokens Moralis couldn't price — fall back to DexScreener
            $missing = array_values(array_filter($addresses, fn($a) => !isset($livePrices[strtolower($a)])));
            if (!empty($missing)) {
                $dexPrices  = $this->getDexScreenerPrices($missing);
                $livePrices = array_merge($livePrices, $dexPrices);
            }

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

            $row = (array) $token;

            // Try Moralis first, then DexScreener
            $prices = (new MoralisService())->getTokenPrices([$address]);
            $addr   = strtolower($address);
            if (isset($prices[$addr])) {
                $row['initial_price']  = $prices[$addr];
                $row['price_currency'] = 'USD';
            } else {
                $dex = $this->getDexScreenerPrices([$address]);
                if (isset($dex[$addr])) {
                    $row['initial_price']  = $dex[$addr];
                    $row['price_currency'] = 'USD';
                }
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
    // DexScreener fallback — free, no API key needed
    // Accepts up to 30 addresses at once
    // -------------------------------------------------------
    private function getDexScreenerPrices(array $addresses): array
    {
        $prices = [];
        // DexScreener allows up to 30 addresses per call
        foreach (array_chunk($addresses, 30) as $chunk) {
            $joined   = implode(',', $chunk);
            $cacheKey = 'dex_prices_' . md5($joined);

            $chunkPrices = Cache::remember($cacheKey, 120, function () use ($joined) {
                try {
                    $response = Http::timeout(5)
                        ->get("https://api.dexscreener.com/latest/dex/tokens/{$joined}");

                    if (!$response->successful()) return [];

                    $result = [];
                    foreach ($response->json()['pairs'] ?? [] as $pair) {
                        $addr = strtolower($pair['baseToken']['address'] ?? '');
                        $price = (float)($pair['priceUsd'] ?? 0);
                        if ($addr && $price > 0 && !isset($result[$addr])) {
                            $result[$addr] = $this->formatPrice($price);
                        }
                    }
                    return $result;
                } catch (\Exception $e) {
                    Log::warning('DexScreener fallback failed: ' . $e->getMessage());
                    return [];
                }
            });

            $prices = array_merge($prices, $chunkPrices);
        }
        return $prices;
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
