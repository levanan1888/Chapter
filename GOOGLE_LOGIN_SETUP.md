# Google Login Setup Guide

## Bước 1: Tạo Google OAuth Application

1. Truy cập [Google Cloud Console](https://console.cloud.google.com/)
2. Tạo project mới hoặc chọn project hiện có
3. Kích hoạt Google+ API hoặc Google Identity API
4. Vào **Credentials** → **Create Credentials** → **OAuth 2.0 Client IDs**
5. Chọn **Web application**
6. Thêm **Authorized redirect URIs**:
   - `http://localhost/project-story/admin/google_callback` (cho development)
   - `https://yourdomain.com/admin/google_callback` (cho production)

## Bước 2: Cấu hình trong ứng dụng

1. Mở file `fuel/app/config/google.php`
2. Thay thế các giá trị sau:
   ```php
   'client_id' => 'YOUR_GOOGLE_CLIENT_ID',
   'client_secret' => 'YOUR_GOOGLE_CLIENT_SECRET',
   ```

## Bước 3: Chạy Migration

Chạy migration để thêm cột `google_id` vào bảng `admins`:

```bash
php oil migrate
```

## Bước 4: Test Google Login

1. Truy cập trang đăng nhập admin
2. Click vào nút "Đăng nhập với Google"
3. Xác thực với Google
4. Kiểm tra xem có tạo tài khoản mới hoặc đăng nhập thành công không

## Lưu ý

- Đảm bảo redirect URI trong Google Console khớp với URL callback trong config
- Trong production, sử dụng HTTPS cho redirect URI
- Google ID sẽ được lưu trong cột `google_id` của bảng `admins`
- Nếu email đã tồn tại, hệ thống sẽ liên kết Google ID với tài khoản hiện có
