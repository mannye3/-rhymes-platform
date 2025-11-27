<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== ERPREV API Direct Test ===\n\n";

$baseUrl = 'https://y301y.erprev.com/api/1.0';
$apiKey = config('services.erprev.api_key');
$apiSecret = config('services.erprev.api_secret');
$authHeader = 'Basic ' . base64_encode($apiKey . ':' . $apiSecret);

$payload = [
    'parameters' => [
        'Name' => 'Test Book Direct',
        'Barcode' => 'ISBN-TEST-DIRECT-001',
        'Category' => 'Books',
        'Description' => 'Test description',
        'Price' => 25.00,
        'Taxable' => '0',
        'Measure' => 'pcs',
    ]
];

echo "Sending request to: {$baseUrl}/register-product/json/\n";
echo "Payload:\n";
echo json_encode($payload, JSON_PRETTY_PRINT) . "\n\n";

try {
    $response = Http::withHeaders([
        'Authorization' => $authHeader,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->timeout(30)->post($baseUrl . '/register-product/json/', $payload);
    
    echo "Status Code: " . $response->status() . "\n";
    echo "Success: " . ($response->successful() ? 'YES' : 'NO') . "\n\n";
    echo "Response Body:\n";
    echo $response->body() . "\n\n";
    
    if ($response->successful()) {
        $data = $response->json();
        echo "Parsed JSON:\n";
        print_r($data);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
