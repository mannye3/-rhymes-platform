<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;
use App\Services\RevService;

$book = Book::where('status', 'accepted')->first();

if (!$book) {
    echo "No book found\n";
    exit(1);
}

echo "Testing with book: {$book->title}\n\n";

$revService = new RevService();
$result = $revService->registerProduct($book);

echo "Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
echo "Message: {$result['message']}\n";
echo "Product ID: " . ($result['product_id'] ?? 'NULL') . "\n\n";

if (isset($result['raw_response'])) {
    echo "Response:\n";
    print_r($result['raw_response']);
}
