<?php

/**
 * Admin Category Controller
 * 
 * Xử lý quản lý danh mục (CRUD)
 * 
 * @package    App
 * @subpackage Controller\Admin
 */
class Controller_Admin_Category extends Controller_Admin_Base
{
	/**
	 * Quản lý danh mục
	 * 
	 * @return void
	 */
	public function action_index()
	{
		$this->require_login();

		$data = array();

		// Input filters
		$q = Input::get('q', '');
		$status = Input::get('status', 'active'); // active | inactive | all
		$page = max(1, (int) Input::get('page', 1));
		$perPage = 12;
		$offset = ($page - 1) * $perPage;

		$total = Model_Category::count_filtered($q, $status);
		$data['categories'] = Model_Category::get_categories($q, $status, $perPage, $offset);
		// Tính story_count tương tự màn tác giả
		
		if (!empty($data['categories'])) {
			foreach ($data['categories'] as &$category) {
				if (is_array($category)) {
					$category = (object) $category; // normalize to object for views
				}
				$category->story_count = isset($category->id)
					? (is_callable([$category, 'get_story_count']) ? $category->get_story_count() : Model_Category::get_story_count_by_id($category->id))
					: 0;
			}
			unset($category);
		}

		// Pagination data
		$data['q'] = $q;
		$data['status'] = $status;
		$data['page'] = $page;
		$data['per_page'] = $perPage;
		$data['total'] = $total;
		$data['total_pages'] = (int) ceil($total / $perPage);

		$data['title'] = 'Quản lý Danh mục';
		$data['content'] = View::forge('admin/content/categories', $data, false);
		// dd($data);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Thêm danh mục mới
	 * 
	 * @return void
	 */
	public function action_add()
	{
		$this->require_login();

		$data = array();
		$data['success_message'] = '';
		$data['error_message'] = '';

		// Xử lý form thêm danh mục
		if (Input::method() === 'POST') {
			$name = Input::post('name', '');
			$description = Input::post('description', '');
			$color = Input::post('color', '#007bff');
			$sort_order = Input::post('sort_order', 0);

			// Kiểm tra dữ liệu đầu vào
			if (empty($name)) {
				$data['error_message'] = 'Vui lòng nhập tên danh mục.';
			} else {
				// Tạo danh mục mới
				$category_data = array(
					'name' => $name,
					'description' => $description,
					'color' => $color,
					'sort_order' => $sort_order,
				);

				$new_category = Model_Category::create_category($category_data);
				if ($new_category) {
					$data['success_message'] = 'Thêm danh mục thành công!';
					// Reset form
					$data['form_data'] = array();
				} else {
					$data['error_message'] = 'Có lỗi xảy ra khi thêm danh mục.';
				}
			}

			// Giữ lại dữ liệu form nếu có lỗi
			if (!empty($data['error_message'])) {
				$data['form_data'] = array(
					'name' => $name,
					'description' => $description,
					'color' => $color,
					'sort_order' => $sort_order,
				);
			}
		}

		$data['title'] = 'Thêm Danh mục';
		$data['content'] = View::forge('admin/content/category_add', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Sửa danh mục
	 * 
	 * @param int $id ID của danh mục
	 * @return void
	 */
	public function action_edit($id = null)
	{
		$this->require_login();

		if (empty($id)) {
			Response::redirect('admin/categories');
		}

		$data = array();
		$data['success_message'] = '';
		$data['error_message'] = '';
		$data['category'] = Model_Category::find($id);

		if (!$data['category']) {
			Session::set_flash('error', 'Không tìm thấy danh mục.');
			Response::redirect('admin/categories');
		}

		// Xử lý form sửa danh mục
		if (Input::method() === 'POST') {
			$name = Input::post('name', '');
			$description = Input::post('description', '');
			$color = Input::post('color', '#007bff');
			$sort_order = Input::post('sort_order', 0);

			// Kiểm tra dữ liệu đầu vào
			if (empty($name)) {
				$data['error_message'] = 'Vui lòng nhập tên danh mục.';
			} else {
				// Cập nhật danh mục
				$update_data = array(
					'name' => $name,
					'description' => $description,
					'color' => $color,
					'sort_order' => $sort_order,
				);

				if ($data['category']->update_category($update_data)) {
					$data['success_message'] = 'Cập nhật danh mục thành công!';
					// Reload data
					$data['category'] = Model_Category::find($id);
				} else {
					$data['error_message'] = 'Có lỗi xảy ra khi cập nhật danh mục.';
				}
			}
		}

		$data['title'] = 'Sửa Danh mục';
		$data['content'] = View::forge('admin/content/category_edit', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Xóa danh mục (AJAX)
	 * 
	 * @param int $id ID của danh mục
	 * @return Response
	 */
	public function action_delete($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/categories');
		}

		if (empty($id)) {
			return $this->error_response('ID không hợp lệ.');
		}

		$category = Model_Category::find($id);
		
		if (!$category) {
			return $this->error_response('Không tìm thấy danh mục.');
		}

		if ($category->soft_delete()) {
			return $this->success_response('Xóa danh mục thành công!');
		} else {
			return $this->error_response('Có lỗi xảy ra khi xóa danh mục.');
		}
	}

	/**
	 * API lấy danh sách danh mục (AJAX)
	 * 
	 * @return Response
	 */
	public function action_api_list()
	{
		$this->require_login();

		$categories = Model_Category::get_all_categories();

		return $this->success_response('Danh sách danh mục', $categories);
	}

	/**
	 * API tạo danh mục nhanh (AJAX)
	 * 
	 * @return Response
	 */
	public function action_api_create()
	{
		$this->require_login();

		$name = Input::post('name', '');
		$description = Input::post('description', '');
		$color = Input::post('color', '#007bff');

		if (empty($name)) {
			return $this->error_response('Vui lòng nhập tên danh mục.');
		}

		$category_data = array(
			'name' => $name,
			'description' => $description,
			'color' => $color,
		);

		$new_category = Model_Category::create_category($category_data);
		if ($new_category) {
			return $this->success_response('Tạo danh mục thành công!', $new_category);
		} else {
			return $this->error_response('Có lỗi xảy ra khi tạo danh mục.');
		}
	}

	/**
	 * API cập nhật thứ tự danh mục (AJAX)
	 * 
	 * @return Response
	 */
	public function action_api_update_order()
	{
		$this->require_login();

		$category_orders = Input::post('category_orders', array());

		if (empty($category_orders) || !is_array($category_orders)) {
			return $this->error_response('Dữ liệu không hợp lệ.');
		}

		$success_count = 0;
		foreach ($category_orders as $category_id => $sort_order) {
			$category = Model_Category::find($category_id);
			if ($category) {
				if ($category->update_category(array('sort_order' => $sort_order))) {
					$success_count++;
				}
			}
		}

		if ($success_count > 0) {
			return $this->success_response("Cập nhật thứ tự {$success_count} danh mục thành công!");
		} else {
			return $this->error_response('Không thể cập nhật thứ tự danh mục.');
		}
	}

	/**
	 * Quản lý thùng rác danh mục
	 * 
	 * @return void
	 */
	public function action_trash()
	{
		$this->require_login();

		$data = array();

		// Input filters
		$q = Input::get('q', '');
		$page = max(1, (int) Input::get('page', 1));
		$perPage = 12;
		$offset = ($page - 1) * $perPage;

		$total = Model_Category::count_deleted();
		$data['categories'] = Model_Category::get_deleted_categories($perPage, $offset);

		// Pagination data
		$data['q'] = $q;
		$data['page'] = $page;
		$data['per_page'] = $perPage;
		$data['total'] = $total;
		$data['total_pages'] = (int) ceil($total / $perPage);

		$data['title'] = 'Thùng rác - Danh mục';
		$data['content'] = View::forge('admin/content/categories_trash', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Khôi phục danh mục từ thùng rác (AJAX)
	 * 
	 * @param int $id ID của danh mục
	 * @return Response
	 */
	public function action_restore($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/categories');
		}

		if (empty($id)) {
			return $this->error_response('ID không hợp lệ.');
		}

		$category = Model_Category::find_deleted($id);
		
		if (!$category) {
			return $this->error_response('Không tìm thấy danh mục trong thùng rác.');
		}

		if ($category->restore()) {
			return $this->success_response('Khôi phục danh mục thành công!');
		} else {
			return $this->error_response('Có lỗi xảy ra khi khôi phục danh mục.');
		}
	}

	/**
	 * Xóa vĩnh viễn danh mục (AJAX)
	 * 
	 * @param int $id ID của danh mục
	 * @return Response
	 */
	public function action_force_delete($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/categories');
		}

		if (empty($id)) {
			return $this->error_response('ID không hợp lệ.');
		}

		$category = Model_Category::find_deleted($id);
		
		if (!$category) {
			return $this->error_response('Không tìm thấy danh mục trong thùng rác.');
		}

		if ($category->force_delete()) {
			return $this->success_response('Xóa vĩnh viễn danh mục thành công!');
		} else {
			return $this->error_response('Có lỗi xảy ra khi xóa vĩnh viễn danh mục.');
		}
	}
}
