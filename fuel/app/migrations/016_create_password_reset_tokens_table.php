<?php

namespace Fuel\Migrations;

class Create_password_reset_tokens_table
{
    public function up()
    {
        \DBUtil::create_table('password_reset_tokens', array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'email' => array('type' => 'varchar', 'constraint' => 255),
            'token' => array('type' => 'varchar', 'constraint' => 255),
            'expires_at' => array('type' => 'datetime'),
            'created_at' => array('type' => 'datetime'),
            'updated_at' => array('type' => 'datetime'),
        ), array('id'), true, 'InnoDB', 'utf8mb4_unicode_ci');

        // Thêm index cho email và token để tìm kiếm nhanh
        \DBUtil::create_index('password_reset_tokens', 'email', 'idx_email');
        \DBUtil::create_index('password_reset_tokens', 'token', 'idx_token');
        \DBUtil::create_index('password_reset_tokens', 'expires_at', 'idx_expires_at');
    }

    public function down()
    {
        \DBUtil::drop_table('password_reset_tokens');
    }
}
