<?php

namespace Fuel\Migrations;

class Update_stories_visibility_for_cascade
{
	public function up()
	{
		// Đảm bảo tất cả truyện có is_visible = 1 (hiển thị) nếu chưa được set
		\DB::query("UPDATE stories SET is_visible = 1 WHERE is_visible IS NULL")->execute();
		
		// Log migration
		\Log::info('Migration 014: Updated stories visibility for cascade effect');
	}

	public function down()
	{
		// Không cần rollback vì đây chỉ là cập nhật dữ liệu
		\Log::info('Migration 014 down: No rollback needed for visibility update');
	}
}
