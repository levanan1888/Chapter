<?php

/**
 * Script tự động test đăng nhập admin cho đến khi thành công
 */

echo "=== AUTO TEST ADMIN LOGIN ===\n\n";

// Cấu hình
$base_url = 'http://localhost/project-story';
$login_url = $base_url . '/admin/login';
$dashboard_url = $base_url . '/admin/dashboard';
$max_attempts = 10;
$delay_between_attempts = 2; // seconds

$credentials = [
    'username' => 'admin@example.com',
    'password' => 'admin123'
];

echo "Target URL: {$login_url}\n";
echo "Dashboard URL: {$dashboard_url}\n";
echo "Max attempts: {$max_attempts}\n";
echo "Delay between attempts: {$delay_between_attempts}s\n\n";

for ($attempt = 1; $attempt <= $max_attempts; $attempt++) {
    echo "=== ATTEMPT {$attempt}/{$max_attempts} ===\n";
    
    try {
        // Bước 1: Lấy trang login để lấy CSRF token
        echo "1. Getting login page...\n";
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
            continue;
        }
        
        // Bước 2: Tìm CSRF token từ form
        echo "2. Extracting CSRF token from form...\n";
        $csrf_token = '';
        
        // Tìm CSRF token trong form (FuelPHP sử dụng tên khác)
        if (preg_match('/name="[^"]*csrf[^"]*" value="([^"]+)"/', $login_page, $matches)) {
            $csrf_token = $matches[1];
            echo "✅ CSRF token found: " . substr($csrf_token, 0, 20) . "...\n";
        } else {
            echo "❌ CSRF token not found in form\n";
            echo "Debug: Looking for CSRF patterns...\n";
            
            // Debug: Tìm tất cả input hidden
            if (preg_match_all('/<input[^>]*type="hidden"[^>]*>/', $login_page, $hidden_inputs)) {
                echo "Found hidden inputs:\n";
                foreach ($hidden_inputs[0] as $input) {
                    echo "  " . $input . "\n";
                }
            }
            continue;
        }
        
        // Bước 3: Gửi form đăng nhập
        echo "3. Submitting login form...\n";
        $post_data = http_build_query([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'csrf_token' => $csrf_token
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $login_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $redirect_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        curl_close($ch);
        
        echo "HTTP Code: {$http_code}\n";
        if ($redirect_url) {
            echo "Redirect URL: {$redirect_url}\n";
        }
        
        // Bước 4: Kiểm tra kết quả
        if ($http_code == 302 && strpos($redirect_url, 'dashboard') !== false) {
            echo "✅ LOGIN SUCCESS! Redirected to dashboard\n";
            
            // Bước 5: Test truy cập dashboard
            echo "4. Testing dashboard access...\n";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $dashboard_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
            curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $dashboard_content = curl_exec($ch);
            $dashboard_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($dashboard_http_code == 200 && strpos($dashboard_content, 'Dashboard') !== false) {
                echo "✅ DASHBOARD ACCESS SUCCESS!\n";
                echo "🎉 ADMIN LOGIN WORKING PERFECTLY!\n";
                
                // Kiểm tra log
                echo "\n=== CHECKING LOG ===\n";
                if (file_exists('fuel/app/logs/2025/10/24.php')) {
                    $log_content = file_get_contents('fuel/app/logs/2025/10/24.php');
                    if (!empty(trim($log_content))) {
                        echo "✅ Log file has content:\n";
                        echo "---\n";
                        echo $log_content;
                        echo "\n---\n";
                    } else {
                        echo "⚠️ Log file is empty\n";
                    }
                } else {
                    echo "⚠️ Log file not found\n";
                }
                
                // Dọn dẹp
                if (file_exists('cookies.txt')) {
                    unlink('cookies.txt');
                }
                
                echo "\n🎉 TEST COMPLETED SUCCESSFULLY!\n";
                exit(0);
            } else {
                echo "❌ Dashboard access failed (HTTP: {$dashboard_http_code})\n";
            }
        } else {
            echo "❌ Login failed (HTTP: {$http_code})\n";
            
            // Kiểm tra nội dung response để debug
            if (strpos($response, 'Tên đăng nhập hoặc mật khẩu không đúng') !== false) {
                echo "❌ Error: Wrong username/password\n";
            } elseif (strpos($response, 'không phải admin') !== false) {
                echo "❌ Error: Not admin user\n";
            } elseif (strpos($response, 'Token không hợp lệ') !== false) {
                echo "❌ Error: Invalid CSRF token\n";
            } else {
                echo "❌ Unknown error\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "\n";
    }
    
    if ($attempt < $max_attempts) {
        echo "⏳ Waiting {$delay_between_attempts} seconds before next attempt...\n";
        sleep($delay_between_attempts);
    }
    
    echo "\n";
}

echo "❌ ALL ATTEMPTS FAILED!\n";
echo "Please check the following:\n";
echo "1. Server is running on localhost\n";
echo "2. Database connection is working\n";
echo "3. Admin credentials are correct\n";
echo "4. No server errors in logs\n";

// Dọn dẹp
if (file_exists('cookies.txt')) {
    unlink('cookies.txt');
}

echo "\n=== TEST COMPLETED ===\n";
