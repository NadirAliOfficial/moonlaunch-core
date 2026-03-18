<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\MoralisService;

class UpdateTokenPrices extends Command
{
    protected $signature   = 'tokens:update-prices';
    protected $description = 'Fetch live prices for all tokens from Moralis + DexScreener and store in DB';

    public function handle(): void
    {
        $addresses = DB::table('tokens')->pluck('token_address')->toArray();

        if (empty($addresses)) {
            $this->info('No tokens found.');
            return;
        }

        $this->info('Fetching prices for ' . count($addresses) . ' tokens...');

        // Step 1: Moralis batch (handles up to 25 per call)
        $moralisPrices = [];
        try {
            $moralisPrices = (new MoralisService())->getTokenPrices($addresses);
        } catch (\Exception $e) {
            Log::warning('Moralis price update failed: ' . $e->getMessage());
        }

        // Step 2: DexScreener for tokens Moralis missed
        $missing = array_values(array_filter($addresses, fn($a) => !isset($moralisPrices[strtolower($a)])));
        $dexPrices = [];
        if (!empty($missing)) {
            $dexPrices = $this->getDexScreenerPrices($missing);
        }

        $allPrices = array_merge($moralisPrices, $dexPrices);

        // Step 3: Update DB
        $updated = 0;
        foreach ($allPrices as $addr => $price) {
            DB::table('tokens')
                ->where('token_address', strtolower($addr))
                ->update([
                    'current_price'    => $price,
                    'price_updated_at' => now(),
                ]);
            $updated++;
        }

        $this->info("Updated {$updated} / " . count($addresses) . " tokens.");
    }

    private function getDexScreenerPrices(array $addresses): array
    {
        $prices = [];
        foreach (array_chunk($addresses, 30) as $chunk) {
            try {
                $response = Http::timeout(8)
                    ->get('https://api.dexscreener.com/latest/dex/tokens/' . implode(',', $chunk));

                if (!$response->successful()) continue;

                foreach ($response->json()['pairs'] ?? [] as $pair) {
                    $addr  = strtolower($pair['baseToken']['address'] ?? '');
                    $price = (float)($pair['priceUsd'] ?? 0);
                    if ($addr && $price > 0 && !isset($prices[$addr])) {
                        $prices[$addr] = $this->formatPrice($price);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('DexScreener update failed: ' . $e->getMessage());
            }
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
