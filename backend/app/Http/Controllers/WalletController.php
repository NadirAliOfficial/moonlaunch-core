<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MoralisService;

class WalletController extends Controller
{
    // -------------------------------------------------------
    // GET /api/wallet/balance?address=0x...
    // Returns BNB balance + token holdings for a wallet
    // -------------------------------------------------------
    public function getBalance(Request $request)
    {
        $request->validate([
            'address' => 'required|string|regex:/^0x[a-fA-F0-9]{40}$/',
        ]);

        $address = strtolower($request->get('address'));

        try {
            $moralis = new MoralisService();

            $bnbBalance  = $moralis->getNativeBalance($address) ?? '0';
            $tokens      = $moralis->getWalletTokens($address);
            $bnbPriceUsd = $moralis->getBnbPriceUsd();
            $totalUsd    = number_format((float)$bnbBalance * $bnbPriceUsd, 2, '.', '');

            return response()->json([
                'status'       => 'success',
                'address'      => $address,
                'bnb_balance'  => $bnbBalance,
                'bnb_price_usd' => $bnbPriceUsd,
                'total_usd'    => $totalUsd,
                'tokens'       => $tokens,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
