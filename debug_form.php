<?php

echo "=== DEBUG LOGIN FORM ===\n\n";

$base_url = 'http://localhost/project-story';
$login_url = $base_url . '/admin/login';

// Lấy trang login
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $login_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$login_page = curl_exec($ch);
curl_close($ch);

if (!$login_page) {
    echo "❌ Failed to get login page\n";
    exit(1);
}

echo "✅ Login page retrieved\n\n";

// Tìm form
if (preg_match('/<form[^>]*>(.*?)<\/form>/s', $login_page, $form_matches)) {
    $form_content = $form_matches[1];
    echo "=== FORM CONTENT ===\n";
    echo $form_content;
    echo "\n=== END FORM ===\n\n";
    
    // Tìm tất cả input
    if (preg_match_all('/<input[^>]*>/', $form_content, $input_matches)) {
        echo "=== ALL INPUTS ===\n";
        foreach ($input_matches[0] as $input) {
            echo $input . "\n";
        }
        echo "=== END INPUTS ===\n\n";
    }
    
    // Tìm CSRF token
    echo "=== CSRF TOKEN SEARCH ===\n";
    $csrf_patterns = [
        '/name="csrf_token" value="([^"]+)"/',
        '/name="[^"]*csrf[^"]*" value="([^"]+)"/',
        '/value="([^"]+)"[^>]*name="[^"]*csrf[^"]*"/',
        '/name="[^"]*token[^"]*" value="([^"]+)"/',
        '/name="[^"]*security[^"]*" value="([^"]+)"/',
    ];
    
    foreach ($csrf_patterns as $pattern) {
        if (preg_match($pattern, $form_content, $matches)) {
            echo "✅ Found CSRF with pattern: {$pattern}\n";
            echo "Token: " . substr($matches[1], 0, 20) . "...\n";
            break;
        }
    }
    
    // Tìm action URL
    if (preg_match('/action="([^"]+)"/', $form_content, $action_matches)) {
        echo "Form action: {$action_matches[1]}\n";
    }
    
    // Tìm method
    if (preg_match('/method="([^"]+)"/', $form_content, $method_matches)) {
        echo "Form method: {$method_matches[1]}\n";
    }
    
} else {
    echo "❌ No form found\n";
}

// Dọn dẹp
if (file_exists('cookies.txt')) {
    unlink('cookies.txt');
}

echo "\n=== DEBUG COMPLETED ===\n";



