<?php

/**
 * Migration: Add deleted_at column to admins table for soft delete
 * 
 * @package    App
 * @subpackage Migration
 */
class Migration_Add_Deleted_At_To_Admins
{
	/**
	 * Up migration
	 * 
	 * @return void
	 */
	public function up()
	{
		\DB::query("ALTER TABLE admins ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL")->execute();
	}

	/**
	 * Down migration
	 * 
	 * @return void
	 */
	public function down()
	{
		\DB::query("ALTER TABLE admins DROP COLUMN deleted_at")->execute();
	}
}
