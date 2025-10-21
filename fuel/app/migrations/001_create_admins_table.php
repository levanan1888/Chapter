<?php

namespace Fuel\Migrations;

class Create_admins_table
{
	public function up()
	{
		\DBUtil::create_table('admins', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true),
			'username' => array('type' => 'varchar', 'constraint' => 50),
			'email' => array('type' => 'varchar', 'constraint' => 100),
			'password' => array('type' => 'varchar', 'constraint' => 255),
			'full_name' => array('type' => 'varchar', 'constraint' => 100, 'null' => true),
			'is_active' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 1),
			'last_login' => array('type' => 'datetime', 'null' => true),
			'google_id' => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
			'created_at' => array('type' => 'datetime'),
			'updated_at' => array('type' => 'datetime'),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('admins');
	}
}