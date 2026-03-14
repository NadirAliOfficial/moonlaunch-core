<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\MoralisService;

class WalletController extends Controller
{
    // BSC mainnet RPC
    const BSC_RPC = 'https://bsc-dataseed.binance.org/';

    // -------------------------------------------------------
    // GET /api/wallet/balance?address=0x...
    // Returns BNB balance + token holdings for a wallet
    // Merges Moralis-indexed tokens with locally stored purchases
    // so tokens appear immediately after buying (no indexing delay).
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

            // Merge locally recorded purchases that Moralis may not have indexed yet
            $tokens = $this->mergeWithUserPurchases($address, $tokens);

            return response()->json([
                'status'        => 'success',
                'address'       => $address,
                'bnb_balance'   => $bnbBalance,
                'bnb_price_usd' => $bnbPriceUsd,
                'total_usd'     => $totalUsd,
                'tokens'        => $tokens,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Merge Moralis token list with user_purchases from DB.
     * Tokens already indexed by Moralis take precedence.
     * For purchased tokens Moralis hasn't indexed yet, we fetch
     * the on-chain balance via BSC RPC balanceOf and metadata from
     * the tokens table.
     */
    private function mergeWithUserPurchases(string $address, array $moralisTokens): array
    {
        // Build set of token addresses already returned by Moralis
        $moralisAddresses = array_map(
            fn($t) => strtolower($t['token_address'] ?? ''),
            $moralisTokens
        );
        $moralisSet = array_flip($moralisAddresses);

        // Fetch user purchases not yet in Moralis results
        $purchases = DB::table('user_purchases')
            ->where('wallet_address', $address)
            ->get(['token_address']);

        $missing = [];
        foreach ($purchases as $p) {
            $addr = strtolower($p->token_address);
            if (!isset($moralisSet[$addr])) {
                $missing[] = $addr;
            }
        }

        if (empty($missing)) {
            return $moralisTokens;
        }

        // For each missing token, get metadata from DB + on-chain balance
        $tokenRows = DB::table('tokens')
            ->whereIn('token_address', $missing)
            ->get(['token_address', 'name', 'symbol', 'logo', 'decimals'])
            ->keyBy('token_address');

        foreach ($missing as $tokenAddr) {
            $meta     = $tokenRows[$tokenAddr] ?? null;
            $decimals = (int)($meta->decimals ?? 18);
            $balance  = $this->getTokenBalance($address, $tokenAddr, $decimals);

            // Only include tokens the user actually still holds
            if ($balance === '0' || $balance === '') continue;

            $moralisTokens[] = [
                'token_address' => $tokenAddr,
                'name'          => $meta->name ?? null,
                'symbol'        => $meta->symbol ?? null,
                'logo'          => $meta->logo ?? null,
                'decimals'      => $decimals,
                'balance'       => $balance,
                'source_type'   => 'pancakeswap',
            ];
        }

        return $moralisTokens;
    }

    /**
     * Calls ERC-20 balanceOf(address) via BSC RPC eth_call.
     * Returns human-readable balance string (e.g. "12345.678900").
     */
    private function getTokenBalance(string $wallet, string $token, int $decimals): string
    {
        try {
            // balanceOf(address) selector = 0x70a08231
            $paddedWallet = str_pad(ltrim(strtolower($wallet), '0x'), 64, '0', STR_PAD_LEFT);
            $data         = '0x70a08231' . $paddedWallet;

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post(self::BSC_RPC, [
                    'jsonrpc' => '2.0',
                    'method'  => 'eth_call',
                    'params'  => [['to' => $token, 'data' => $data], 'latest'],
                    'id'      => 1,
                ]);

            if ($response->successful()) {
                $hexResult = ltrim($response->json()['result'] ?? '0x0', '0x');
                // Strip leading zeros; empty means zero balance
                $hexResult = ltrim($hexResult, '0');
                if (!$hexResult) return '0';
                // hexdec() returns float for large numbers — precision is fine for display
                $wei = hexdec($hexResult);
                if ($wei <= 0) return '0';
                if ($decimals <= 0) return (string)(int)$wei;
                $balance = $wei / pow(10, min($decimals, 18));
                if ($balance <= 0) return '0';
                return number_format($balance, min(8, $decimals), '.', '');
            }

            return '0';
        } catch (\Exception $e) {
            Log::warning("getTokenBalance({$token}) error: " . $e->getMessage());
            return '0';
        }
    }
}
