<?php

/**
 * Model PasswordResetToken
 * 
 * Quản lý token đặt lại mật khẩu
 * 
 * @package    App
 * @subpackage Model
 */
class Model_PasswordResetToken extends \Model
{
    /**
     * Tên bảng trong database
     * 
     * @var string
     */
    protected static $_table_name = 'password_reset_tokens';

    /**
     * Khóa chính của bảng
     * 
     * @var string
     */
    protected static $_primary_key = 'id';

    /**
     * Các trường có thể được gán giá trị
     * 
     * @var array
     */
    protected static $_properties = array(
        'id',
        'email',
        'token',
        'expires_at',
        'created_at',
        'updated_at',
    );

    /**
     * Các properties của model
     */
    public $id;
    public $email;
    public $token;
    public $expires_at;
    public $created_at;
    public $updated_at;

    /**
     * Các trường được mã hóa khi lưu
     * 
     * @var array
     */
    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'mysql_timestamp' => false,
        ),
    );

    /**
     * Tạo token đặt lại mật khẩu
     * 
     * @param string $email Email của user
     * @return Model_PasswordResetToken|null
     */
    public static function create_reset_token($email)
    {
        try {
            // Xóa token cũ nếu có
            self::delete_old_tokens($email);

            // Tạo token mới
            $token = self::generate_token();
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token hết hạn sau 1 giờ
            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            $query = \DB::query("INSERT INTO password_reset_tokens (email, token, expires_at, created_at, updated_at) VALUES (:email, :token, :expires_at, :created_at, :updated_at)");
            $result = $query->param('email', $email)
                            ->param('token', $token)
                            ->param('expires_at', $expires_at)
                            ->param('created_at', $created_at)
                            ->param('updated_at', $updated_at)
                            ->execute();

            if ($result) {
                $reset_token = new self();
                $reset_token->id = $result[0];
                $reset_token->email = $email;
                $reset_token->token = $token;
                $reset_token->expires_at = $expires_at;
                $reset_token->created_at = $created_at;
                $reset_token->updated_at = $updated_at;
                return $reset_token;
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Failed to create password reset token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm token theo email và token
     * 
     * @param string $email Email
     * @param string $token Token
     * @return Model_PasswordResetToken|null
     */
    public static function find_by_email_and_token($email, $token)
    {
        try {
            $query = \DB::query("SELECT * FROM password_reset_tokens WHERE email = :email AND token = :token AND expires_at > :now");
            $result = $query->param('email', $email)
                            ->param('token', $token)
                            ->param('now', date('Y-m-d H:i:s'))
                            ->execute();

            if ($result->count() > 0) {
                $data = $result->current();
                $reset_token = new self();
                foreach ($data as $key => $value) {
                    $reset_token->$key = $value;
                }
                return $reset_token;
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Failed to find password reset token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Xóa token cũ của email
     * 
     * @param string $email Email
     * @return bool
     */
    public static function delete_old_tokens($email)
    {
        try {
            $query = \DB::query("DELETE FROM password_reset_tokens WHERE email = :email");
            $result = $query->param('email', $email)->execute();
            return (bool) $result;
        } catch (\Exception $e) {
            \Log::error('Failed to delete old password reset tokens: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa token đã sử dụng
     * 
     * @return bool
     */
    public function delete()
    {
        try {
            $query = \DB::query("DELETE FROM password_reset_tokens WHERE id = :id");
            $result = $query->param('id', $this->id)->execute();
            return (bool) $result;
        } catch (\Exception $e) {
            \Log::error('Failed to delete password reset token: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa token hết hạn
     * 
     * @return int Số token đã xóa
     */
    public static function cleanup_expired_tokens()
    {
        try {
            $query = \DB::query("DELETE FROM password_reset_tokens WHERE expires_at < :now");
            $result = $query->param('now', date('Y-m-d H:i:s'))->execute();
            return (int) $result;
        } catch (\Exception $e) {
            \Log::error('Failed to cleanup expired password reset tokens: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Tạo token ngẫu nhiên
     * 
     * @return string
     */
    protected static function generate_token()
    {
        return bin2hex(random_bytes(6));
    }

    /**
     * Kiểm tra token có hết hạn không
     * 
     * @return bool
     */
    public function is_expired()
    {
        return strtotime($this->expires_at) < time();
    }
}

