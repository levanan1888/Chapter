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
		
		// Thống kê lượt xem và trạng thái truyện
		$data['total_views'] = Model_Story::get_total_views();
		$data['active_stories'] = Model_Story::count_active();
		$data['featured_stories'] = Model_Story::count_featured();
		$data['hot_stories_count'] = Model_Story::count_hot();

		// Truyện mới nhất
		$data['latest_stories'] = Model_Story::get_latest_stories(5);
		
		// Truyện hot
		$data['hot_stories'] = Model_Story::get_hot_stories(5);
		
		// Truyện được xem nhiều nhất
		$data['most_viewed_stories'] = Model_Story::get_most_viewed_stories(5);

		// Dữ liệu cho biểu đồ
		$data['chart_data'] = $this->get_chart_data();

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

	/**
	 * Lấy dữ liệu cho biểu đồ
	 * 
	 * @return array
	 */
	private function get_chart_data()
	{
		$chart_data = array();

		// Dữ liệu truyện theo tháng (7 tháng gần nhất)
		$chart_data['stories_by_month'] = $this->get_stories_by_month();
		
		// Dữ liệu chương theo tháng (7 tháng gần nhất)
		$chart_data['chapters_by_month'] = $this->get_chapters_by_month();
		
		
		// Dữ liệu lượt xem truyện top 5
		$chart_data['top_viewed_stories'] = $this->get_top_viewed_stories();
		
		// Dữ liệu lượt xem theo tháng
		$chart_data['views_by_month'] = $this->get_views_by_month();
		
		// Dữ liệu truyện theo trạng thái
		$chart_data['story_status_stats'] = $this->get_story_status_stats();
		
		// Dữ liệu truyện theo danh mục
		$chart_data['stories_by_category'] = $this->get_stories_by_category();

		return $chart_data;
	}

	/**
	 * Lấy số lượng truyện theo tháng
	 * 
	 * @return array
	 */
	private function get_stories_by_month()
	{
		try {
			$query = \DB::query("
				SELECT 
					DATE_FORMAT(created_at, '%Y-%m') as month,
					COUNT(*) as count
				FROM stories 
				WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 MONTH)
				GROUP BY DATE_FORMAT(created_at, '%Y-%m')
				ORDER BY month ASC
			");
			$results = $query->execute();
			
			$data = array();
			foreach ($results as $result) {
				$data[] = array(
					'month' => $result['month'],
					'count' => (int) $result['count']
				);
			}
			
			return $data;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Lấy số lượng chương theo tháng
	 * 
	 * @return array
	 */
	private function get_chapters_by_month()
	{
		try {
			$query = \DB::query("
				SELECT 
					DATE_FORMAT(created_at, '%Y-%m') as month,
					COUNT(*) as count
				FROM chapters 
				WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 MONTH)
				AND deleted_at IS NULL
				GROUP BY DATE_FORMAT(created_at, '%Y-%m')
				ORDER BY month ASC
			");
			$results = $query->execute();
			
			$data = array();
			foreach ($results as $result) {
				$data[] = array(
					'month' => $result['month'],
					'count' => (int) $result['count']
				);
			}
			
			return $data;
		} catch (\Exception $e) {
			return array();
		}
	}


	/**
	 * Lấy top truyện được xem nhiều nhất
	 * 
	 * @return array
	 */
	private function get_top_viewed_stories()
	{
		try {
			$query = \DB::query("
				SELECT 
					title,
					views
				FROM stories 
				WHERE is_visible = 1
				ORDER BY views DESC
				LIMIT 5
			");
			$results = $query->execute();
			
			$data = array();
			foreach ($results as $result) {
				$data[] = array(
					'title' => $result['title'],
					'views' => (int) $result['views']
				);
			}
			
			return $data;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Lấy số lượng lượt xem theo tháng
	 * 
	 * @return array
	 */
	private function get_views_by_month()
	{
		try {
			$query = \DB::query("
				SELECT 
					DATE_FORMAT(created_at, '%Y-%m') as month,
					SUM(views) as total_views
				FROM stories 
				WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 MONTH)
				GROUP BY DATE_FORMAT(created_at, '%Y-%m')
				ORDER BY month ASC
			");
			$results = $query->execute();
			
			$data = array();
			foreach ($results as $result) {
				$data[] = array(
					'month' => $result['month'],
					'total_views' => (int) $result['total_views']
				);
			}
			
			return $data;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Lấy thống kê truyện theo trạng thái
	 * 
	 * @return array
	 */
	private function get_story_status_stats()
	{
		try {
			$query = \DB::query("
				SELECT 
					CASE 
						WHEN is_visible = 1 THEN 'active'
						ELSE 'inactive'
					END as status,
					COUNT(*) as count
				FROM stories 
				GROUP BY is_visible
			");
			$results = $query->execute();
			
			$data = array();
			foreach ($results as $result) {
				$data[] = array(
					'status' => $result['status'],
					'count' => (int) $result['count']
				);
			}
			
			return $data;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Lấy truyện theo danh mục (top 5)
	 * 
	 * @return array
	 */
	private function get_stories_by_category()
	{
		try {
			$query = \DB::query("
				SELECT 
					c.name,
					COUNT(sc.story_id) as story_count
				FROM categories c
				LEFT JOIN story_categories sc ON c.id = sc.category_id
				LEFT JOIN stories s ON sc.story_id = s.id AND s.is_visible = 1
				WHERE c.is_active = 1
				GROUP BY c.id, c.name
				ORDER BY story_count DESC
				LIMIT 5
			");
			$results = $query->execute();
			
			$data = array();
			foreach ($results as $result) {
				$data[] = array(
					'name' => $result['name'],
					'story_count' => (int) $result['story_count']
				);
			}
			
			return $data;
		} catch (\Exception $e) {
			return array();
		}
	}
}
