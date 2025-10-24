<?php

echo "=== KIỂM TRA LOG ADMIN LOGIN ===\n\n";

$log_file = 'fuel/app/logs/2025/10/24.php';

if (file_exists($log_file)) {
    $content = file_get_contents($log_file);
    
    if (empty(trim($content))) {
        echo "❌ Log file trống - chưa có hoạt động nào\n";
    } else {
        echo "✅ Log file có nội dung:\n";
        echo "---\n";
        echo $content;
        echo "\n---\n";
        
        // Phân tích log
        $lines = explode("\n", $content);
        $debug_count = 0;
        $error_count = 0;
        
        foreach ($lines as $line) {
            if (strpos($line, 'DEBUG') !== false) {
                $debug_count++;
            }
            if (strpos($line, 'ERROR') !== false) {
                $error_count++;
            }
        }
        
        echo "\n=== THỐNG KÊ ===\n";
        echo "Debug messages: {$debug_count}\n";
        echo "Error messages: {$error_count}\n";
        
        if ($debug_count > 0) {
            echo "\n=== DEBUG MESSAGES ===\n";
            foreach ($lines as $line) {
                if (strpos($line, 'DEBUG') !== false) {
                    echo $line . "\n";
                }
            }
        }
        
        if ($error_count > 0) {
            echo "\n=== ERROR MESSAGES ===\n";
            foreach ($lines as $line) {
                if (strpos($line, 'ERROR') !== false) {
                    echo $line . "\n";
                }
            }
        }
    }
} else {
    echo "❌ Log file không tồn tại: {$log_file}\n";
}

echo "\n=== HOÀN THÀNH ===\n";


