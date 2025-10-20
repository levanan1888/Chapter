<?php
/**
 * Task để setup hệ thống admin
 * 
 * Chạy migration và tạo admin mặc định
 * 
 * @package    Fuel\Tasks
 */

namespace Fuel\Tasks;

class Setup_Admin
{
	/**
	 * Chạy setup hệ thống admin
	 * 
	 * @return void
	 */
	public static function run()
	{
		echo "=== SETUP HỆ THỐNG ADMIN ===\n\n";

		try {
			// Chạy migration tạo bảng admins
			echo "1. Chạy migration tạo bảng admins...\n";
			self::run_migration('001_create_admins_table');
			echo "   ✓ Tạo bảng admins thành công\n\n";

			// Chạy migration tạo admin mặc định
			echo "2. Tạo admin mặc định...\n";
			self::run_migration('002_seed_default_admin');
			echo "   ✓ Tạo admin mặc định thành công\n\n";

			echo "=== HOÀN THÀNH SETUP ===\n";
			echo "Thông tin đăng nhập mặc định:\n";
			echo "Username: admin\n";
			echo "Password: admin123\n";
			echo "Email: admin@example.com\n\n";
			echo "Truy cập: " . Uri::create('admin/login') . "\n";

		} catch (\Exception $e) {
			echo "❌ Lỗi: " . $e->getMessage() . "\n";
			echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
		}
	}

	/**
	 * Chạy migration
	 * 
	 * @param string $migration_name Tên migration
	 * @return void
	 */
	protected static function run_migration($migration_name)
	{
		$migration_file = APPPATH . 'migrations/' . $migration_name . '.php';
		
		if (!file_exists($migration_file)) {
			throw new \Exception("Migration file không tồn tại: " . $migration_file);
		}

		// Load migration class
		require_once $migration_file;
		// Tạo tên class từ tên file migration (bỏ số prefix và giữ underscore)
		$class_name = preg_replace('/^\d+_/', '', $migration_name);
		$class_name = ucwords(str_replace('_', ' ', $class_name));
		$class_name = str_replace(' ', '_', $class_name);
		
		if (!class_exists($class_name)) {
			throw new \Exception("Migration class không tồn tại: " . $class_name);
		}

		$migration = new $class_name();
		$migration->up();
	}

	/**
	 * Hiển thị hướng dẫn sử dụng
	 * 
	 * @return void
	 */
	public static function help()
	{
		echo "Task Setup Admin\n\n";
		echo "Chạy migration và tạo admin mặc định cho hệ thống.\n\n";
		echo "Sử dụng:\n";
		echo "  php oil task setup_admin\n\n";
		echo "Chức năng:\n";
		echo "  - Tạo bảng admins trong database\n";
		echo "  - Tạo admin mặc định (admin/admin123)\n";
		echo "  - Hiển thị thông tin đăng nhập\n\n";
		echo "Lưu ý: Đảm bảo database đã được cấu hình trong fuel/app/config/db.php\n";
	}
}
