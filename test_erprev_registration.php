<?php

require_once 'vendor/autoload.php';

// Load Laravel's bootstrap
$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the RevService
$revService = new \App\Services\RevService();

// Test the connection first
echo "Testing ERPREV connection...\n";
$connectionResult = $revService->testConnection();
echo "Connection result: " . json_encode($connectionResult, JSON_PRETTY_PRINT) . "\n\n";

// If connection is successful, try to register a test product
if ($connectionResult['success']) {
    echo "Connection successful. Testing product registration...\n";
    
    // Create a test book object
    $book = new \stdClass();
    $book->id = 999999;
    $book->title = "Test Book for ERPREV Registration";
    $book->isbn = "978-0-123456-78-9";
    $book->description = "This is a test book for checking ERPREV registration";
    $book->price = 29.99;
    $book->book_type = "paperback";
    $book->genre = "Fiction";
    
    // Create a mock user object
    $user = new \stdClass();
    $user->name = "Test Author";
    $book->user = $user;
    
    // Try to register the product
    $registrationResult = $revService->registerProduct($book);
    echo "Registration result: " . json_encode($registrationResult, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Connection failed. Cannot test registration.\n";
}