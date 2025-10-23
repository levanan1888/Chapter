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
		$status = Input::get('status', 'all'); // active | inactive | all
		$sort = Input::get('sort', 'created_at_desc');
		$page = max(1, (int) Input::get('page', 1));
		$perPage = 12;
		$offset = ($page - 1) * $perPage;

		$total = Model_Category::count_filtered($q, $status);
		$data['categories'] = Model_Category::get_categories($q, $status, $perPage, $offset, $sort);

		// Pagination data
		$data['q'] = $q;
		$data['status'] = $status;
		$data['sort'] = $sort;
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
			$slug = Input::post('slug', '');
			$description = Input::post('description', '');
			$color = Input::post('color', '#007bff');
			$sort_order = Input::post('sort_order', 0);
			$is_active = Input::post('is_active', 0);

			// Tải lớp validation tùy chỉnh
			require_once APPPATH . 'classes/validation/custom.php';
			
			// Thiết lập validation
			$validation = Validation_Custom::forge();
			// Thêm trường 'name' với các quy tắc validation:
			// - required: bắt buộc phải có
			// - custom_name: kiểm tra tên cơ bản (không rỗng, tối thiểu 2 ký tự)
			// - custom_category: không được chứa chữ "n"
			$validation->add_field('name', 'Tên danh mục', 'required|custom_name|custom_category');
			$validation->add_field('description', 'Mô tả', 'required|custom_category|custom_name');

			if ($validation->run()) {
				// Tạo slug từ tên nếu không có slug
				if (empty($slug)) {
					$slug = $this->create_slug($name);
				}
				
				// Tạo danh mục mới
				$category_data = array(
					'name' => $name,
					'slug' => $slug,
					'description' => $description,
					'color' => $color,
					'sort_order' => $sort_order,
					'is_active' => $is_active,
				);
				$new_category = Model_Category::create_category($category_data);
				if ($new_category) {
					Session::set_flash('success', 'Thêm danh mục thành công!');
					Response::redirect('admin/categories');
				} else {
					$data['error_message'] = 'Có lỗi xảy ra khi thêm danh mục.';
				}
			} else {
				// Xử lý lỗi validation
				$errors = $validation->error(); // Lấy danh sách lỗi validation
				$error_messages = array(); // Mảng chứa thông báo lỗi
				
				// Duyệt qua từng lỗi validation
				foreach ($errors as $field => $error) {
					if ($field === 'name') { // Nếu lỗi ở trường 'name'
						// Kiểm tra loại lỗi validation cụ thể
						if ($error->rule === 'custom_name') {
							$error_messages[] = 'Tên danh mục không được để trống và phải có ít nhất 2 ký tự.';
						}
						if ($error->rule === 'custom_category') {
							$error_messages[] = 'Tên danh mục chỉ được chứa chữ cái và khoảng trắng.';
						}
					}
					if ($field === 'description') {
						if ($error->rule === 'custom_category') {
							$error_messages[] = 'Mô tả chỉ được chứa chữ cái và khoảng trắng.';
						}
					}
				}
				
				$data['error_message'] = implode(' ', $error_messages);
			}

			// Giữ lại dữ liệu form nếu có lỗi
			if (!empty($data['error_message'])) {
				$data['form_data'] = array(
					'name' => $name,
					'slug' => $slug,
					'description' => $description,
					'color' => $color,
					'sort_order' => $sort_order,
					'is_active' => $is_active,
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
			$slug = Input::post('slug', '');
			$description = Input::post('description', '');
			$color = Input::post('color', '#007bff');
			$sort_order = Input::post('sort_order', 0);
			$is_active = Input::post('is_active', 0);

			// Tải lớp validation tùy chỉnh
			require_once APPPATH . 'classes/validation/custom.php';
			
			// Thiết lập validation
			$validation = Validation_Custom::forge();
			$validation->add_field('name', 'Tên danh mục', 'required|custom_name|custom_category');
			$validation->add_field('description', 'Mô tả', 'custom_category');

			if ($validation->run()) {
				// Tạo slug từ tên nếu không có slug
				if (empty($slug)) {
					$slug = $this->create_slug($name);
				}
				
				// Cập nhật danh mục
				$update_data = array(
					'name' => $name,
					'slug' => $slug,
					'description' => $description,
					'color' => $color,
					'sort_order' => $sort_order,
					'is_active' => $is_active,
				);

				if ($data['category']->update_category($update_data)) {
					Session::set_flash('success', 'Cập nhật danh mục thành công!');
					Response::redirect('admin/categories');
				} else {
					$data['error_message'] = 'Có lỗi xảy ra khi cập nhật danh mục.';
				}
			} else {
				// Xử lý lỗi validation
				$errors = $validation->error(); // Lấy danh sách lỗi validation
				$error_messages = array(); // Mảng chứa thông báo lỗi
				
				// Duyệt qua từng lỗi validation
				foreach ($errors as $field => $error) {
					if ($field === 'name') {
						if ($error->rule === 'custom_name') {
							$error_messages[] = 'Tên danh mục không được để trống và phải có ít nhất 2 ký tự.';
						}
						if ($error->rule === 'custom_category') {
							$error_messages[] = 'Tên danh mục chỉ được chứa chữ cái và khoảng trắng.';
						}
					}
					if ($field === 'description') {
						if ($error->rule === 'custom_category') {
							$error_messages[] = 'Mô tả chỉ được chứa chữ cái và khoảng trắng.';
						}
					}
				}
				
				$data['error_message'] = implode(' ', $error_messages);
			}
		}

		$data['title'] = 'Sửa Danh mục';
		$data['content'] = View::forge('admin/content/category_edit', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Xóa danh mục (soft delete)
	 * 
	 * @param int $id ID của danh mục
	 * @return void
	 */
	public function action_delete($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/categories');
		}

		// Kiểm tra CSRF token
		if (!Security::check_token()) {
			Session::set_flash('error', 'Token bảo mật không hợp lệ. Vui lòng thử lại.');
			Response::redirect('admin/categories');
		}

		if (empty($id)) {
			Session::set_flash('error', 'ID không hợp lệ.');
			Response::redirect('admin/categories');
		}

		$category = Model_Category::find($id);
		
		if (!$category) {
			Session::set_flash('error', 'Không tìm thấy danh mục.');
			Response::redirect('admin/categories');
		}

		// Kiểm tra trạng thái danh mục
		if ($category->is_active == 0) {
			Session::set_flash('error', 'Không thể xóa danh mục không hoạt động. Vui lòng kích hoạt danh mục trước khi xóa.');
			Response::redirect('admin/categories');
		}

		if ($category->deleted_at) {
			Session::set_flash('error', 'Danh mục đã bị xóa trước đó.');
			Response::redirect('admin/categories');
		}

		if ($category->soft_delete()) {
			Session::set_flash('success', 'Xóa danh mục thành công!');
			Response::redirect('admin/categories');
		} else {
			Session::set_flash('error', 'Có lỗi xảy ra khi xóa danh mục.');
			Response::redirect('admin/categories');
		}
	}

	/**
	 * Toggle trạng thái danh mục (AJAX)
	 * 
	 * @param int $id ID của danh mục
	 * @return Response
	 */
	public function action_toggle_status($id = null)
	{
		$this->require_login();

		// Chỉ cho phép POST request
		if (Input::method() !== 'POST') {
			return $this->error_response('Method not allowed', 405);
		}

		// Kiểm tra CSRF token
		if (!Security::check_token()) {
			return $this->error_response('Invalid CSRF token', 403);
		}

		if (empty($id)) {
			return $this->error_response('ID không hợp lệ.', 400);
		}

		$category = Model_Category::find($id);
		
		if (!$category) {
			return $this->error_response('Không tìm thấy danh mục.', 404);
		}

		if ($category->deleted_at) {
			return $this->error_response('Không thể thay đổi trạng thái danh mục đã bị xóa.', 400);
		}

		$is_active = (int) Input::post('is_active', 0);
		
		// Cập nhật trạng thái danh mục
		$result = $category->update_category(array('is_active' => $is_active));
		
		if ($result) {
			// Tạo CSRF token mới sau khi xử lý thành công
			$new_csrf_token = Security::fetch_token();
			
			$data = array(
				'id' => $category->id,
				'is_active' => $is_active,
				'csrf_token' => $new_csrf_token
			);
			
			return $this->success_response($is_active ? 'Danh mục đã được kích hoạt.' : 'Danh mục đã được ẩn.', $data);
		} else {
			return $this->error_response('Có lỗi xảy ra khi cập nhật trạng thái danh mục.', 500);
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
		$search = Input::get('search', '');
		$sort = Input::get('sort', 'deleted_at_desc');
		$page = max(1, (int) Input::get('page', 1));
		$perPage = 10;
		$offset = ($page - 1) * $perPage;

		$total = Model_Category::count_deleted($search);
		$data['categories'] = Model_Category::get_deleted_categories($perPage, $offset, $search, $sort);
		\Log::info("Found " . count($data['categories']) . " deleted categories for trash view");

		// Pagination data
		$data['search'] = $search;
		$data['sort'] = $sort;
		$data['current_page'] = $page;
		$data['per_page'] = $perPage;
		$data['total'] = $total;
		$data['total_pages'] = (int) ceil($total / $perPage);

		$data['title'] = 'Thùng rác - Danh mục';
		$data['content'] = View::forge('admin/content/categories_trash', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Xem chi tiết danh mục
	 * 
	 * @param int $id ID của danh mục
	 * @return void
	 */
	public function action_view($id = null)
	{
		$this->require_login();

		if (empty($id)) {
			Response::redirect('admin/categories');
		}

		$category = Model_Category::find($id);
		if (!$category) {
			Session::set_flash('error', 'Không tìm thấy danh mục.');
			Response::redirect('admin/categories');
		}

		$data = array();
		$data['category'] = $category;
		
		// Lấy danh sách truyện của danh mục
		$data['stories'] = $category->get_stories();
		$data['story_count'] = count($data['stories']);

		$data['title'] = 'Chi tiết Danh mục - ' . $category->name;
		$data['content'] = View::forge('admin/content/category_view', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Khôi phục danh mục từ thùng rác
	 * 
	 * @param int $id ID của danh mục
	 * @return void
	 */
	public function action_restore($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/categories/trash');
		}

		if (empty($id)) {
			Session::set_flash('error', 'ID không hợp lệ.');
			Response::redirect('admin/categories/trash');
		}

		$category = Model_Category::find_deleted($id);
		
		if (!$category) {
			Session::set_flash('error', 'Không tìm thấy danh mục trong thùng rác.');
			Response::redirect('admin/categories/trash');
		}

		if ($category->restore()) {
			Session::set_flash('success', 'Khôi phục danh mục thành công!');
			Response::redirect('admin/categories/trash');
		} else {
			Session::set_flash('error', 'Có lỗi xảy ra khi khôi phục danh mục.');
			Response::redirect('admin/categories/trash');
		}
	}

	/**
	 * Xóa vĩnh viễn danh mục
	 * 
	 * @param int $id ID của danh mục
	 * @return void
	 */
	public function action_force_delete($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/categories/trash');
		}

		if (empty($id)) {
			Session::set_flash('error', 'ID không hợp lệ.');
			Response::redirect('admin/categories/trash');
		}

		$category = Model_Category::find_deleted($id);
		
		if (!$category) {
			Session::set_flash('error', 'Không tìm thấy danh mục trong thùng rác.');
			Response::redirect('admin/categories/trash');
		}

		if ($category->force_delete()) {
			Session::set_flash('success', 'Xóa vĩnh viễn danh mục thành công!');
			Response::redirect('admin/categories/trash');
		} else {
			Session::set_flash('error', 'Có lỗi xảy ra khi xóa vĩnh viễn danh mục.');
			Response::redirect('admin/categories/trash');
		}
	}

	/**
	 * Tạo slug từ tên danh mục
	 * 
	 * @param string $name Tên danh mục
	 * @return string Slug
	 */
	private function create_slug($name)
	{
		// Chuyển về chữ thường
		$slug = strtolower($name);
		
		// Loại bỏ dấu tiếng Việt
		$slug = str_replace(
			array('á', 'à', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ',
				  'é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ',
				  'í', 'ì', 'ỉ', 'ĩ', 'ị',
				  'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ',
				  'ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự',
				  'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ',
				  'đ'),
			array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
				  'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
				  'i', 'i', 'i', 'i', 'i',
				  'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
				  'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
				  'y', 'y', 'y', 'y', 'y',
				  'd'),
			$slug
		);
		
		// Thay thế khoảng trắng và ký tự đặc biệt bằng dấu gạch ngang
		$slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
		
		// Loại bỏ dấu gạch ngang ở đầu và cuối
		$slug = trim($slug, '-');
		
		return $slug;
	}

	/**
	 * Xóa hàng loạt danh mục
	 * 
	 * @return void
	 */
	public function action_bulk_delete()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/categories');
		}

		$category_ids = Input::post('category_ids', array());
		
		if (empty($category_ids)) {
			Session::set_flash('error', 'Vui lòng chọn ít nhất một danh mục để xóa.');
			Response::redirect('admin/categories');
		}

		$deleted_count = 0;
		$error_count = 0;
		$inactive_count = 0;

		foreach ($category_ids as $id) {
			$category = Model_Category::find($id);
			if ($category && !$category->deleted_at) {
				// Kiểm tra trạng thái danh mục
				if ($category->is_active == 0) {
					$inactive_count++;
					continue;
				}
				
				if ($category->soft_delete()) {
					$deleted_count++;
				} else {
					$error_count++;
				}
			} else {
				$error_count++;
			}
		}

		if ($deleted_count > 0) {
			Session::set_flash('success', "Đã xóa thành công {$deleted_count} danh mục.");
		}
		
		if ($inactive_count > 0) {
			Session::set_flash('warning', "Có {$inactive_count} danh mục không hoạt động không thể xóa. Vui lòng kích hoạt trước khi xóa.");
		}
		
		if ($error_count > 0) {
			Session::set_flash('error', "Có {$error_count} danh mục không thể xóa.");
		}

		Response::redirect('admin/categories');
	}

	/**
	 * Khôi phục hàng loạt danh mục
	 * 
	 * @return void
	 */
	public function action_bulk_restore()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/categories/trash');
		}

		$category_ids = Input::post('category_ids', array());
		
		\Log::info("Bulk restore category IDs: " . implode(', ', $category_ids));
		
		if (empty($category_ids)) {
			Session::set_flash('error', 'Vui lòng chọn ít nhất một danh mục để khôi phục.');
			Response::redirect('admin/categories/trash');
		}

		$restored_count = 0;
		$error_count = 0;

		foreach ($category_ids as $id) {
			$category = Model_Category::find_deleted($id);
			\Log::info("Restore category ID: {$id}, Found: " . ($category ? 'Yes' : 'No') . ", Deleted_at: " . ($category ? $category->deleted_at : 'N/A'));
			if ($category && $category->deleted_at) {
				if ($category->restore()) {
					$restored_count++;
				} else {
					$error_count++;
				}
			} else {
				$error_count++;
			}
		}

		if ($restored_count > 0) {
			Session::set_flash('success', "Đã khôi phục thành công {$restored_count} danh mục.");
		}
		
		if ($error_count > 0) {
			Session::set_flash('error', "Có {$error_count} danh mục không thể khôi phục.");
		}

		Response::redirect('admin/categories/trash');
	}

	/**
	 * Xóa vĩnh viễn hàng loạt danh mục
	 * 
	 * @return void
	 */
	public function action_bulk_force_delete()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/categories/trash');
		}

		$category_ids = Input::post('category_ids', array());
		
		\Log::info("Bulk force delete category IDs: " . implode(', ', $category_ids));
		
		if (empty($category_ids)) {
			Session::set_flash('error', 'Vui lòng chọn ít nhất một danh mục để xóa vĩnh viễn.');
			Response::redirect('admin/categories/trash');
		}

		$deleted_count = 0;
		$error_count = 0;

		foreach ($category_ids as $id) {
			$category = Model_Category::find_deleted($id);
			\Log::info("Force delete category ID: {$id}, Found: " . ($category ? 'Yes' : 'No') . ", Deleted_at: " . ($category ? $category->deleted_at : 'N/A') . ", Is_active: " . ($category ? $category->is_active : 'N/A'));
			
			if ($category && $category->deleted_at !== null && $category->deleted_at !== '') {
				if ($category->force_delete()) {
					$deleted_count++;
					\Log::info("Successfully force deleted category ID: {$id}");
				} else {
					$error_count++;
					\Log::error("Failed to force delete category ID: {$id}");
				}
			} else {
				$error_count++;
				\Log::warning("Category ID {$id} not found or not deleted: " . ($category ? "deleted_at = '{$category->deleted_at}', is_active = {$category->is_active}" : "not found"));
			}
		}

		if ($deleted_count > 0) {
			Session::set_flash('success', "Đã xóa vĩnh viễn thành công {$deleted_count} danh mục.");
		}
		
		if ($error_count > 0) {
			Session::set_flash('error', "Có {$error_count} danh mục không thể xóa vĩnh viễn.");
		}

		Response::redirect('admin/categories/trash');
	}
}

