<?php

/**
 * Migration: Add google_id column to admins table
 * 
 * @package    App
 * @subpackage Migration
 */
class Migration_Add_Google_Id_To_Admins
{
	/**
	 * Up migration
	 * 
	 * @return void
	 */
	public function up()
	{
		\DB::query("ALTER TABLE admins ADD COLUMN google_id VARCHAR(255) NULL AFTER last_login")->execute();
	}

	/**
	 * Down migration
	 * 
	 * @return void
	 */
	public function down()
	{
		\DB::query("ALTER TABLE admins DROP COLUMN google_id")->execute();
	}
}
