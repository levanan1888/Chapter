<?php

namespace Fuel\Migrations;

class Create_authors_table
{
	public function up()
	{
		\DBUtil::create_table('authors', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true),
			'name' => array('type' => 'varchar', 'constraint' => 255),
			'slug' => array('type' => 'varchar', 'constraint' => 255),
			'description' => array('type' => 'text', 'null' => true),
			'avatar' => array('type' => 'varchar', 'constraint' => 500, 'null' => true),
			'is_active' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 1),
			'created_at' => array('type' => 'datetime'),
			'updated_at' => array('type' => 'datetime'),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('authors');
	}
}