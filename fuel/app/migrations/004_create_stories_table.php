<?php

namespace Fuel\Migrations;

class Create_stories_table
{
	public function up()
	{
		\DBUtil::create_table('stories', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true),
			'title' => array('type' => 'varchar', 'constraint' => 255),
			'slug' => array('type' => 'varchar', 'constraint' => 255, 'unique' => true),
			'description' => array('type' => 'text', 'null' => true),
			'cover_image' => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
			'author_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
			'status' => array('type' => 'enum', 'constraint' => array('ongoing', 'completed', 'paused'), 'default' => 'ongoing'),
			'views' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'is_featured' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 0),
			'is_hot' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 0),
			'created_at' => array('type' => 'datetime'),
			'updated_at' => array('type' => 'datetime'),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('stories');
	}
}