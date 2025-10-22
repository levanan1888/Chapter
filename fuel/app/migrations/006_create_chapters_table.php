<?php

namespace Fuel\Migrations;

class Create_chapters_table
{
	public function up()
	{
		\DBUtil::create_table('chapters', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true),
			'story_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
			'title' => array('type' => 'varchar', 'constraint' => 255),
			'chapter_number' => array('type' => 'int', 'constraint' => 11),
            'images' => array('type' => 'text'), // JSON encoded array of image paths
            'background_image' => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
			'views' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'created_at' => array('type' => 'datetime'),
			'updated_at' => array('type' => 'datetime'),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('chapters');
	}
}