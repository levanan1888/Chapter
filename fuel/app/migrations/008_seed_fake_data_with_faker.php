<?php

namespace Fuel\Migrations;

class Seed_fake_data_with_faker
{
	public function up()
	{
		// Import Faker
		$faker = \Faker\Factory::create('vi_VN'); // Tiếng Việt
		
		// Tạo admin mặc định
		$admin_data = array(
			'username' => 'admin',
			'email' => 'admin@example.com',
			'password' => password_hash('admin123', PASSWORD_DEFAULT),
			'full_name' => 'Administrator',
			'is_active' => 1,
			'created_at' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
			'updated_at' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
		);
		\DB::insert('admins')->set($admin_data)->execute();

		// Tạo categories với Faker (chỉ tạo những category chưa tồn tại)
		$category_names = ['Action', 'Romance', 'Comedy', 'Drama', 'Fantasy', 'Horror', 'Sci-Fi', 'Mystery', 'Thriller', 'Adventure'];
		$category_colors = ['#ff6b6b', '#ff9ff3', '#54a0ff', '#5f27cd', '#00d2d3', '#2f3542', '#ff6348', '#ffa502', '#2ed573', '#ff3838'];
		
		for ($i = 0; $i < 10; $i++) {
			// Kiểm tra xem category đã tồn tại chưa
			$existing = \DB::select()
				->from('categories')
				->where('name', '=', $category_names[$i])
				->execute();
			
			if ($existing->count() == 0) {
				$category_data = array(
					'name' => $category_names[$i],
					'description' => $faker->sentence(10),
					'color' => $category_colors[$i],
					'sort_order' => $i + 1,
					'created_at' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
					'updated_at' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
				);
				\DB::insert('categories')->set($category_data)->execute();
			}
		}

		// Tạo authors với Faker (chỉ tạo thêm nếu cần)
		$existing_authors = \DB::select()->from('authors')->execute();
		$authors_to_create = max(0, 20 - $existing_authors->count());
		
		for ($i = 0; $i < $authors_to_create; $i++) {
			$author_data = array(
				'name' => $faker->name(),
				'description' => $faker->paragraph(3),
				'created_at' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
				'updated_at' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
			);
			\DB::insert('authors')->set($author_data)->execute();
		}

		// Tạo stories với Faker (chỉ tạo thêm nếu cần)
		$existing_stories = \DB::select()->from('stories')->execute();
		$stories_to_create = max(0, 50 - $existing_stories->count());
		
		$story_titles = [
			'One Piece', 'Naruto', 'Attack on Titan', 'Demon Slayer', 'My Hero Academia',
			'Dragon Ball', 'Bleach', 'Death Note', 'Fullmetal Alchemist', 'Hunter x Hunter',
			'Tokyo Ghoul', 'One Punch Man', 'Mob Psycho 100', 'Jujutsu Kaisen', 'Chainsaw Man',
			'Spy x Family', 'Demon Slayer', 'The Promised Neverland', 'Dr. Stone', 'Fire Force'
		];

		// Lấy số lượng authors hiện có để đảm bảo author_id hợp lệ
		$total_authors = \DB::select()->from('authors')->execute()->count();

		for ($i = 0; $i < $stories_to_create; $i++) {
			$title = $faker->randomElement($story_titles) . ' ' . $faker->numberBetween(1, 999);
			$slug = strtolower(str_replace(' ', '-', $title));
			
			$story_data = array(
				'title' => $title,
				'slug' => $slug,
				'description' => $faker->paragraphs(3, true),
				'author_id' => $faker->numberBetween(1, max(1, $total_authors)),
				'status' => $faker->randomElement(['ongoing', 'completed', 'paused']),
				'views' => $faker->numberBetween(100, 50000),
				'is_featured' => $faker->boolean(20), // 20% chance
				'is_hot' => $faker->boolean(30), // 30% chance
				'created_at' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
				'updated_at' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
			);
			\DB::insert('stories')->set($story_data)->execute();
		}

		// Tạo story_categories relationships (chỉ cho stories mới tạo)
		$total_stories = \DB::select()->from('stories')->execute()->count();
		$total_categories = \DB::select()->from('categories')->execute()->count();
		
		// Chỉ tạo relationships cho stories từ migration này (bắt đầu từ story cuối cùng của migration 007 + 1)
		$start_story_id = max(1, $total_stories - $stories_to_create + 1);
		
		for ($i = $start_story_id; $i <= $total_stories; $i++) {
			$num_categories = $faker->numberBetween(1, 4); // Mỗi story có 1-4 categories
			$selected_categories = $faker->randomElements(range(1, $total_categories), $num_categories);
			
			foreach ($selected_categories as $category_id) {
				// Kiểm tra xem đã tồn tại chưa
				$exists = \DB::select()
					->from('story_categories')
					->where('story_id', '=', $i)
					->where('category_id', '=', $category_id)
					->execute();
				
				if ($exists->count() == 0) {
					$story_category_data = array(
						'story_id' => $i,
						'category_id' => $category_id,
					);
					\DB::insert('story_categories')->set($story_category_data)->execute();
				}
			}
		}

		// Tạo chapters với Faker (chỉ cho stories mới tạo)
		for ($i = $start_story_id; $i <= $total_stories; $i++) {
			$num_chapters = $faker->numberBetween(5, 30); // Mỗi story có 5-30 chapters
			
			for ($j = 1; $j <= $num_chapters; $j++) {
				$num_images = $faker->numberBetween(10, 50); // Mỗi chapter có 10-50 ảnh
				$images = array();
				
				for ($k = 1; $k <= $num_images; $k++) {
					$images[] = "uploads/chapters/story-$i-chapter-$j-image-$k.jpg";
				}
				
				$chapter_data = array(
					'story_id' => $i,
					'title' => $faker->sentence(4),
					'chapter_number' => $j,
					'images' => json_encode($images),
					'views' => $faker->numberBetween(50, 5000),
					'created_at' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
					'updated_at' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
				);
				\DB::insert('chapters')->set($chapter_data)->execute();
			}
		}
	}

	public function down()
	{
		// Xóa tất cả dữ liệu (kiểm tra tồn tại bảng trước khi xóa để tránh lỗi khi migrate xuống)
		try {
			if (\DBUtil::table_exists('chapters')) {
				\DB::delete('chapters')->execute();
			}
			if (\DBUtil::table_exists('story_categories')) {
				\DB::delete('story_categories')->execute();
			}
			if (\DBUtil::table_exists('stories')) {
				\DB::delete('stories')->execute();
			}
			if (\DBUtil::table_exists('authors')) {
				\DB::delete('authors')->execute();
			}
			if (\DBUtil::table_exists('categories')) {
				\DB::delete('categories')->execute();
			}
			if (\DBUtil::table_exists('admins')) {
				\DB::delete('admins')->where('username', '=', 'admin')->execute();
			}
		} catch (\Exception $e) {
			\Log::error('Migration 008 down() failed to cleanup: ' . $e->getMessage());
		}
	}
}
