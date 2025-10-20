<?php

/**
 * Google OAuth Service
 * 
 * Xử lý logic Google OAuth authentication
 * 
 * @package    App
 * @subpackage Service
 */
class Service_Googleoauth
{
	/**
	 * Cấu hình Google OAuth
	 * 
	 * @var array
	 */
	protected $config;
	
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$this->config = Config::load('google');
	}
	
	/**
	 * Tạo URL để redirect đến Google OAuth
	 * 
	 * @return string
	 */
	public function get_auth_url()
	{
		$params = array(
			'client_id' => $this->config['client_id'],
			'redirect_uri' => $this->config['redirect_uri'],
			'scope' => implode(' ', $this->config['scopes']),
			'response_type' => 'code',
			'access_type' => 'offline',
			'state' => $this->generate_state()
		);
		
		// Lưu state vào session để verify
		Session::set('google_oauth_state', $params['state']);
		
		return $this->config['auth_url'] . '?' . http_build_query($params);
	}
	
	/**
	 * Xử lý callback từ Google OAuth
	 * 
	 * @param string $code Authorization code từ Google
	 * @param string $state State parameter để verify
	 * @return array|false Thông tin user hoặc false nếu lỗi
	 */
	public function handle_callback($code, $state)
	{
		// Verify state parameter
		$session_state = Session::get('google_oauth_state');
		if (!$session_state || $session_state !== $state) {
			return false;
		}
		
		// Xóa state khỏi session
		Session::delete('google_oauth_state');
		
		// Lấy access token
		$token_data = $this->get_access_token($code);
		if (!$token_data) {
			return false;
		}
		
		// Lấy thông tin user
		$user_info = $this->get_user_info($token_data['access_token']);
		if (!$user_info) {
			return false;
		}
		
		return $user_info;
	}
	
	/**
	 * Lấy access token từ authorization code
	 * 
	 * @param string $code Authorization code
	 * @return array|false Token data hoặc false nếu lỗi
	 */
	protected function get_access_token($code)
	{
		$data = array(
			'client_id' => $this->config['client_id'],
			'client_secret' => $this->config['client_secret'],
			'code' => $code,
			'grant_type' => 'authorization_code',
			'redirect_uri' => $this->config['redirect_uri']
		);
		
		$response = $this->make_http_request($this->config['token_url'], $data);
		
		if ($response && isset($response['access_token'])) {
			return $response;
		}
		
		return false;
	}
	
	/**
	 * Lấy thông tin user từ Google API
	 * 
	 * @param string $access_token Access token
	 * @return array|false User info hoặc false nếu lỗi
	 */
	protected function get_user_info($access_token)
	{
		$headers = array(
			'Authorization: Bearer ' . $access_token
		);
		
		$response = $this->make_http_request($this->config['userinfo_url'], null, $headers);
		
		if ($response && isset($response['email'])) {
			return $response;
		}
		
		return false;
	}
	
	/**
	 * Tạo state parameter ngẫu nhiên
	 * 
	 * @return string
	 */
	protected function generate_state()
	{
		return bin2hex(random_bytes(16));
	}
	
	/**
	 * Thực hiện HTTP request
	 * 
	 * @param string $url URL
	 * @param array|null $post_data POST data
	 * @param array $headers HTTP headers
	 * @return array|false Response data hoặc false nếu lỗi
	 */
	protected function make_http_request($url, $post_data = null, $headers = array())
	{
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		
		if ($post_data) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
		}
		
		if (!empty($headers)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($http_code === 200 && $response) {
			return json_decode($response, true);
		}
		
		return false;
	}
}
