<?php

namespace Fuel\Migrations;

class Seed_fake_data
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
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		);
		\DB::insert('admins')->set($admin_data)->execute();

		// Tạo categories
		$categories = array(
			array('name' => 'Action', 'slug' => 'action', 'description' => 'Truyện hành động', 'color' => '#ff6b6b', 'sort_order' => 1, 'is_active' => 1),
			array('name' => 'Romance', 'slug' => 'romance', 'description' => 'Truyện tình cảm', 'color' => '#ff9ff3', 'sort_order' => 2, 'is_active' => 1),
			array('name' => 'Comedy', 'slug' => 'comedy', 'description' => 'Truyện hài hước', 'color' => '#54a0ff', 'sort_order' => 3, 'is_active' => 1),
			array('name' => 'Drama', 'slug' => 'drama', 'description' => 'Truyện chính kịch', 'color' => '#5f27cd', 'sort_order' => 4, 'is_active' => 1),
			array('name' => 'Fantasy', 'slug' => 'fantasy', 'description' => 'Truyện giả tưởng', 'color' => '#00d2d3', 'sort_order' => 5, 'is_active' => 1),
			array('name' => 'Horror', 'slug' => 'horror', 'description' => 'Truyện kinh dị', 'color' => '#2f3542', 'sort_order' => 6, 'is_active' => 1),
		);

		foreach ($categories as $category) {
			$category['created_at'] = date('Y-m-d H:i:s');
			$category['updated_at'] = date('Y-m-d H:i:s');
			\DB::insert('categories')->set($category)->execute();
		}

		// Tạo authors
		$authors = array(
			array('name' => 'Tác giả A', 'description' => 'Tác giả nổi tiếng với nhiều tác phẩm hay'),
			array('name' => 'Tác giả B', 'description' => 'Chuyên viết truyện tình cảm'),
			array('name' => 'Tác giả C', 'description' => 'Tác giả trẻ với phong cách mới'),
			array('name' => 'Tác giả D', 'description' => 'Chuyên viết truyện hành động'),
			array('name' => 'Tác giả E', 'description' => 'Tác giả kinh nghiệm lâu năm'),
		);

		foreach ($authors as $author) {
			$author['created_at'] = date('Y-m-d H:i:s');
			$author['updated_at'] = date('Y-m-d H:i:s');
			\DB::insert('authors')->set($author)->execute();
		}

		// Tạo stories
		$stories = array(
			array(
				'title' => 'One Piece',
				'slug' => 'one-piece',
				'description' => 'Câu chuyện về Luffy và nhóm hải tặc Mũ Rơm đi tìm kho báu One Piece.',
				'author_id' => 1,
				'status' => 'ongoing',
				'views' => 15000,
				'is_featured' => 1,
				'is_hot' => 1,
			),
			array(
				'title' => 'Naruto',
				'slug' => 'naruto',
				'description' => 'Câu chuyện về ninja trẻ Naruto Uzumaki và ước mơ trở thành Hokage.',
				'author_id' => 2,
				'status' => 'completed',
				'views' => 25000,
				'is_featured' => 1,
				'is_hot' => 0,
			),
			array(
				'title' => 'Attack on Titan',
				'slug' => 'attack-on-titan',
				'description' => 'Cuộc chiến của nhân loại chống lại những Titan khổng lồ.',
				'author_id' => 3,
				'status' => 'completed',
				'views' => 30000,
				'is_featured' => 0,
				'is_hot' => 1,
			),
			array(
				'title' => 'Demon Slayer',
				'slug' => 'demon-slayer',
				'description' => 'Câu chuyện về Tanjiro và cuộc chiến chống lại quỷ.',
				'author_id' => 4,
				'status' => 'completed',
				'views' => 20000,
				'is_featured' => 1,
				'is_hot' => 1,
			),
			array(
				'title' => 'My Hero Academia',
				'slug' => 'my-hero-academia',
				'description' => 'Thế giới nơi mọi người đều có siêu năng lực.',
				'author_id' => 5,
				'status' => 'ongoing',
				'views' => 18000,
				'is_featured' => 0,
				'is_hot' => 0,
			),
		);

		foreach ($stories as $story) {
			$story['created_at'] = date('Y-m-d H:i:s');
			$story['updated_at'] = date('Y-m-d H:i:s');
			\DB::insert('stories')->set($story)->execute();
		}

		// Tạo story_categories relationships
		$story_categories = array(
			array('story_id' => 1, 'category_id' => 1), // One Piece - Action
			array('story_id' => 1, 'category_id' => 3), // One Piece - Comedy
			array('story_id' => 2, 'category_id' => 1), // Naruto - Action
			array('story_id' => 2, 'category_id' => 4), // Naruto - Drama
			array('story_id' => 3, 'category_id' => 1), // Attack on Titan - Action
			array('story_id' => 3, 'category_id' => 4), // Attack on Titan - Drama
			array('story_id' => 4, 'category_id' => 1), // Demon Slayer - Action
			array('story_id' => 4, 'category_id' => 4), // Demon Slayer - Drama
			array('story_id' => 5, 'category_id' => 1), // My Hero Academia - Action
			array('story_id' => 5, 'category_id' => 3), // My Hero Academia - Comedy
		);

		foreach ($story_categories as $sc) {
			\DB::insert('story_categories')->set($sc)->execute();
		}

		// Tạo chapters cho mỗi story
		$chapters_data = array();
		
		// One Piece chapters
		for ($i = 1; $i <= 10; $i++) {
			$chapters_data[] = array(
				'story_id' => 1,
				'title' => "Chương $i: Bắt đầu cuộc phiêu lưu",
				'chapter_number' => $i,
				'images' => json_encode(array(
					"uploads/chapters/one-piece-$i-1.jpg",
					"uploads/chapters/one-piece-$i-2.jpg",
					"uploads/chapters/one-piece-$i-3.jpg"
				)),
				'views' => rand(100, 1000),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			);
		}

		// Naruto chapters
		for ($i = 1; $i <= 15; $i++) {
			$chapters_data[] = array(
				'story_id' => 2,
				'title' => "Chương $i: Hành trình ninja",
				'chapter_number' => $i,
				'images' => json_encode(array(
					"uploads/chapters/naruto-$i-1.jpg",
					"uploads/chapters/naruto-$i-2.jpg"
				)),
				'views' => rand(200, 1500),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			);
		}

		// Attack on Titan chapters
		for ($i = 1; $i <= 12; $i++) {
			$chapters_data[] = array(
				'story_id' => 3,
				'title' => "Chương $i: Cuộc chiến với Titan",
				'chapter_number' => $i,
				'images' => json_encode(array(
					"uploads/chapters/aot-$i-1.jpg",
					"uploads/chapters/aot-$i-2.jpg",
					"uploads/chapters/aot-$i-3.jpg"
				)),
				'views' => rand(300, 2000),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			);
		}

		// Demon Slayer chapters
		for ($i = 1; $i <= 8; $i++) {
			$chapters_data[] = array(
				'story_id' => 4,
				'title' => "Chương $i: Cuộc chiến với quỷ",
				'chapter_number' => $i,
				'images' => json_encode(array(
					"uploads/chapters/demon-slayer-$i-1.jpg",
					"uploads/chapters/demon-slayer-$i-2.jpg"
				)),
				'views' => rand(250, 1800),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			);
		}

		// My Hero Academia chapters
		for ($i = 1; $i <= 6; $i++) {
			$chapters_data[] = array(
				'story_id' => 5,
				'title' => "Chương $i: Học viện siêu anh hùng",
				'chapter_number' => $i,
				'images' => json_encode(array(
					"uploads/chapters/mha-$i-1.jpg",
					"uploads/chapters/mha-$i-2.jpg"
				)),
				'views' => rand(150, 1200),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			);
		}

		// Insert all chapters
		foreach ($chapters_data as $chapter) {
			\DB::insert('chapters')->set($chapter)->execute();
		}
	}

	public function down()
	{
		// Xóa tất cả dữ liệu (an toàn khi bảng có thể đã bị drop ở migration khác)
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
			\Log::error('Migration 007 down() failed to cleanup: ' . $e->getMessage());
		}
	}
}
