<?php

/**
 * Base Admin Controller
 * 
 * Controller cơ sở cho tất cả admin controllers
 * Chứa các phương thức chung như authentication, authorization
 * 
 * @package    App
 * @subpackage Controller\Admin
 */
abstract class Controller_Admin_Base extends Controller
{
	/**
	 * Kiểm tra admin đã đăng nhập chưa
	 * 
	 * @return bool
	 */
	protected function is_logged_in()
	{
		return Session::get('admin_id') !== null;
	}

	/**
	 * Redirect đến trang đăng nhập nếu chưa đăng nhập
	 * 
	 * @return void
	 */
	protected function require_login()
	{
		if (!$this->is_logged_in()) {
			Response::redirect('admin/login');
		}
	}

	/**
	 * Redirect đến dashboard nếu đã đăng nhập
	 * 
	 * @return void
	 */
	protected function redirect_if_logged_in()
	{
		if ($this->is_logged_in()) {
			Response::redirect('admin/dashboard');
		}
	}

	/**
	 * Lấy thông tin admin hiện tại
	 * 
	 * @return Model_Admin|null
	 */
	protected function get_current_admin()
	{
		$admin_id = Session::get('admin_id');
		if ($admin_id) {
			return Model_Admin::find($admin_id);
		}
		return null;
	}

	/**
	 * Tạo JSON response
	 * 
	 * @param array $data Dữ liệu response
	 * @param int $http_code HTTP status code
	 * @return Response
	 */
	protected function json_response($data, $http_code = 200)
	{
		$response_obj = Response::forge(json_encode($data));
		$response_obj->set_header('Content-Type', 'application/json');
		$response_obj->set_status($http_code);
		return $response_obj;
	}

	/**
	 * Tạo success response
	 * 
	 * @param string $message Thông báo
	 * @param array $data Dữ liệu bổ sung
	 * @return Response
	 */
	protected function success_response($message, $data = array())
	{
		$response = array(
			'success' => true,
			'message' => $message
		);
		
		if (!empty($data)) {
			$response['data'] = $data;
		}
		
		return $this->json_response($response);
	}

	/**
	 * Tạo error response
	 * 
	 * @param string $message Thông báo lỗi
	 * @param array $data Dữ liệu bổ sung
	 * @param int $http_code HTTP status code
	 * @return Response
	 */
	protected function error_response($message, $data = array(), $http_code = 400)
	{
		$response = array(
			'success' => false,
			'message' => $message
		);
		
		if (!empty($data)) {
			$response['data'] = $data;
		}
		
		return $this->json_response($response, $http_code);
	}

	/**
	 * Validate CSRF token
	 * 
	 * @return bool
	 */
	protected function validate_csrf()
	{
		// FuelPHP tự động validate CSRF token
		return true;
	}

	/**
	 * Kiểm tra quyền truy cập
	 * 
	 * @param string $permission Quyền cần kiểm tra
	 * @return bool
	 */
	protected function has_permission($permission)
	{
		// Hiện tại tất cả admin đều có quyền như nhau
		// Có thể mở rộng sau này với role-based permissions
		return $this->is_logged_in();
	}

	/**
	 * Redirect với flash message
	 * 
	 * @param string $url URL đích
	 * @param string $type Loại message (success, error, info, warning)
	 * @param string $message Nội dung message
	 * @return void
	 */
	protected function redirect_with_message($url, $type, $message)
	{
		Session::set_flash($type, $message);
		Response::redirect($url);
	}

	/**
	 * Lấy flash message
	 * 
	 * @param string $type Loại message
	 * @return string
	 */
	protected function get_flash_message($type)
	{
		return Session::get_flash($type);
	}

	/**
	 * Set flash message
	 * 
	 * @param string $type Loại message
	 * @param string $message Nội dung message
	 * @return void
	 */
	protected function set_flash_message($type, $message)
	{
		Session::set_flash($type, $message);
	}
}
