<?php
// Simple script to check authentication header construction

// From your .env file:
$apiKey = '6b6c44f8-456f-4952-9ada-fcbbf763b5da';
$apiSecret = '38cb680916b312b3cb7001b2f83a152d7820e9d1';

echo "API Key: $apiKey\n";
echo "API Secret: $apiSecret\n\n";

// Create authorization header
$credentials = base64_encode($apiKey . ':' . $apiSecret);
$authHeader = 'Basic ' . $credentials;

echo "Credentials String: $apiKey:$apiSecret\n";
echo "Base64 Encoded: $credentials\n";
echo "Auth Header: $authHeader\n\n";

// Let's also check what the decoded credentials look like
$decoded = base64_decode($credentials);
echo "Decoded Credentials: $decoded\n";

// Check if they match
list($decodedKey, $decodedSecret) = explode(':', $decoded, 2);
echo "Decoded API Key: $decodedKey\n";
echo "Decoded API Secret: $decodedSecret\n";
echo "Keys Match: " . ($apiKey === $decodedKey ? 'YES' : 'NO') . "\n";
echo "Secrets Match: " . ($apiSecret === $decodedSecret ? 'YES' : 'NO') . "\n";