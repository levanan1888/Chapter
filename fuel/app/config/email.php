<?php

return array(
    // Cấu hình SMTP
    'smtp_enabled' => true, // Đặt true để bật SMTP
    
    // Thông tin người gửi
    'from_email' => 'levanan3418@gmail.com', // Đổi thành email Gmail của bạn
    'from_name' => 'ComicHub',
    
    // Cấu hình SMTP Gmail
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'levanan3418@gmail.com', // Đổi thành email Gmail của bạn
    'smtp_password' => 'svtj crjq jfqg yfre', // Dán mật khẩu ứng dụng ở đây
    'smtp_encryption' => 'tls', // Sử dụng TLS cho Gmail
    
    // Cấu hình khác
    'timeout' => 30,
    'debug' => false,
);
