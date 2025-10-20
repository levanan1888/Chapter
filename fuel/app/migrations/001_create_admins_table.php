<?php

/**
 * Migration: Create admins table
 * 
 * @package    App
 * @subpackage Migration
 */
class Migration_Create_Admins_Table
{
	/**
	 * Up migration
	 * 
	 * @return void
	 */
	public function up()
	{
		\DB::query("CREATE TABLE IF NOT EXISTS admins (
			id INT AUTO_INCREMENT PRIMARY KEY,
			username VARCHAR(50) NOT NULL UNIQUE,
			email VARCHAR(100) NOT NULL UNIQUE,
			password VARCHAR(255) NOT NULL,
			full_name VARCHAR(100) NULL,
			is_active TINYINT(1) DEFAULT 1,
			last_login DATETIME NULL,
			google_id VARCHAR(255) NULL,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL
		)")->execute();
	}

	/**
	 * Down migration
	 * 
	 * @return void
	 */
	public function down()
	{
		\DB::query("DROP TABLE IF EXISTS admins")->execute();
	}
}