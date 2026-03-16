<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\MoralisService;
use App\Services\DexScreenerService;

class TokenController extends Controller
{
    // -------------------------------------------------------
    // GET /api/new-launches
    // Returns latest token launches for mobile app
    // -------------------------------------------------------
    public function newLaunches(Request $request)
    {
        try {
            $limit  = $request->get('limit', 1000);
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

            $addresses = $tokens->pluck('token_address')->toArray();

            // 1. Try Moralis batch prices
            $moralisPrices = (new MoralisService())->getTokenPrices($addresses);

            // 2. Find tokens that Moralis couldn't price
            $unpricedAddresses = array_values(array_filter($addresses, function ($addr) use ($moralisPrices) {
                return !isset($moralisPrices[strtolower($addr)]);
            }));

            // 3. Fallback: DexScreener for unpriced tokens
            $dexPrices = [];
            if (!empty($unpricedAddresses)) {
                $dexPrices = (new DexScreenerService())->getTokenPrices($unpricedAddresses);
            }

            // 4. Merge: Moralis takes priority, DexScreener fills the gaps
            $livePrices = array_merge($dexPrices, $moralisPrices);

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
    // Returns single token detail with live price enrichment
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

            $row  = (array) $token;
            $addr = strtolower($address);

            // Try Moralis first, then DexScreener
            $prices = (new MoralisService())->getTokenPrices([$addr]);
            if (isset($prices[$addr])) {
                $row['initial_price']  = $prices[$addr];
                $row['price_currency'] = 'USD';
            } else {
                $dexPrices = (new DexScreenerService())->getTokenPrices([$addr]);
                if (isset($dexPrices[$addr])) {
                    $row['initial_price']  = $dexPrices[$addr];
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
}
