<?php

/**
 * Admin Dashboard Controller
 * 
 * Xử lý dashboard admin với thống kê và quản lý tổng quan
 * 
 * @package    App
 * @subpackage Controller\Admin
 */
class Controller_Admin_Dashboard extends Controller_Admin_Base
{
	/**
	 * Dashboard admin
	 * 
	 * @return void
	 */
	public function action_index()
	{
		// Kiểm tra đăng nhập
		$this->require_login();

		$data = array();
		
		// Lấy thông tin admin hiện tại
		$admin_id = Session::get('admin_id');
		$data['admin'] = Model_Admin::find($admin_id);

		// Thống kê cơ bản
		$data['total_admins'] = Model_Admin::count_all();
		$data['recent_admins'] = Model_Admin::get_all_admins(5);

		// Thống kê truyện tranh
		$data['total_stories'] = Model_Story::count_all();
		$data['total_chapters'] = Model_Chapter::count_all();
		$data['total_categories'] = Model_Category::count_all();
		$data['total_authors'] = Model_Author::count_all();

		// Truyện mới nhất
		$data['latest_stories'] = Model_Story::get_latest_stories(5);
		
		// Truyện hot
		$data['hot_stories'] = Model_Story::get_hot_stories(5);
		
		// Truyện được xem nhiều nhất
		$data['most_viewed_stories'] = Model_Story::get_most_viewed_stories(5);

		$data['title'] = 'Dashboard Admin';
		$data['content'] = View::forge('admin/dashboard/index', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Trang chủ admin (redirect đến dashboard)
	 * 
	 * @return void
	 */
	public function action_home()
	{
		if ($this->is_logged_in()) {
			Response::redirect('admin/dashboard');
		} else {
			Response::redirect('admin/login');
		}
	}

	/**
	 * API lấy thống kê dashboard (AJAX)
	 * 
	 * @return Response
	 */
	public function action_api_stats()
	{
		$this->require_login();

		$stats = array(
			'total_stories' => Model_Story::count_all(),
			'total_chapters' => Model_Chapter::count_all(),
			'total_categories' => Model_Category::count_all(),
			'total_authors' => Model_Author::count_all(),
			'total_admins' => Model_Admin::count_all(),
		);

		return $this->success_response('Thống kê được tải thành công', $stats);
	}

	/**
	 * API lấy truyện mới nhất (AJAX)
	 * 
	 * @return Response
	 */
	public function action_api_latest_stories()
	{
		$this->require_login();

		$limit = Input::get('limit', 5);
		$stories = Model_Story::get_latest_stories($limit);

		return $this->success_response('Danh sách truyện mới nhất', $stories);
	}

	/**
	 * API lấy truyện hot (AJAX)
	 * 
	 * @return Response
	 */
	public function action_api_hot_stories()
	{
		$this->require_login();

		$limit = Input::get('limit', 5);
		$stories = Model_Story::get_hot_stories($limit);

		return $this->success_response('Danh sách truyện hot', $stories);
	}

	/**
	 * API lấy truyện được xem nhiều nhất (AJAX)
	 * 
	 * @return Response
	 */
	public function action_api_most_viewed_stories()
	{
		$this->require_login();

		$limit = Input::get('limit', 5);
		$stories = Model_Story::get_most_viewed_stories($limit);

		return $this->success_response('Danh sách truyện được xem nhiều nhất', $stories);
	}
}
