<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Aws\SecretsManager\SecretsManagerClient;

class MoralisService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://deep-index.moralis.io/api/v2.2';

    public function __construct()
    {
        $this->apiKey = $this->getApiKey();
    }

    private function getApiKey(): string
    {
        return Cache::remember('moralis_api_key', 3600, function () {
            try {
                $client = new SecretsManagerClient([
                    'version' => 'latest',
                    'region'  => 'eu-north-1', // Fixed: was us-east-1
                ]);

                $result = $client->getSecretValue([
                    'SecretId' => 'moralis/api-key',
                ]);

                $secret = json_decode($result['SecretString'], true);

                // Try multiple possible key names              
                return $secret['api_key']
                    ?? $secret['MORALIS_API_KEY']
                    ?? $secret['moralis_api_key']
                    ?? $result['SecretString'];

            } catch (\Exception $e) {
                Log::error('Failed to fetch Moralis API key: ' . $e->getMessage());
                throw $e;
            }
        });                 
    }

    public function getTokenMetadata(string $tokenAddress): ?array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/erc20/metadata", [
                'chain'     => '0x38',
                'addresses' => [$tokenAddress],
            ]);

            if ($response->successful()) {                
                $data  = $response->json();
                $token = $data[0] ?? null;

                if (!$token) return null;

                return [
                    'name'     => $token['name'] ?? null,
                    'symbol'   => $token['symbol'] ?? null,
                    'decimals' => $token['decimals'] ?? null,
                    'logo'     => $token['logo'] ?? null,
                ];
            }

            Log::warning('Moralis metadata failed: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('getTokenMetadata error: ' . $e->getMessage());
            return null;
        }                         
    }                                              

    public function getTokenPrice(string $tokenAddress): ?string
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/erc20/{$tokenAddress}/price", [
                'chain' => '0x38',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['usdPrice'] ?? null;
            }

            Log::warning('Moralis price failed: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('getTokenPrice error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Batch price lookup for multiple token addresses.
     * Returns associative array: lowercased_token_address => formatted_usd_price_string
     * Results cached 2 minutes per unique set of addresses.
     */
    public function getTokenPrices(array $addresses): array
    {
        if (empty($addresses)) return [];

        $cacheKey = 'token_prices_' . md5(implode(',', array_map('strtolower', $addresses)));

        return Cache::remember($cacheKey, 120, function () use ($addresses) {
            try {
                $tokens = array_map(fn($addr) => ['token_address' => strtolower($addr)], $addresses);

                $response = Http::withHeaders([
                    'X-API-Key' => $this->apiKey,
                ])->post("{$this->baseUrl}/erc20/prices?chain=0x38", [
                    'tokens' => $tokens,
                ]);

                if ($response->successful()) {
                    $prices = [];
                    foreach ($response->json() as $item) {
                        $addr     = strtolower($item['tokenAddress'] ?? '');
                        $usdPrice = $item['usdPrice'] ?? null;
                        if ($addr && $usdPrice !== null && $usdPrice > 0) {
                            $prices[$addr] = $this->formatPrice((float)$usdPrice);
                        }
                    }
                    return $prices;
                }

                Log::warning('Moralis batch prices failed: ' . $response->body());
                return [];

            } catch (\Exception $e) {
                Log::error('getTokenPrices error: ' . $e->getMessage());
                return [];
            }
        });
    }

    private function formatPrice(float $price): string
    {
        if ($price <= 0) return '0';
        if ($price < 0.000001) return number_format($price, 10, '.', '');
        if ($price < 0.0001)   return number_format($price, 8, '.', '');
        if ($price < 0.01)     return number_format($price, 6, '.', '');
        if ($price < 1)        return number_format($price, 4, '.', '');
        return number_format($price, 2, '.', '');
    }

    // Returns current BNB price in USD using Binance public API (cached 60s)
    public function getBnbPriceUsd(): float
    {
        return Cache::remember('bnb_price_usd', 60, function () {
            try {
                $response = Http::timeout(5)
                    ->get('https://api.binance.com/api/v3/ticker/price', [
                        'symbol' => 'BNBUSDT',
                    ]);
                if ($response->successful()) {
                    return (float)($response->json()['price'] ?? 0);
                }
            } catch (\Exception $e) {
                Log::warning('BNB price fetch failed: ' . $e->getMessage());
            }
            return 0.0;
        });
    }

    // Returns BNB native balance in ether (as string) — uses BSC RPC directly
    public function getNativeBalance(string $walletAddress): ?string
    {
        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post('https://bsc-dataseed.binance.org/', [
                    'jsonrpc' => '2.0',
                    'method'  => 'eth_getBalance',
                    'params'  => [$walletAddress, 'latest'],
                    'id'      => 1,
                ]);

            if ($response->successful()) {
                $hex = $response->json()['result'] ?? '0x0';
                $wei = hexdec(ltrim($hex, '0x') ?: '0');
                return number_format($wei / 1e18, 8, '.', '');
            }

            return null;
        } catch (\Exception $e) {
            Log::error('getNativeBalance error: ' . $e->getMessage());
            return null;
        }
    }

    // Returns ERC20 token holdings for a wallet
    public function getWalletTokens(string $walletAddress): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/{$walletAddress}/erc20", [
                'chain' => '0x38',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $tokens = $data['result'] ?? $data ?? [];

                return array_map(function ($t) {
                    $decimals = (int)($t['decimals'] ?? 18);
                    $rawBalance = $t['balance'] ?? '0';
                    $balance = $decimals > 0
                        ? number_format((float)$rawBalance / pow(10, $decimals), min($decimals, 8), '.', '')
                        : $rawBalance;

                    return [
                        'token_address' => strtolower($t['token_address'] ?? ''),
                        'name'          => $t['name'] ?? null,
                        'symbol'        => $t['symbol'] ?? null,
                        'logo'          => $t['logo'] ?? null,
                        'decimals'      => $decimals,
                        'balance'       => $balance,
                        'source_type'   => 'pancakeswap',
                    ];
                }, is_array($tokens) ? $tokens : []);
            }

            Log::warning('Moralis wallet tokens failed: ' . $response->body());
            return [];

        } catch (\Exception $e) {
            Log::error('getWalletTokens error: ' . $e->getMessage());
            return [];
        }
    }
}