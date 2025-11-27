<?php

require_once 'vendor/autoload.php';

// Load Laravel's bootstrap
$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Services\RevService;

// Get the RevService
$revService = new RevService();

echo "=== ERPREV Registration Debug Script ===\n\n";

// Test the connection first
echo "1. Testing ERPREV connection...\n";
$connectionResult = $revService->testConnection();
echo "   Connection success: " . ($connectionResult['success'] ? 'YES' : 'NO') . "\n";
echo "   Connection message: " . $connectionResult['message'] . "\n";
if (isset($connectionResult['status_code'])) {
    echo "   Status code: " . $connectionResult['status_code'] . "\n";
}
echo "\n";

// If connection is successful, check for books that should be registered
if ($connectionResult['success']) {
    echo "2. Checking for accepted books without ERPREV ID...\n";
    
    // Get books that are accepted but don't have a rev_book_id
    $books = Book::where('status', 'accepted')
                 ->whereNull('rev_book_id')
                 ->orderBy('updated_at', 'desc')
                 ->limit(3)
                 ->get();
    
    echo "   Found " . $books->count() . " books that need registration\n\n";
    
    foreach ($books as $book) {
        echo "3. Attempting to register book ID {$book->id}: '{$book->title}'\n";
        
        // Load the user relationship
        $book->load('user');
        
        echo "   Book details:\n";
        echo "     Title: {$book->title}\n";
        echo "     ISBN: {$book->isbn}\n";
        echo "     Price: {$book->price}\n";
        echo "     Book Type: {$book->book_type}\n";
        echo "     Genre: {$book->genre}\n";
        echo "     Author: " . ($book->user ? $book->user->name : 'N/A') . "\n";
        echo "     Description: " . substr($book->description ?? 'N/A', 0, 50) . "...\n";
        
        // Try to register the product
        echo "   Calling registerProduct...\n";
        $registrationResult = $revService->registerProduct($book);
        
        echo "   Registration result:\n";
        echo "     Success: " . ($registrationResult['success'] ? 'YES' : 'NO') . "\n";
        echo "     Message: " . $registrationResult['message'] . "\n";
        if (isset($registrationResult['product_id'])) {
            echo "     Product ID: " . $registrationResult['product_id'] . "\n";
        }
        
        echo "\n" . str_repeat("-", 50) . "\n\n";
    }
} else {
    echo "Cannot test registration due to connection failure.\n";
}

echo "=== Debug Script Complete ===\n";