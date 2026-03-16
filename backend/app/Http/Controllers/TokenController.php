<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            $enriched = $tokens->map(function ($token) use ($livePrices) {
                $row  = (array) $token;
                $addr = strtolower($token->token_address);
                if (isset($livePrices[$addr])) {
                    $row['initial_price']   = $livePrices[$addr];
                    $row['price_currency']  = 'USD';
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
    // Returns single token detail                                   
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

            return response()->json([
                'status' => 'success',
                'data'   => $token,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}