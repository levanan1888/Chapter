<?php
/**
 * Task để tạo database
 * 
 * Tạo database project_story nếu chưa tồn tại
 * 
 * @package    Fuel\Tasks
 */

namespace Fuel\Tasks;

class Create_Database
{
	/**
	 * Tạo database project_story
	 * 
	 * @return void
	 */
	public static function run()
	{
		echo "=== TẠO DATABASE PROJECT_STORY ===\n\n";

		try {
			// Kết nối MySQL mà không chọn database
			$host = 'localhost';
			$username = 'root';
			$password = '';
			$database = 'project_story';

			echo "1. Kết nối MySQL...\n";
			$connection = new \mysqli($host, $username, $password);
			
			if ($connection->connect_error) {
				throw new \Exception("Kết nối MySQL thất bại: " . $connection->connect_error);
			}
			echo "   ✓ Kết nối MySQL thành công\n";

			// Kiểm tra database đã tồn tại chưa
			echo "2. Kiểm tra database '$database'...\n";
			$result = $connection->query("SHOW DATABASES LIKE '$database'");
			
			if ($result->num_rows > 0) {
				echo "   ✓ Database '$database' đã tồn tại\n";
			} else {
				echo "3. Tạo database '$database'...\n";
				$sql = "CREATE DATABASE `$database` CHARACTER SET utf8 COLLATE utf8_general_ci";
				
				if ($connection->query($sql) === TRUE) {
					echo "   ✓ Tạo database '$database' thành công\n";
				} else {
					throw new \Exception("Tạo database thất bại: " . $connection->error);
				}
			}

			$connection->close();

			echo "\n=== HOÀN THÀNH ===\n";
			echo "Database '$database' đã sẵn sàng!\n";
			echo "Bây giờ bạn có thể chạy: php oil task setup_admin\n";

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
		echo "Task Create Database\n\n";
		echo "Tạo database project_story cho hệ thống.\n\n";
		echo "Sử dụng:\n";
		echo "  php oil task create_database\n\n";
		echo "Chức năng:\n";
		echo "  - Kết nối MySQL server\n";
		echo "  - Kiểm tra database project_story\n";
		echo "  - Tạo database nếu chưa tồn tại\n";
		echo "  - Cấu hình charset UTF-8\n\n";
		echo "Lưu ý: Đảm bảo MySQL server đang chạy và user 'root' có quyền tạo database\n";
	}
}
