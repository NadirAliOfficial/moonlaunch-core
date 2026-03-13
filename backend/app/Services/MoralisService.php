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

    // Returns BNB native balance in ether (as string)
    public function getNativeBalance(string $walletAddress): ?string
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/{$walletAddress}/balance", [
                'chain' => '0x38',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                // Moralis returns balance in wei as string
                $wei = $data['balance'] ?? '0';
                // Convert wei to BNB (divide by 10^18)
                $bnb = bcdiv($wei, bcpow('10', '18', 0), 8);
                return $bnb;
            }

            Log::warning('Moralis native balance failed: ' . $response->body());
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
                        ? bcdiv($rawBalance, bcpow('10', (string)$decimals, 0), $decimals)
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