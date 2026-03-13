<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\EnrichTokenJob;

class WebhookController extends Controller
{
    public function handlePairCreated(Request $request)
    {
        try {
            $payload = $request->all();

            if (empty($payload)) {
                return response()->json(['status' => 'empty payload'], 200);
            }

            $logs = $payload['logs'] ?? [];

            if (empty($logs)) {
                return response()->json(['status' => 'no logs'], 200);
            }

            foreach ($logs as $log) {
                $txHash   = $log['transactionHash'] ?? null;
                $logIndex = $log['logIndex'] ?? null;

                if (!$txHash) continue;

                // Deduplicate
                $exists = DB::table('webhook_logs')
                    ->where('tx_hash', $txHash)
                    ->where('log_index', $logIndex)
                    ->exists();

                if ($exists) continue;

                // Store raw payload immediately
                DB::table('webhook_logs')->insert([
                    'tx_hash'     => $txHash,
                    'log_index'   => $logIndex,
                    'raw_payload' => json_encode($payload),
                    'status'      => 'received',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                // Dispatch background job
                EnrichTokenJob::dispatch($log, $payload);
            }

        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
        }

        // ALWAYS return 200 to Moralis
        return response()->json(['status' => 'ok'], 200);
    }
}