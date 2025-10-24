<?php

/**
 * Password Reset Model
 * 
 * Quản lý các token reset password
 * 
 * @package    App
 * @subpackage Model
 */
class Model_Password_Reset extends \Model
{
    /**
     * Table name
     */
    protected static $_table_name = 'password_resets';

    /**
     * Tạo token reset password mới
     * 
     * @param string $email Email của user
     * @return Model_Password_Reset|null
     */
    public static function create_reset_token($email)
    {
        try {
            // Xóa các token cũ của email này
            self::delete_old_tokens($email);

            // Tạo token mới
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token hết hạn sau 1 giờ
            $created_at = date('Y-m-d H:i:s');

            // Insert vào database
            $query = \DB::query("INSERT INTO password_resets (email, token, created_at, expires_at) VALUES (:email, :token, :created_at, :expires_at)");
            $result = $query->param('email', $email)
                           ->param('token', $token)
                           ->param('created_at', $created_at)
                           ->param('expires_at', $expires_at)
                           ->execute();

            if ($result) {
                // Tạo object mới với dữ liệu vừa insert
                $reset = new self();
                $reset->id = $result[0];
                $reset->email = $email;
                $reset->token = $token;
                $reset->created_at = $created_at;
                $reset->expires_at = $expires_at;
                $reset->used_at = null;
                return $reset;
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Create reset token failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm token reset password
     * 
     * @param string $token Token
     * @return Model_Password_Reset|null
     */
    public static function find_by_token($token)
    {
        try {
            $query = \DB::query("SELECT * FROM password_resets WHERE token = :token AND used_at IS NULL AND expires_at > :now");
            $result = $query->param('token', $token)
                           ->param('now', date('Y-m-d H:i:s'))
                           ->execute();

            if ($result->count() > 0) {
                $row = $result->current();
                $reset = new self();
                $reset->id = $row['id'];
                $reset->email = $row['email'];
                $reset->token = $row['token'];
                $reset->created_at = $row['created_at'];
                $reset->expires_at = $row['expires_at'];
                $reset->used_at = $row['used_at'];
                return $reset;
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Find reset token failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Đánh dấu token đã được sử dụng
     * 
     * @return bool
     */
    public function mark_as_used()
    {
        try {
            $used_at = date('Y-m-d H:i:s');
            $query = \DB::query("UPDATE password_resets SET used_at = :used_at WHERE id = :id");
            $result = $query->param('used_at', $used_at)
                           ->param('id', $this->id)
                           ->execute();
            
            if ($result) {
                $this->used_at = $used_at;
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            \Log::error('Mark token as used failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa các token cũ của email
     * 
     * @param string $email Email
     * @return void
     */
    protected static function delete_old_tokens($email)
    {
        try {
            $query = \DB::query("DELETE FROM password_resets WHERE email = :email");
            $query->param('email', $email)->execute();
        } catch (\Exception $e) {
            \Log::error('Delete old tokens failed: ' . $e->getMessage());
        }
    }

    /**
     * Dọn dẹp các token hết hạn
     * 
     * @return void
     */
    public static function cleanup_expired_tokens()
    {
        try {
            $query = \DB::query("DELETE FROM password_resets WHERE expires_at < :now");
            $query->param('now', date('Y-m-d H:i:s'))->execute();
        } catch (\Exception $e) {
            \Log::error('Cleanup expired tokens failed: ' . $e->getMessage());
        }
    }
}
