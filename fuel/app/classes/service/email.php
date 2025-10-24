<?php

/**
 * Email Service
 * 
 * Xử lý gửi email trong hệ thống
 * 
 * @package    App
 * @subpackage Service
 */
class Service_Email
{
    /**
     * Cấu hình email
     * 
     * @var array
     */
    protected $config;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->config = Config::load('email');
    }

    /**
     * Gửi email đặt lại mật khẩu
     * 
     * @param string $email Email người nhận
     * @param string $token Token đặt lại mật khẩu
     * @param string $username Tên người dùng
     * @return bool
     */
    public function send_password_reset($email, $token, $username = '')
    {
        try {
            $subject = 'Mã xác thực đặt lại mật khẩu - ComicHub';
            $message = $this->get_password_reset_template($username, $token);
            
            return $this->send_email($email, $subject, $message);
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi email thông báo đặt lại mật khẩu thành công
     * 
     * @param string $email Email người nhận
     * @param string $username Tên người dùng
     * @return bool
     */
    public function send_password_reset_success($email, $username = '')
    {
        try {
            $subject = 'Mật khẩu đã được đặt lại thành công - ComicHub';
            $message = $this->get_password_reset_success_template($username);
            
            return $this->send_email($email, $subject, $message);
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset success email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi email chung
     * 
     * @param string $to Email người nhận
     * @param string $subject Tiêu đề
     * @param string $message Nội dung
     * @return bool
     */
    protected function send_email($to, $subject, $message)
    {
        try {
            // Sử dụng PHPMailer nếu có cấu hình SMTP
            if (isset($this->config['smtp_enabled']) && $this->config['smtp_enabled']) {
                return $this->send_via_smtp($to, $subject, $message);
            } else {
                // Sử dụng mail() function của PHP
                return $this->send_via_php_mail($to, $subject, $message);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi email qua SMTP
     * 
     * @param string $to Email người nhận
     * @param string $subject Tiêu đề
     * @param string $message Nội dung
     * @return bool
     */
    protected function send_via_smtp($to, $subject, $message)
    {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host = $this->config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtp_username'];
            $mail->Password = $this->config['smtp_password'];
            $mail->SMTPSecure = $this->config['smtp_encryption'];
            $mail->Port = $this->config['smtp_port'];
            $mail->CharSet = 'UTF-8';

            // Người gửi và người nhận
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($to);

            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            return $mail->send();
        } catch (\Exception $e) {
            \Log::error('SMTP email failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi email qua PHP mail()
     * 
     * @param string $to Email người nhận
     * @param string $subject Tiêu đề
     * @param string $message Nội dung
     * @return bool
     */
    protected function send_via_php_mail($to, $subject, $message)
    {
        try {
            $headers = array(
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                'From: ' . $this->config['from_name'] . ' <' . $this->config['from_email'] . '>',
                'Reply-To: ' . $this->config['from_email'],
                'X-Mailer: PHP/' . phpversion()
            );

            return mail($to, $subject, $message, implode("\r\n", $headers));
        } catch (\Exception $e) {
            \Log::error('PHP mail() failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Template email đặt lại mật khẩu
     * 
     * @param string $username Tên người dùng
     * @param string $token Token
     * @return string
     */
    protected function get_password_reset_template($username, $token)
    {
        $display_name = !empty($username) ? $username : 'Bạn';
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Đặt lại mật khẩu - ComicHub</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
                .button { display: inline-block; background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; margin: 20px 0; }
                .code { background: #e9ecef; padding: 15px; border-radius: 4px; font-family: monospace; font-size: 18px; text-align: center; margin: 20px 0; letter-spacing: 2px; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔐 Đặt lại mật khẩu</h1>
                </div>
                <div class="content">
                    <h2>Xin chào ' . htmlspecialchars($display_name) . '!</h2>
                    <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại ComicHub.</p>
                    
                    <p><strong>Mã xác thực của bạn là:</strong></p>
                    <div class="code">' . htmlspecialchars($token) . '</div>
                    
                    <p>Vui lòng quay lại trang web và nhập mã xác thực này để đặt lại mật khẩu của bạn.</p>
                    
                    <p><strong>Lưu ý quan trọng:</strong></p>
                    <ul>
                        <li>Mã xác thực này chỉ có hiệu lực trong <strong>1 giờ</strong></li>
                        <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này</li>
                        <li>Để bảo mật, không chia sẻ mã xác thực với bất kỳ ai</li>
                    </ul>
                </div>
                <div class="footer">
                    <p>Email này được gửi tự động từ hệ thống ComicHub</p>
                    <p>Nếu có thắc mắc, vui lòng liên hệ: support@comichub.com</p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Template email đặt lại mật khẩu thành công
     * 
     * @param string $username Tên người dùng
     * @return string
     */
    protected function get_password_reset_success_template($username)
    {
        $display_name = !empty($username) ? $username : 'Bạn';
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Mật khẩu đã được đặt lại - ComicHub</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✅ Mật khẩu đã được đặt lại thành công</h1>
                </div>
                <div class="content">
                    <h2>Xin chào ' . htmlspecialchars($display_name) . '!</h2>
                    <p>Mật khẩu của bạn đã được đặt lại thành công.</p>
                    
                    <p><strong>Thông tin tài khoản:</strong></p>
                    <ul>
                        <li>Mật khẩu mới đã được cập nhật</li>
                        <li>Bạn có thể đăng nhập với mật khẩu mới ngay bây giờ</li>
                    </ul>
                    
                    <p>Nếu bạn không thực hiện thay đổi này, vui lòng liên hệ với chúng tôi ngay lập tức.</p>
                </div>
                <div class="footer">
                    <p>Email này được gửi tự động từ hệ thống ComicHub</p>
                    <p>Nếu có thắc mắc, vui lòng liên hệ: support@comichub.com</p>
                </div>
            </div>
        </body>
        </html>';
    }
}
