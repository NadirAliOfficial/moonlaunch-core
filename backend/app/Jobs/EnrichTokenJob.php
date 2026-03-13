<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\MoralisService;

class EnrichTokenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;                  

    protected array $log;
    protected array $payload;

    public int $tries = 3;

    public function __construct(array $log, array $payload)
    {
        $this->log     = $log;
        $this->payload = $payload;
    }

    public function handle(): void
    {
        $wbnb = '0xbb4cdb9cbd36b01bd1cbaebf2de08d9173bc095c';

        try {
            $log = $this->log;

            $txHash    = $log['transactionHash'] ?? null;
            $logIndex  = $log['logIndex'] ?? null;
            $blockNum  = $log['blockNumber'] ?? null;
            $timestamp = $log['blockTimestamp'] ?? null;

            // Moralis sends topic1/topic2 as separate keys NOT as an array
            $token0 = isset($log['topic1'])
                ? '0x' . substr($log['topic1'], 26)
                : null;

            $token1 = isset($log['topic2'])
                ? '0x' . substr($log['topic2'], 26)
                : null;

            if (!$token0 || !$token1) {
                $this->markLog($txHash, $logIndex, 'skipped', 'Missing token0 or token1');
                return;
            }

            // Filter ONLY WBNB pairs
            $token0Lower = strtolower($token0);
            $token1Lower = strtolower($token1);
            $wbnbLower   = strtolower($wbnb);

            if ($token0Lower !== $wbnbLower && $token1Lower !== $wbnbLower) {
                $this->markLog($txHash, $logIndex, 'skipped', 'Not a WBNB pair');
                return;
            }

            // Determine the NEW token (non-WBNB side)
            $newToken = $token0Lower === $wbnbLower ? $token1 : $token0;

            // Extract LP Pair Address from log data
            $data        = $log['data'] ?? '';
            $pairAddress = '0x' . substr($data, 26, 40);

            // Avoid duplicate tokens
            $tokenExists = DB::table('tokens')
                ->where('token_address', strtolower($newToken))
                ->exists();

            if ($tokenExists) {
                $this->markLog($txHash, $logIndex, 'skipped', 'Token already exists');
                return;
            }

            // Fetch Metadata + Price from Moralis
            $moralis  = new MoralisService();
            $metadata = $moralis->getTokenMetadata($newToken);
            $price    = $moralis->getTokenPrice($newToken);

            // Store token in DB
            DB::table('tokens')->insert([
                'chain_id'       => '56',
                'token_address'  => strtolower($newToken),
                'pair_address'   => strtolower($pairAddress),
                'tx_hash'        => $txHash,
                'log_index'      => $logIndex,
                'block_number'   => $blockNum,
                'detected_at'    => $timestamp ? date('Y-m-d H:i:s', $timestamp) : now(),
                'name'           => $metadata['name'] ?? null,
                'symbol'         => $metadata['symbol'] ?? null,
                'decimals'       => $metadata['decimals'] ?? null,
                'logo'           => $metadata['logo'] ?? null,
                'initial_price'  => $price,
                'price_currency' => 'USD',
                'source_type'    => 'pancakeswap',
                'trade_mode'     => 'router_swap',
                'status'         => 'active',
                'is_tradeable'   => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            $this->markLog($txHash, $logIndex, 'processed', null);
            Log::info("Token enriched: {$newToken}");

        } catch (\Exception $e) {
            Log::error('EnrichTokenJob failed: ' . $e->getMessage());
            $this->markLog(
                $this->log['transactionHash'] ?? 'unknown',
                $this->log['logIndex'] ?? null,
                'failed',
                $e->getMessage()
            );
            throw $e;
        }
    }

    private function markLog(
        string $txHash,
        ?string $logIndex,
        string $status,
        ?string $reason
    ): void {
        DB::table('webhook_logs')
            ->where('tx_hash', $txHash)
            ->where('log_index', $logIndex)
            ->update([
                'status'      => $status,
                'skip_reason' => $reason,
                'updated_at'  => now(),
            ]);
    }
}