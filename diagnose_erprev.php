<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;
use App\Services\RevService;
use Illuminate\Support\Facades\Log;

echo "=== ERPREV Integration Diagnostic Tool ===\n\n";

// 1. Check configuration
echo "1. Checking ERPREV Configuration...\n";
echo "   - Account URL: " . config('services.erprev.account_url') . "\n";
echo "   - API Key Set: " . (config('services.erprev.api_key') ? 'Yes' : 'No') . "\n";
echo "   - API Secret Set: " . (config('services.erprev.api_secret') ? 'Yes' : 'No') . "\n";
echo "   - Sync Enabled: " . (config('services.erprev.enabled') ? 'Yes' : 'No') . "\n\n";

if (!config('services.erprev.enabled')) {
    echo "   ⚠️  WARNING: ERPREV sync is DISABLED in .env file!\n";
    echo "   To enable, set ERPREV_SYNC_ENABLED=true in your .env file\n\n";
}

if (!config('services.erprev.api_key') || !config('services.erprev.api_secret')) {
    echo "   ⚠️  WARNING: ERPREV API credentials are missing!\n";
    echo "   Please set ERPREV_API_KEY and ERPREV_API_SECRET in your .env file\n\n";
}

// 2. Test ERPREV connection
echo "2. Testing ERPREV Connection...\n";
try {
    $revService = new RevService();
    $result = $revService->testConnection();
    
    if ($result['success']) {
        echo "   ✅ Connection successful!\n";
        echo "   Response: " . ($result['message'] ?? 'OK') . "\n\n";
    } else {
        echo "   ❌ Connection failed!\n";
        echo "   Error: " . $result['message'] . "\n\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Connection error!\n";
    echo "   Exception: " . $e->getMessage() . "\n\n";
}

// 3. Check for accepted books without ERPREV registration
echo "3. Checking for accepted books not registered in ERPREV...\n";
$unregisteredBooks = Book::where('status', 'accepted')
    ->whereNull('rev_book_id')
    ->with('user')
    ->get();

if ($unregisteredBooks->count() > 0) {
    echo "   Found " . $unregisteredBooks->count() . " accepted book(s) not registered in ERPREV:\n\n";
    
    foreach ($unregisteredBooks as $book) {
        echo "   - Book ID: {$book->id}\n";
        echo "     Title: {$book->title}\n";
        echo "     ISBN: {$book->isbn}\n";
        echo "     Author: {$book->user->name}\n";
        echo "     Status: {$book->status}\n";
        echo "     REV Book ID: " . ($book->rev_book_id ?? 'Not set') . "\n\n";
    }
    
    // Offer to register them
    echo "   Would you like to register these books in ERPREV now? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim(strtolower($line)) === 'yes') {
        echo "\n   Registering books...\n\n";
        
        foreach ($unregisteredBooks as $book) {
            echo "   Processing: {$book->title}...\n";
            
            try {
                $result = $revService->registerProduct($book);
                
                if ($result['success']) {
                    $book->update(['rev_book_id' => $result['product_id']]);
                    echo "   ✅ Registered successfully! Product ID: {$result['product_id']}\n\n";
                } else {
                    echo "   ❌ Registration failed: {$result['message']}\n\n";
                }
            } catch (\Exception $e) {
                echo "   ❌ Error: {$e->getMessage()}\n\n";
            }
        }
    }
} else {
    echo "   ✅ All accepted books are registered in ERPREV!\n\n";
}

// 4. Check recent sync logs
echo "4. Checking recent ERPREV sync logs...\n";
$recentLogs = \App\Models\RevSyncLog::where('area', 'products')
    ->latest()
    ->take(5)
    ->get();

if ($recentLogs->count() > 0) {
    echo "   Recent product sync logs:\n\n";
    foreach ($recentLogs as $log) {
        $status = $log->status === 'success' ? '✅' : '❌';
        echo "   {$status} [{$log->created_at}] {$log->message}\n";
    }
    echo "\n";
} else {
    echo "   No product sync logs found.\n\n";
}

// 5. Summary and recommendations
echo "=== Summary ===\n\n";

$issues = [];

if (!config('services.erprev.enabled')) {
    $issues[] = "ERPREV sync is disabled. Enable it in .env: ERPREV_SYNC_ENABLED=true";
}

if (!config('services.erprev.api_key') || !config('services.erprev.api_secret')) {
    $issues[] = "ERPREV API credentials are missing. Set them in .env file";
}

if ($unregisteredBooks->count() > 0) {
    $issues[] = "{$unregisteredBooks->count()} accepted book(s) not registered in ERPREV";
}

if (count($issues) > 0) {
    echo "⚠️  Issues found:\n";
    foreach ($issues as $i => $issue) {
        echo "   " . ($i + 1) . ". {$issue}\n";
    }
    echo "\n";
} else {
    echo "✅ Everything looks good!\n\n";
}

echo "=== Diagnostic Complete ===\n";
