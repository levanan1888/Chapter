<?php

namespace Fuel\Migrations;

class Create_story_categories_table
{
	public function up()
	{
		\DBUtil::create_table('story_categories', array(
			'story_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
			'category_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
		), array('story_id', 'category_id'));

		\DBUtil::create_index('story_categories', array('story_id', 'category_id'), 'idx_story_category', 'UNIQUE');
	}

	public function down()
	{
		\DBUtil::drop_table('story_categories');
	}
}