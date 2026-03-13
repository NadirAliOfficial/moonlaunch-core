<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TradingService;

class TradeController extends Controller
{
    // -------------------------------------------------------
    // POST /api/trade/buy
    // Body: { wallet_address, token_address, bnb_amount_wei, slippage_bps? }
    // bnb_amount_wei: decimal string, e.g. "50000000000000000" (0.05 BNB)
    // -------------------------------------------------------
    public function buy(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string|regex:/^0x[a-fA-F0-9]{40}$/',
            'token_address'  => 'required|string|regex:/^0x[a-fA-F0-9]{40}$/',
            'bnb_amount_wei' => 'required|string|regex:/^[0-9]+$/',
            'slippage_bps'   => 'nullable|integer|min:10|max:3000',
        ]);

        if ((int)$request->bnb_amount_wei < 10000000000000000) {
            return response()->json(['message' => 'Minimum buy is 0.01 BNB'], 422);
        }

        $user = DB::table('users')
            ->whereRaw('LOWER(wallet_address) = ?', [strtolower($request->wallet_address)])
            ->first(['turnkey_suborg_id', 'turnkey_suborg_key', 'wallet_address']);

        if (!$user || !$user->turnkey_suborg_id) {
            return response()->json(['message' => 'Wallet not registered'], 404);
        }

        if (!$user->turnkey_suborg_key) {
            return response()->json(['message' => 'Wallet signing key not found — please re-create your account'], 422);
        }

        try {
            $service = new TradingService();
            $txHash  = $service->buy(
                walletAddress: $user->wallet_address,
                tokenAddress:  strtolower($request->token_address),
                bnbAmountWei:  $request->bnb_amount_wei,
                subOrgId:      $user->turnkey_suborg_id,
                subOrgKey:     $user->turnkey_suborg_key,
                slippageBps:   (int)($request->slippage_bps ?? 500),
            );

            return response()->json([
                'status'   => 'success',
                'tx_hash'  => $txHash,
                'explorer' => 'https://bscscan.com/tx/' . $txHash,
            ]);

        } catch (\Exception $e) {
            Log::error('[TradeController/buy] ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // -------------------------------------------------------
    // POST /api/wallet/send
    // Body: { wallet_address, to_address, amount_wei, token_address? }
    // If token_address is omitted → send native BNB
    // amount_wei: decimal string representing smallest unit
    // -------------------------------------------------------
    public function send(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string|regex:/^0x[a-fA-F0-9]{40}$/',
            'to_address'     => 'required|string|regex:/^0x[a-fA-F0-9]{40}$/',
            'amount_wei'     => 'required|string|regex:/^[0-9]+$/',
            'token_address'  => 'nullable|string|regex:/^0x[a-fA-F0-9]{40}$/',
        ]);

        $user = DB::table('users')
            ->whereRaw('LOWER(wallet_address) = ?', [strtolower($request->wallet_address)])
            ->first(['turnkey_suborg_id', 'turnkey_suborg_key', 'wallet_address']);

        if (!$user || !$user->turnkey_suborg_id) {
            return response()->json(['message' => 'Wallet not registered'], 404);
        }

        if (!$user->turnkey_suborg_key) {
            return response()->json(['message' => 'Wallet signing key not found — please re-create your account'], 422);
        }

        try {
            $service = new TradingService();

            if ($request->filled('token_address')) {
                $txHash = $service->sendToken(
                    from:         $user->wallet_address,
                    tokenAddress: strtolower($request->token_address),
                    toAddress:    strtolower($request->to_address),
                    amountWei:    $request->amount_wei,
                    subOrgId:     $user->turnkey_suborg_id,
                    subOrgKey:    $user->turnkey_suborg_key,
                );
            } else {
                $txHash = $service->sendNative(
                    from:      $user->wallet_address,
                    to:        strtolower($request->to_address),
                    amountWei: $request->amount_wei,
                    subOrgId:  $user->turnkey_suborg_id,
                    subOrgKey: $user->turnkey_suborg_key,
                );
            }

            return response()->json([
                'status'   => 'success',
                'tx_hash'  => $txHash,
                'explorer' => 'https://bscscan.com/tx/' . $txHash,
            ]);

        } catch (\Exception $e) {
            Log::error('[TradeController/send] ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
