<?php

/**
 * Admin User Controller
 * 
 * Xử lý quản lý admin users (CRUD)
 * 
 * @package    App
 * @subpackage Controller\Admin
 */
class Controller_Admin_User extends Controller_Admin_Base
{
	/**
	 * Quản lý admin users
	 * 
	 * @return void
	 */
	public function action_index()
	{
		$this->require_login();

		$data = array();
		
		// Lấy tham số từ URL
		$page = Input::get('page', 1);
		$limit = 20; // Cố định limit như các màn hình khác
		$search = Input::get('search', '');
		$status = Input::get('status', '');
		$sort = Input::get('sort', 'created_at_desc');
		
		$offset = ($page - 1) * $limit;

		// Lấy danh sách admin với filter
		$data['admins'] = Model_Admin::get_all_admins($limit, $offset, $search, $status, $sort);
		$data['total_admins'] = Model_Admin::count_all($search, $status);
		$data['current_page'] = $page;
		$data['total_pages'] = ceil($data['total_admins'] / $limit);
		
		// Truyền tham số filter về view
		$data['search'] = $search;
		$data['status'] = $status;
		$data['sort'] = $sort;

		$data['title'] = 'Quản lý Admin';
		$data['content'] = View::forge('admin/content/manage', $data, false);
	
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Thêm admin mới
	 * 
	 * @return void
	 */
	public function action_add()
	{
		$this->require_login();

		$data = array();
		$data['success_message'] = '';
		$data['error_message'] = '';

		// Xử lý form thêm admin
		if (Input::method() === 'POST') {
			$username = Input::post('username', '');
			$email = Input::post('email', '');
			$password = Input::post('password', '');
			$full_name = Input::post('full_name', '');
			$user_type = Input::post('user_type', 'admin');

			// Kiểm tra dữ liệu đầu vào
			if (empty($username) || empty($email) || empty($password)) {
				\Log::warning('Admin creation failed: Missing required fields - Username: ' . $username . ', Email: ' . $email);
				$data['error_message'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
			} else {
				// Kiểm tra username và email đã tồn tại chưa
				$existing_admin = Model_Admin::find_by_username_or_email($username);
				if ($existing_admin) {
					\Log::warning('Admin creation failed: Username/Email already exists - Username: ' . $username . ', Email: ' . $email);
					$data['error_message'] = 'Username hoặc email đã tồn tại.';
				} else {
					// Tạo admin mới
					$admin_data = array(
						'username' => $username,
						'email' => $email,
						'password' => $password,
						'full_name' => $full_name,
						'user_type' => $user_type,
						'is_active' => 1
					);

					$new_admin = Model_Admin::create_admin($admin_data);
					if ($new_admin) {
						\Log::info('Admin created successfully: ID ' . $new_admin->id . ', Username: ' . $new_admin->username);
						Session::set_flash('success', 'Thêm admin thành công!');
						Response::redirect('admin/users');
					} else {
						\Log::error('Failed to create admin: Username: ' . $username . ', Email: ' . $email);
						$data['error_message'] = 'Có lỗi xảy ra khi thêm admin. Vui lòng thử lại.';
					}
				}
			}

			// Giữ lại dữ liệu form nếu có lỗi
			if (!empty($data['error_message'])) {
				$data['form_data'] = array(
					'username' => $username,
					'email' => $email,
					'full_name' => $full_name,
					'user_type' => $user_type
				);
			}
		}

		$data['title'] = 'Thêm Admin';
		$data['content'] = View::forge('admin/content/add', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Sửa thông tin admin
	 * 
	 * @param int $id ID của admin
	 * @return void
	 */
	public function action_edit($id = null)
	{
		$this->require_login();

		if (empty($id)) {
			Response::redirect('admin/users');
		}

		$data = array();
		$data['success_message'] = '';
		$data['error_message'] = '';
		$data['admin'] = Model_Admin::find($id);

		if (!$data['admin']) {
			Session::set_flash('error', 'Không tìm thấy admin.');
			Response::redirect('admin/users');
		}

		// Xử lý form sửa admin
		if (Input::method() === 'POST') {
			$username = Input::post('username', '');
			$email = Input::post('email', '');
			$password = Input::post('password', '');
			$full_name = Input::post('full_name', '');
			$user_type = Input::post('user_type', 'admin');
			$is_active = Input::post('is_active', 1);

			// Kiểm tra dữ liệu đầu vào
			if (empty($username) || empty($email)) {
				\Log::warning('Admin update failed: Missing required fields - ID: ' . $id . ', Username: ' . $username . ', Email: ' . $email);
				$data['error_message'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
			} else {
				// Kiểm tra username và email đã tồn tại chưa (trừ admin hiện tại)
				$existing_admin = Model_Admin::find_by_username_or_email($username);
				if ($existing_admin && $existing_admin->id != $id) {
					\Log::warning('Admin update failed: Username already exists - ID: ' . $id . ', Username: ' . $username);
					$data['error_message'] = 'Username đã tồn tại.';
				} else {
					$existing_admin = Model_Admin::find_by_username_or_email($email);
					if ($existing_admin && $existing_admin->id != $id) {
						\Log::warning('Admin update failed: Email already exists - ID: ' . $id . ', Email: ' . $email);
						$data['error_message'] = 'Email đã tồn tại.';
					} else {
						// Cập nhật admin
						$update_data = array(
							'username' => $username,
							'email' => $email,
							'full_name' => $full_name,
							'user_type' => $user_type,
							'is_active' => $is_active
						);

						// Chỉ cập nhật mật khẩu nếu có nhập
						if (!empty($password)) {
							$update_data['password'] = $password;
						}

						if ($data['admin']->update_admin($update_data)) {
							\Log::info('Admin updated successfully: ID ' . $id . ', Username: ' . $username);
							Session::set_flash('success', 'Cập nhật admin thành công!');
							Response::redirect('admin/users');
						} else {
							\Log::error('Failed to update admin: ID ' . $id . ', Username: ' . $username);
							$data['error_message'] = 'Có lỗi xảy ra khi cập nhật admin. Vui lòng thử lại.';
						}
					}
				}
			}
		}

		$data['title'] = 'Sửa Admin';
		$data['content'] = View::forge('admin/content/edit', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Xóa admin (soft delete) bằng AJAX
	 * 
	 * @param int $id ID của admin
	 * @return Response
	 */
	public function action_delete($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/users');
		}

		if (empty($id)) {
			return $this->error_response('ID không hợp lệ.');
		}

		$admin = Model_Admin::find($id);
		
		if (!$admin) {
			return $this->error_response('Không tìm thấy admin.');
		}

		// Không cho phép xóa chính mình
		$current_admin_id = Session::get('admin_id');
		if ($admin->id == $current_admin_id) {
			return $this->error_response('Không thể xóa chính mình.');
		}

		if ($admin->soft_delete()) {
			\Log::info('Admin soft deleted successfully: ID ' . $admin->id . ', Username: ' . $admin->username);
			
			// Tạo CSRF token mới sau khi xử lý thành công
			$new_csrf_token = Security::fetch_token();
			
			$data = array(
				'admin_id' => $admin->id,
				'csrf_token' => $new_csrf_token
			);
			
			return $this->success_response('Xóa admin thành công!', $data);
		} else {
			\Log::error('Failed to soft delete admin: ID ' . $admin->id . ', Username: ' . $admin->username);
			return $this->error_response('Có lỗi xảy ra khi xóa admin. Vui lòng thử lại.');
		}
	}

	/**
	 * Danh sách admin đã xóa (soft delete)
	 *
	 * @return void
	 */
	public function action_deleted()
	{
		$this->require_login();

		$data = array();
		$page = Input::get('page', 1);
		$limit = 20; // Cố định limit như các màn hình khác
		$search = Input::get('search', '');
		$sort = Input::get('sort', 'deleted_at_desc');
		$offset = ($page - 1) * $limit;

		$data['admins'] = Model_Admin::get_deleted_admins($limit, $offset, $search, $sort);
		$data['total_admins'] = Model_Admin::count_deleted($search);
		$data['current_page'] = $page;
		$data['total_pages'] = ceil($data['total_admins'] / $limit);
		
		// Truyền tham số filter về view
		$data['search'] = $search;
		$data['sort'] = $sort;

		$data['title'] = 'Admin đã xóa';
		$data['content'] = View::forge('admin/content/deleted', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Khôi phục admin (AJAX)
	 *
	 * @param int $id ID của admin
	 * @return Response
	 */
	public function action_restore($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/users/deleted');
		}

		if (empty($id)) {
			return $this->error_response('ID không hợp lệ.');
		}

		// Lấy admin kể cả đã xóa
		try {
			$query = \DB::query("SELECT * FROM admins WHERE id = :id");
			$result = $query->param('id', $id)->execute();
			if ($result->count() === 0) {
				return $this->error_response('Không tìm thấy admin.');
			} else {
				$data = $result->current();
				$admin = new Model_Admin();
				foreach ($data as $key => $value) {
					$admin->$key = $value;
				}
				if ($admin->restore()) {
					\Log::info('Admin restored successfully: ID ' . $admin->id . ', Username: ' . $admin->username);
					
					// Tạo CSRF token mới sau khi xử lý thành công
					$new_csrf_token = Security::fetch_token();
					
					$data = array(
						'admin_id' => $admin->id,
						'csrf_token' => $new_csrf_token
					);
					
					return $this->success_response('Khôi phục admin thành công!', $data);
				} else {
					\Log::error('Failed to restore admin: ID ' . $admin->id . ', Username: ' . $admin->username);
					return $this->error_response('Khôi phục thất bại. Vui lòng thử lại.');
				}
			}
		} catch (\Exception $e) {
			\Log::error('Exception in admin restore: ' . $e->getMessage() . ' - ID: ' . $id);
			return $this->error_response('Có lỗi xảy ra. Vui lòng thử lại.');
		}
	}

	/**
	 * Xóa vĩnh viễn admin (AJAX)
	 *
	 * @param int $id ID của admin
	 * @return Response
	 */
	public function action_delete_permanent($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/users/deleted');
		}

		if (empty($id)) {
			return $this->error_response('ID không hợp lệ.');
		}

		try {
			$query = \DB::query("SELECT * FROM admins WHERE id = :id");
			$result = $query->param('id', $id)->execute();
			if ($result->count() === 0) {
				return $this->error_response('Không tìm thấy admin.');
			} else {
				$data = $result->current();
				$admin = new Model_Admin();
				foreach ($data as $key => $value) { 
					$admin->$key = $value; 
				}
				if ($admin->hard_delete()) {
					\Log::info('Admin hard deleted successfully: ID ' . $admin->id . ', Username: ' . $admin->username);
					
					// Tạo CSRF token mới sau khi xử lý thành công
					$new_csrf_token = Security::fetch_token();
					
					$data = array(
						'admin_id' => $admin->id,
						'csrf_token' => $new_csrf_token
					);
					
					return $this->success_response('Đã xóa vĩnh viễn!', $data);
				} else {
					\Log::error('Failed to hard delete admin: ID ' . $admin->id . ', Username: ' . $admin->username);
					return $this->error_response('Xóa vĩnh viễn thất bại. Vui lòng thử lại.');
				}
			}
		} catch (\Exception $e) {
			\Log::error('Exception in admin hard delete: ' . $e->getMessage() . ' - ID: ' . $id);
			return $this->error_response('Có lỗi xảy ra. Vui lòng thử lại.');
		}
	}

	/**
	 * Soft delete hàng loạt (AJAX)
	 */
	public function action_bulk_delete()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/users');
		}

		$ids = Input::post('ids', array());
		if (!is_array($ids)) {
			$ids = array();
		}

		// Loại trừ chính mình khỏi danh sách xóa
		$current_admin_id = Session::get('admin_id');
		$ids = array_filter($ids, function($id) use ($current_admin_id) {
			return $id != $current_admin_id;
		});

		if (empty($ids)) {
			return $this->error_response('Không thể xóa chính mình.');
		}

		$count = Model_Admin::bulk_soft_delete($ids);
		
		if ($count > 0) {
			\Log::info('Bulk soft delete completed: ' . $count . ' admins deleted, IDs: ' . implode(',', $ids));
		} else {
			\Log::warning('Bulk soft delete failed: No admins were deleted, IDs: ' . implode(',', $ids));
		}
		
		// Tạo CSRF token mới sau khi xử lý thành công
		$new_csrf_token = Security::fetch_token();
		
		$data = array(
			'affected' => (int) $count,
			'csrf_token' => $new_csrf_token
		);
		
		return $this->success_response('Đã xóa '.(int)$count.' admin', $data);
	}

	/**
	 * Khôi phục hàng loạt (AJAX)
	 */
	public function action_bulk_restore()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/users/deleted');
		}

		$ids = Input::post('ids', array());
		if (!is_array($ids)) {
			$ids = array();
		}

		$count = Model_Admin::bulk_restore($ids);
		
		if ($count > 0) {
			\Log::info('Bulk restore completed: ' . $count . ' admins restored, IDs: ' . implode(',', $ids));
		} else {
			\Log::warning('Bulk restore failed: No admins were restored, IDs: ' . implode(',', $ids));
		}
		
		// Tạo CSRF token mới sau khi xử lý thành công
		$new_csrf_token = Security::fetch_token();
		
		$data = array(
			'affected' => (int) $count,
			'csrf_token' => $new_csrf_token
		);
		
		return $this->success_response('Đã khôi phục '.(int)$count.' admin', $data);
	}

	/**
	 * Xóa vĩnh viễn hàng loạt (AJAX)
	 */
	public function action_bulk_delete_permanent()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/users/deleted');
		}

		$ids = Input::post('ids', array());
		if (!is_array($ids)) {
			$ids = array();
		}

		$count = Model_Admin::bulk_hard_delete($ids);
		
		if ($count > 0) {
			\Log::info('Bulk hard delete completed: ' . $count . ' admins permanently deleted, IDs: ' . implode(',', $ids));
		} else {
			\Log::warning('Bulk hard delete failed: No admins were deleted, IDs: ' . implode(',', $ids));
		}
		
		// Tạo CSRF token mới sau khi xử lý thành công
		$new_csrf_token = Security::fetch_token();
		
		$data = array(
			'affected' => (int) $count,
			'csrf_token' => $new_csrf_token
		);
		
		return $this->success_response('Đã xóa vĩnh viễn '.(int)$count.' admin', $data);
	}
}
