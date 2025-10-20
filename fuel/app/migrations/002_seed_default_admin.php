<?php
/**
 * Migration để tạo admin mặc định
 * 
 * Tạo tài khoản admin đầu tiên với thông tin mặc định
 */

class Seed_Default_Admin
{
	/**
	 * Tạo admin mặc định
	 * 
	 * @return void
	 */
	public function up()
	{
		// Dữ liệu admin mặc định
		$admin_data = array(
			'username' => 'admin',
			'email' => 'admin@example.com',
			'password' => password_hash('admin123', PASSWORD_DEFAULT),
			'full_name' => 'Administrator',
			'is_active' => 1,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		);

		// Kiểm tra xem admin đã tồn tại chưa
		$existing_admin = \DB::select()
			->from('admins')
			->where('username', '=', 'admin')
			->execute();

		if ($existing_admin->count() == 0) {
			\DB::insert('admins')
				->set($admin_data)
				->execute();
		}
	}

	/**
	 * Xóa admin mặc định
	 * 
	 * @return void
	 */
	public function down()
	{
		\DB::delete('admins')
			->where('username', '=', 'admin')
			->execute();
	}
}
