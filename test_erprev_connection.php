<?php
// Simple script to test ERPREV connection

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

// Get configuration
$accountUrl = config('services.erprev.account_url');
$apiKey = config('services.erprev.api_key');
$apiSecret = config('services.erprev.api_secret');
$enabled = config('services.erprev.enabled', false);

echo "ERPREV Configuration:\n";
echo "Account URL: $accountUrl\n";
echo "API Key: " . substr($apiKey, 0, 10) . "...\n";
echo "API Secret: " . substr($apiSecret, 0, 10) . "...\n";
echo "Enabled: " . ($enabled ? 'Yes' : 'No') . "\n\n";

if (!$enabled) {
    echo "ERPREV sync is disabled!\n";
    exit(1);
}

// Construct base URL
$baseUrl = "https://{$accountUrl}/api/1.0";
echo "Base URL: $baseUrl\n\n";

// Create authorization header
$credentials = base64_encode($apiKey . ':' . $apiSecret);
$authHeader = 'Basic ' . $credentials;
echo "Authorization header: " . substr($authHeader, 0, 20) . "...\n\n";

// Test connection with enhanced debugging
echo "Testing connection to ERPREV...\n";
try {
    $response = Http::withHeaders([
        'Authorization' => $authHeader,
        'Content-Type' => 'application/json',
    ])->timeout(60)->post($baseUrl . '/about/json', []);
    
    echo "Response Status: " . $response->status() . "\n";
    echo "Successful: " . ($response->successful() ? 'Yes' : 'No') . "\n";
    echo "Response Headers:\n";
    foreach ($response->headers() as $header => $values) {
        echo "  $header: " . implode(', ', $values) . "\n";
    }
    
    $body = $response->body();
    echo "Response Body: " . substr($body, 0, 500) . (strlen($body) > 500 ? '...' : '') . "\n\n";
    
    // Try to decode JSON
    $decoded = json_decode($body, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "JSON Decoded Successfully:\n";
        echo "  Status: " . ($decoded['status'] ?? 'N/A') . "\n";
        echo "  Error: " . ($decoded['error'] ?? 'N/A') . "\n";
    } else {
        echo "JSON Decode Error: " . json_last_error_msg() . "\n";
        echo "Raw response appears to be: " . (is_numeric(strpos($body, '<html')) ? 'HTML' : 'Plain text') . "\n";
    }
    
    if ($response->successful() && $decoded && isset($decoded['status']) && $decoded['status'] === '1') {
        echo "✓ Connection successful!\n";
    } else {
        echo "✗ Connection failed!\n";
        if ($decoded && isset($decoded['error'])) {
            echo "  Error: " . $decoded['error'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Connection error: " . $e->getMessage() . "\n";
}

// Test getting products with a simple filter
echo "\nTesting product retrieval...\n";
try {
    $response = Http::withHeaders([
        'Authorization' => $authHeader,
        'Content-Type' => 'application/json',
    ])->timeout(60)->post($baseUrl . '/get-products-list/json', [
        'limit' => 1
    ]);
    
    echo "Response Status: " . $response->status() . "\n";
    echo "Successful: " . ($response->successful() ? 'Yes' : 'No') . "\n";
    echo "Body Length: " . strlen($response->body()) . " bytes\n";
    
    if ($response->successful()) {
        $body = $response->body();
        $data = json_decode($body, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "Found " . count($data['records'] ?? []) . " records\n";
            echo "✓ Product retrieval successful!\n";
        } else {
            echo "✗ JSON decode error: " . json_last_error_msg() . "\n";
            echo "Body sample: " . substr($body, 0, 200) . "...\n";
        }
    } else {
        echo "✗ Product retrieval failed!\n";
        echo "Error: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "✗ Product retrieval error: " . $e->getMessage() . "\n";
}