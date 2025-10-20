<?php
/**
 * Task setup admin đơn giản
 * 
 * Sử dụng PDO trực tiếp để tạo bảng và admin
 * 
 * @package    Fuel\Tasks
 */

namespace Fuel\Tasks;

class Setup_Admin_Simple
{
	/**
	 * Chạy setup hệ thống admin
	 * 
	 * @return void
	 */
	public static function run()
	{
		echo "=== SETUP HỆ THỐNG ADMIN (SIMPLE) ===\n\n";

		try {
			// Kết nối database
			echo "1. Kết nối database...\n";
			$pdo = new \PDO('mysql:host=localhost;dbname=project_story', 'root', '');
			$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			echo "   ✓ Kết nối database thành công\n\n";

			// Tạo bảng admins
			echo "2. Tạo bảng admins...\n";
			$sql = "CREATE TABLE IF NOT EXISTS `admins` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`username` varchar(50) NOT NULL,
				`email` varchar(100) NOT NULL,
				`password` varchar(255) NOT NULL,
				`full_name` varchar(100) DEFAULT NULL,
				`is_active` tinyint(1) NOT NULL DEFAULT 1,
				`last_login` datetime DEFAULT NULL,
				`created_at` datetime NOT NULL,
				`updated_at` datetime NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `username` (`username`),
				UNIQUE KEY `email` (`email`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
			
			$pdo->exec($sql);
			echo "   ✓ Tạo bảng admins thành công\n\n";

			// Kiểm tra admin đã tồn tại chưa
			echo "3. Kiểm tra admin mặc định...\n";
			$stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
			$stmt->execute(['admin']);
			$count = $stmt->fetchColumn();

			if ($count > 0) {
				echo "   ✓ Admin mặc định đã tồn tại\n\n";
			} else {
				echo "4. Tạo admin mặc định...\n";
				$password_hash = password_hash('admin123', PASSWORD_DEFAULT);
				$now = date('Y-m-d H:i:s');
				
				$stmt = $pdo->prepare("INSERT INTO admins (username, email, password, full_name, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
				$stmt->execute([
					'admin',
					'admin@example.com',
					$password_hash,
					'Administrator',
					1,
					$now,
					$now
				]);
				echo "   ✓ Tạo admin mặc định thành công\n\n";
			}

			echo "=== HOÀN THÀNH SETUP ===\n";
			echo "Thông tin đăng nhập mặc định:\n";
			echo "Username: admin\n";
			echo "Password: admin123\n";
			echo "Email: admin@example.com\n\n";
			echo "Truy cập: http://localhost/project-story/admin/login\n";

		} catch (\Exception $e) {
			echo "❌ Lỗi: " . $e->getMessage() . "\n";
		}
	}

	/**
	 * Hiển thị hướng dẫn sử dụng
	 * 
	 * @return void
	 */
	public static function help()
	{
		echo "Task Setup Admin Simple\n\n";
		echo "Setup hệ thống admin bằng PDO trực tiếp.\n\n";
		echo "Sử dụng:\n";
		echo "  php oil r setup_admin_simple\n\n";
		echo "Chức năng:\n";
		echo "  - Tạo bảng admins trong database\n";
		echo "  - Tạo admin mặc định (admin/admin123)\n";
		echo "  - Hiển thị thông tin đăng nhập\n\n";
	}
}
