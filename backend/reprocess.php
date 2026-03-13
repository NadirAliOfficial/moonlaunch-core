<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Only process real events, skip test ones
$logs = DB::table('webhook_logs')
    ->where('tx_hash', '!=', '0xtest123')
    ->get();

$count = 0;

foreach ($logs as $log) {
    $payload = json_decode($log->raw_payload, true);
    $logData = $payload['logs'][0] ?? null;
    if ($logData) {
        \App\Jobs\EnrichTokenJob::dispatch($logData, $payload);
        $count++;
    }
}

echo "Total dispatched: " . $count . "\n";
echo "Jobs in queue: " . DB::table('jobs')->count() . "\n";