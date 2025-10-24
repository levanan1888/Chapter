<?php

namespace Fuel\Migrations;

class Seed_default_admin
{
	public function up()
	{
		// Tạo admin mặc định
		$admin_data = array(
			'username' => 'admin',
			'email' => 'admin@example.com',
			'password' => password_hash('admin123', PASSWORD_DEFAULT),
			'full_name' => 'Administrator',
			'is_active' => 1,
			'user_type' => 'admin',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);

		\DB::insert('admins')->set($admin_data)->execute();
	}

	public function down()
	{
		\DB::delete('admins')->where('username', '=', 'admin')->execute();
	}
}
