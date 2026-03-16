<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DexScreenerService
{
    protected string $baseUrl = 'https://api.dexscreener.com/tokens/v1/bsc';

    /**
     * Fetch USD prices for an array of BSC token addresses using DexScreener.
     * Queries in batches of 30 (API limit). Results cached for 2 minutes.
     *
     * Returns: [ lowercased_token_address => formatted_price_string ]
     */
    public function getTokenPrices(array $addresses): array
    {
        if (empty($addresses)) return [];

        $cacheKey = 'dexscreener_prices_' . md5(implode(',', array_map('strtolower', $addresses)));

        return Cache::remember($cacheKey, 120, function () use ($addresses) {
            $prices  = [];
            $batches = array_chunk($addresses, 30);

            foreach ($batches as $batch) {
                try {
                    $joined   = implode(',', array_map('strtolower', $batch));
                    $response = Http::timeout(10)->get("{$this->baseUrl}/{$joined}");

                    if (!$response->successful()) {
                        Log::warning('DexScreener batch failed: ' . $response->body());
                        continue;
                    }

                    $pairs = $response->json() ?? [];

                    // Each token may have multiple pairs — pick the one with highest liquidity
                    $best = [];
                    foreach ($pairs as $pair) {
                        $addr     = strtolower($pair['baseToken']['address'] ?? '');
                        $usdPrice = (float)($pair['priceUsd'] ?? 0);
                        $liq      = (float)($pair['liquidity']['usd'] ?? 0);

                        if ($addr && $usdPrice > 0) {
                            if (!isset($best[$addr]) || $liq > ($best[$addr]['liq'] ?? 0)) {
                                $best[$addr] = ['price' => $usdPrice, 'liq' => $liq];
                            }
                        }
                    }

                    foreach ($best as $addr => $data) {
                        $prices[$addr] = $this->formatPrice($data['price']);
                    }

                } catch (\Exception $e) {
                    Log::error('DexScreener error: ' . $e->getMessage());
                }
            }

            return $prices;
        });
    }

    private function formatPrice(float $price): string
    {
        if ($price <= 0)        return '0';
        if ($price < 0.000001) return number_format($price, 10, '.', '');
        if ($price < 0.0001)   return number_format($price, 8,  '.', '');
        if ($price < 0.01)     return number_format($price, 6,  '.', '');
        if ($price < 1)        return number_format($price, 4,  '.', '');
        return number_format($price, 2, '.', '');
    }
}
