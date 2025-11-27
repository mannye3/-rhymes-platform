<?php
// Direct ERPREV API test script with detailed response analysis

require_once 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Get configuration from Laravel config
$accountUrl = config('services.erprev.account_url');
$apiKey = config('services.erprev.api_key');
$apiSecret = config('services.erprev.api_secret');
$enabled = config('services.erprev.enabled', false);

echo "=== ERPREV API Response Structure Analysis ===\n\n";

if (empty($accountUrl) || empty($apiKey) || empty($apiSecret)) {
    echo "ERROR: Missing required configuration values!\n";
    exit(1);
}

// Construct base URL
$cleanAccountUrl = preg_replace('#^https?://#', '', $accountUrl);
$baseUrl = "https://{$cleanAccountUrl}/api/1.0";

// Create authorization header
$credentials = base64_encode($apiKey . ':' . $apiSecret);
$authHeader = 'Basic ' . $credentials;

echo "Testing product retrieval with limit 1 to analyze response structure...\n";

try {
    $response = Http::withHeaders([
        'Authorization' => $authHeader,
        'Content-Type' => 'application/json',
    ])->timeout(30)->post($baseUrl . '/get-products-list/json', ['limit' => 1]);
    
    echo "Response Status: " . $response->status() . "\n";
    echo "Successful: " . ($response->successful() ? 'Yes' : 'No') . "\n";
    
    $body = $response->body();
    echo "Response Body Length: " . strlen($body) . " characters\n";
    
    // Parse JSON
    $data = json_decode($body, true);
    
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "JSON Parsed Successfully!\n";
        echo "Top-level keys: " . implode(', ', array_keys($data)) . "\n";
        
        // Analyze structure
        if (isset($data['status'])) {
            echo "Status: " . $data['status'] . "\n";
        }
        
        if (isset($data['records'])) {
            echo "Records found: " . count($data['records']) . "\n";
            if (count($data['records']) > 0) {
                echo "First record keys: " . implode(', ', array_keys($data['records'][0])) . "\n";
                echo "Sample data:\n";
                foreach (array_slice($data['records'][0], 0, 5) as $key => $value) {
                    echo "  $key: " . (is_string($value) ? substr($value, 0, 50) . (strlen($value) > 50 ? '...' : '') : gettype($value)) . "\n";
                }
            }
        }
        
        if (isset($data['pagenation'])) {  // Note: ERPREV uses "pagenation" not "pagination"
            echo "Pagination info:\n";
            foreach ($data['pagenation'] as $key => $value) {
                echo "  $key: $value\n";
            }
        }
        
    } else {
        echo "JSON Parse Error: " . json_last_error_msg() . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";