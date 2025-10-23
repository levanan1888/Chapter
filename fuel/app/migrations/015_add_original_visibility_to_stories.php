<?php

namespace Fuel\Migrations;

class Add_original_visibility_to_stories
{
	public function up()
	{
		\DBUtil::add_fields('stories', array(
			'original_visibility' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 1, 'comment' => 'Trạng thái hiển thị ban đầu trước khi bị ẩn bởi danh mục: 1 = hiển thị, 0 = ẩn'),
		));
		
		// Set original_visibility = is_visible cho tất cả truyện hiện tại
		\DB::query("UPDATE stories SET original_visibility = is_visible")->execute();
		
		\Log::info('Migration 015: Added original_visibility field to stories table');
	}

	public function down()
	{
		\DBUtil::drop_fields('stories', array('original_visibility'));
		\Log::info('Migration 015 down: Dropped original_visibility field from stories table');
	}
}
