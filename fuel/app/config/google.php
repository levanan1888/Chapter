<?php

/**
 * Google OAuth Configuration
 * 
 * Cấu hình cho Google OAuth login
 * 
 * @package    App
 * @subpackage Config
 */

return array(
	/**
	 * Google OAuth Client ID
	 * Lấy từ Google Cloud Console
	 */
	'client_id' => '474636792697-5oc8o8b6ndlcnkvi15pldl3nut9rqrgd.apps.googleusercontent.com',
	
	/**
	 * Google OAuth Client Secret
	 * Lấy từ Google Cloud Console
	 */
	'client_secret' => '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',
	
	/**
	 * Redirect URI sau khi xác thực Google
	 * Thay đổi URL này nếu bạn chạy trên domain khác
	 */
	'redirect_uri' => 'http://localhost/project-story/user/google_callback',
	
	/**
	 * Các scope cần thiết cho Google OAuth
	 */
	'scopes' => array(
		'https://www.googleapis.com/auth/userinfo.email',
		'https://www.googleapis.com/auth/userinfo.profile'
	),
	
	/**
	 * URL Google OAuth
	 */
	'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
	
	/**
	 * URL để lấy access token
	 */
	'token_url' => 'https://oauth2.googleapis.com/token',
	
	/**
	 * URL để lấy thông tin user
	 */
	'userinfo_url' => 'https://www.googleapis.com/oauth2/v2/userinfo'
);
