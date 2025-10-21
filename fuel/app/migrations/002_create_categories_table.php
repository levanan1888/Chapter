<?php

namespace Fuel\Migrations;

class Create_categories_table
{
	public function up()
	{
		\DBUtil::create_table('categories', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true),
			'name' => array('type' => 'varchar', 'constraint' => 255),
			'slug' => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
			'description' => array('type' => 'text', 'null' => true),
			'color' => array('type' => 'varchar', 'constraint' => 7, 'default' => '#007bff'),
			'is_active' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 1),
			'sort_order' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'created_at' => array('type' => 'datetime'),
			'updated_at' => array('type' => 'datetime'),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('categories');
	}
}