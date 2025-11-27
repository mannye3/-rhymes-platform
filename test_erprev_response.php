<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;
use App\Services\RevService;

echo "=== ERPREV Response Structure Test ===\n\n";

// Find a book to test with
$book = Book::where('status', 'accepted')->first();

if (!$book) {
    echo "❌ No accepted book found to test with.\n";
    echo "Please approve a book first, then run this script.\n";
    exit(1);
}

echo "📚 Testing with book:\n";
echo "   ID: {$book->id}\n";
echo "   Title: {$book->title}\n";
echo "   ISBN: {$book->isbn}\n\n";

echo "🔄 Attempting to register in ERPREV...\n\n";

$revService = new RevService();
$result = $revService->registerProduct($book);

echo "📊 Registration Result:\n";
echo "   Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
echo "   Message: {$result['message']}\n";
echo "   Product ID: " . ($result['product_id'] ?? 'NOT SET') . "\n\n";

if (isset($result['raw_response'])) {
    echo "📝 Raw ERPREV Response:\n";
    echo json_encode($result['raw_response'], JSON_PRETTY_PRINT) . "\n\n";
}

echo "💡 Check the logs for detailed information:\n";
echo "   Look in storage/logs/laravel.log for:\n";
echo "   - 'ERPREV registerProduct - Response received'\n";
echo "   - 'ERPREV registerProduct - Parsed response'\n\n";

echo "=== Test Complete ===\n";
