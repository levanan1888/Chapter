<?php

namespace Fuel\Migrations;

class Add_is_visible_to_stories
{
	public function up()
	{
		\DBUtil::add_fields('stories', array(
			'is_visible' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 1, 'comment' => 'Trạng thái hiển thị: 1 = hiển thị, 0 = ẩn'),
		));
	}

	public function down()
	{
		\DBUtil::drop_fields('stories', array('is_visible'));
	}
}
