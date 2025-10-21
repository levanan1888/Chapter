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

		// Tạo categories với Faker
		$category_names = ['Action', 'Romance', 'Comedy', 'Drama', 'Fantasy', 'Horror', 'Sci-Fi', 'Mystery', 'Thriller', 'Adventure'];
		$category_colors = ['#ff6b6b', '#ff9ff3', '#54a0ff', '#5f27cd', '#00d2d3', '#2f3542', '#ff6348', '#ffa502', '#2ed573', '#ff3838'];
		
		for ($i = 0; $i < 10; $i++) {
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

		// Tạo authors với Faker
		for ($i = 0; $i < 20; $i++) {
			$author_data = array(
				'name' => $faker->name(),
				'description' => $faker->paragraph(3),
				'created_at' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
				'updated_at' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
			);
			\DB::insert('authors')->set($author_data)->execute();
		}

		// Tạo stories với Faker
		$story_titles = [
			'One Piece', 'Naruto', 'Attack on Titan', 'Demon Slayer', 'My Hero Academia',
			'Dragon Ball', 'Bleach', 'Death Note', 'Fullmetal Alchemist', 'Hunter x Hunter',
			'Tokyo Ghoul', 'One Punch Man', 'Mob Psycho 100', 'Jujutsu Kaisen', 'Chainsaw Man',
			'Spy x Family', 'Demon Slayer', 'The Promised Neverland', 'Dr. Stone', 'Fire Force'
		];

		for ($i = 0; $i < 50; $i++) {
			$title = $faker->randomElement($story_titles) . ' ' . $faker->numberBetween(1, 999);
			$slug = strtolower(str_replace(' ', '-', $title));
			
			$story_data = array(
				'title' => $title,
				'slug' => $slug,
				'description' => $faker->paragraphs(3, true),
				'author_id' => $faker->numberBetween(1, 20),
				'status' => $faker->randomElement(['ongoing', 'completed', 'paused']),
				'views' => $faker->numberBetween(100, 50000),
				'is_featured' => $faker->boolean(20), // 20% chance
				'is_hot' => $faker->boolean(30), // 30% chance
				'created_at' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
				'updated_at' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
			);
			\DB::insert('stories')->set($story_data)->execute();
		}

		// Tạo story_categories relationships
		for ($i = 1; $i <= 50; $i++) {
			$num_categories = $faker->numberBetween(1, 4); // Mỗi story có 1-4 categories
			$selected_categories = $faker->randomElements(range(1, 10), $num_categories);
			
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

		// Tạo chapters với Faker
		for ($i = 1; $i <= 50; $i++) {
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
		// Xóa tất cả dữ liệu
		\DB::delete('chapters')->execute();
		\DB::delete('story_categories')->execute();
		\DB::delete('stories')->execute();
		\DB::delete('authors')->execute();
		\DB::delete('categories')->execute();
		\DB::delete('admins')->where('username', '=', 'admin')->execute();
	}
}
