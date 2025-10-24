<?php

echo "=== DEBUG SERVER CONNECTION ===\n\n";

$base_url = 'http://localhost/project-story';
$login_url = $base_url . '/admin/login';

echo "Testing connection to: {$login_url}\n\n";

// Test 1: Basic connection
echo "1. Testing basic connection...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $login_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ cURL Error: {$error}\n";
} else {
    echo "✅ HTTP Code: {$http_code}\n";
    
    if ($http_code == 200) {
        echo "✅ Server is responding\n";
        
        // Test 2: Check if it's FuelPHP
        if (strpos($response, 'FuelPHP') !== false || strpos($response, 'Fuel') !== false) {
            echo "✅ FuelPHP detected\n";
        } else {
            echo "⚠️ FuelPHP not detected in response\n";
        }
        
        // Test 3: Check for login form
        if (strpos($response, 'username') !== false && strpos($response, 'password') !== false) {
            echo "✅ Login form detected\n";
        } else {
            echo "❌ Login form not found\n";
        }
        
        // Test 4: Check for CSRF token
        if (preg_match('/name="csrf_token" value="([^"]+)"/', $response, $matches)) {
            echo "✅ CSRF token found: " . substr($matches[1], 0, 20) . "...\n";
        } else {
            echo "❌ CSRF token not found\n";
            
            // Debug: Show part of response
            echo "\n=== RESPONSE PREVIEW ===\n";
            $preview = substr($response, 0, 1000);
            echo $preview;
            if (strlen($response) > 1000) {
                echo "\n... (truncated)\n";
            }
            echo "\n=== END PREVIEW ===\n";
        }
        
    } else {
        echo "❌ Server not responding correctly (HTTP: {$http_code})\n";
    }
}

// Test 2: Check if XAMPP is running
echo "\n2. Testing XAMPP status...\n";
$test_urls = [
    'http://localhost/',
    'http://localhost/project-story/',
    'http://localhost/project-story/admin/',
];

foreach ($test_urls as $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "{$url} -> HTTP {$http_code}\n";
}

echo "\n=== DEBUG COMPLETED ===\n";


