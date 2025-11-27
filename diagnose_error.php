<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RevSyncLog;
use App\Models\Book;

echo "=== ERPREV Registration Error Diagnosis ===\n\n";

// Check recent sync logs
echo "1. Recent ERPREV Sync Logs (Last 10):\n\n";
$logs = RevSyncLog::latest()->take(10)->get();

if ($logs->count() > 0) {
    foreach ($logs as $log) {
        $icon = $log->status === 'success' ? '✅' : '❌';
        echo "   {$icon} [{$log->created_at}] {$log->area}\n";
        echo "      Status: {$log->status}\n";
        echo "      Message: {$log->message}\n";
        
        if ($log->payload) {
            $payload = json_decode($log->payload, true);
            if (isset($payload['book_id'])) {
                echo "      Book ID: {$payload['book_id']}\n";
            }
            if ($log->status === 'error' && isset($payload['error'])) {
                echo "      Error Details: " . json_encode($payload['error']) . "\n";
            }
        }
        echo "\n";
    }
} else {
    echo "   No sync logs found.\n\n";
}

// Check for books accepted recently without rev_book_id
echo "2. Recently Accepted Books Without ERPREV ID:\n\n";
$recentBooks = Book::where('status', 'accepted')
    ->whereNull('rev_book_id')
    ->where('updated_at', '>', now()->subHours(1))
    ->with('user')
    ->get();

if ($recentBooks->count() > 0) {
    foreach ($recentBooks as $book) {
        echo "   ❌ Book ID: {$book->id}\n";
        echo "      Title: {$book->title}\n";
        echo "      ISBN: {$book->isbn}\n";
        echo "      Accepted: {$book->updated_at}\n";
        echo "      REV Book ID: NOT SET\n\n";
    }
} else {
    echo "   ✅ No recent books without ERPREV ID\n\n";
}

// Check ERPREV configuration
echo "3. ERPREV Configuration:\n\n";
echo "   Account URL: " . config('services.erprev.account_url') . "\n";
echo "   API Key Set: " . (config('services.erprev.api_key') ? 'Yes' : 'No') . "\n";
echo "   API Secret Set: " . (config('services.erprev.api_secret') ? 'Yes' : 'No') . "\n";
echo "   Sync Enabled: " . (config('services.erprev.enabled') ? 'Yes' : 'No') . "\n\n";

// Test connection
echo "4. Testing ERPREV Connection:\n\n";
try {
    $revService = new \App\Services\RevService();
    $result = $revService->testConnection();
    
    if ($result['success']) {
        echo "   ✅ Connection successful!\n\n";
    } else {
        echo "   ❌ Connection failed!\n";
        echo "   Error: {$result['message']}\n\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Connection error!\n";
    echo "   Exception: {$e->getMessage()}\n\n";
}

// Check if there are error logs in rev_sync_logs
echo "5. Recent Errors in Sync Logs:\n\n";
$errorLogs = RevSyncLog::where('status', 'error')
    ->where('created_at', '>', now()->subHours(24))
    ->latest()
    ->take(5)
    ->get();

if ($errorLogs->count() > 0) {
    echo "   Found {$errorLogs->count()} error(s) in last 24 hours:\n\n";
    foreach ($errorLogs as $log) {
        echo "   ❌ [{$log->created_at}] {$log->area}\n";
        echo "      Message: {$log->message}\n";
        if ($log->payload) {
            $payload = json_decode($log->payload, true);
            echo "      Payload: " . json_encode($payload, JSON_PRETTY_PRINT) . "\n";
        }
        echo "\n";
    }
} else {
    echo "   ✅ No errors in last 24 hours\n\n";
}

echo "=== Diagnosis Complete ===\n";
