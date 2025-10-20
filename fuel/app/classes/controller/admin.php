<?php

/**
 * Controller Admin
 * 
 * Xử lý logic cho hệ thống admin
 * Bao gồm: đăng nhập, dashboard, đăng xuất
 * 
 * @package    App
 * @subpackage Controller
 */
class Controller_Admin extends Controller
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
	 * Trang đăng nhập admin
	 * 
	 * @return void
	 */
	public function action_login()
	{
		// Nếu đã đăng nhập thì redirect đến dashboard
		$this->redirect_if_logged_in();

		$data = array();
		$data['error_message'] = '';
		$data['success_message'] = '';
		
		// Lấy flash messages
		if (Session::get_flash('error')) {
			$data['error_message'] = Session::get_flash('error');
		}
		if (Session::get_flash('success')) {
			$data['success_message'] = Session::get_flash('success');
		}
		
		// Cho phép đăng ký nếu hệ thống chưa có admin nào
		$data['show_register_link'] = (Model_Admin::count_all() === 0);

		// Xử lý form đăng nhập
		if (Input::method() === 'POST') {
			// CSRF token đã được kiểm tra tự động bởi Fuel
			$username = Input::post('username', '');
			$password = Input::post('password', '');

			// Kiểm tra dữ liệu đầu vào
			if (empty($username) || empty($password)) {
				$data['error_message'] = 'Vui lòng nhập đầy đủ thông tin đăng nhập.';
			} else {
				// Tìm admin theo username hoặc email
				$admin = Model_Admin::find_by_username_or_email($username);

				if ($admin && $admin->check_password($password)) {
					// Đăng nhập thành công
					Session::set('admin_id', $admin->id);
					Session::set('admin_username', $admin->username);
					Session::set('admin_full_name', $admin->full_name);

					// Cập nhật thời gian đăng nhập cuối
					$admin->update_last_login();

					// Redirect đến dashboard
					Response::redirect('admin/dashboard');
				} else {
					$data['error_message'] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
				}
			}
		}

		$data['title'] = 'Đăng nhập Admin';
		$data['is_login_page'] = true;
		$data['content'] = View::forge('admin/content/login', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Đăng ký admin đầu tiên (chỉ khi chưa có admin nào)
	 * 
	 * @return void
	 */
	public function action_register()
	{
		// Nếu đã có admin, không cho đăng ký công khai
		if (Model_Admin::count_all() > 0) {
			Response::redirect('admin/login');
		}

		$data = array();
		$data['error_message'] = '';
		$data['success_message'] = '';
		$data['form_data'] = array();

		if (Input::method() === 'POST') {
			// CSRF token đã được kiểm tra tự động bởi Fuel
			$username = Input::post('username', '');
			$email = Input::post('email', '');
			$password = Input::post('password', '');
			$full_name = Input::post('full_name', '');

			if (empty($username) || empty($email) || empty($password)) {
				$data['error_message'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
			} else {
				// Kiểm tra tồn tại trùng username hoặc email
				$existing = Model_Admin::find_by_username_or_email($username);
				if (!$existing) {
					$existing = Model_Admin::find_by_username_or_email($email);
				}

				if ($existing) {
					$data['error_message'] = 'Username hoặc email đã tồn tại.';
				} else {
					$admin_data = array(
						'username' => $username,
						'email' => $email,
						'password' => $password,
						'full_name' => $full_name,
						'is_active' => 1,
					);

					$new_admin = Model_Admin::create_admin($admin_data);
					if ($new_admin) {
						// Sau khi tạo, chuyển sang đăng nhập
						$data['success_message'] = 'Tạo tài khoản admin thành công. Vui lòng đăng nhập!';
						Response::redirect('admin/login');
					} else {
						$data['error_message'] = 'Không thể tạo tài khoản. Vui lòng thử lại.';
					}
				}
			}

			// Lưu lại dữ liệu form nếu lỗi
			if (!empty($data['error_message'])) {
				$data['form_data'] = array(
					'username' => $username,
					'email' => $email,
					'full_name' => $full_name,
				);
			}
		}

		$data['title'] = 'Đăng ký Admin';
		$data['is_login_page'] = true;
		$data['content'] = View::forge('admin/content/register', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Dashboard admin
	 * 
	 * @return void
	 */
	public function action_dashboard()
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

		$data['title'] = 'Dashboard Admin';
		$data['content'] = View::forge('admin/content/dashboard', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Đăng xuất admin
	 * 
	 * @return void
	 */
	public function action_logout()
	{
		// Xóa session
		Session::delete('admin_id');
		Session::delete('admin_username');
		Session::delete('admin_full_name');

		// Redirect đến trang đăng nhập
		Response::redirect('admin/login');
	}

	/**
	 * Trang quản lý admin (danh sách admin)
	 * 
	 * @return void
	 */
	public function action_manage()
	{
		// Kiểm tra đăng nhập
		$this->require_login();

		$data = array();
		
		// Phân trang
		$page = Input::get('page', 1);
		$limit = 10;
		$offset = ($page - 1) * $limit;

		// Lấy danh sách admin
		$data['admins'] = Model_Admin::get_all_admins($limit, $offset);
		$data['total_admins'] = Model_Admin::count_all();
		$data['current_page'] = $page;
		$data['total_pages'] = ceil($data['total_admins'] / $limit);

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
		// Kiểm tra đăng nhập
		$this->require_login();

		$data = array();
		$data['success_message'] = '';
		$data['error_message'] = '';

		// Xử lý form thêm admin
		if (Input::method() === 'POST') {
			// CSRF token đã được kiểm tra tự động bởi Fuel
			$username = Input::post('username', '');
			$email = Input::post('email', '');
			$password = Input::post('password', '');
			$full_name = Input::post('full_name', '');

			// Kiểm tra dữ liệu đầu vào
			if (empty($username) || empty($email) || empty($password)) {
				$data['error_message'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
			} else {
				// Kiểm tra username và email đã tồn tại chưa
				$existing_admin = Model_Admin::find_by_username_or_email($username);
				if ($existing_admin) {
					$data['error_message'] = 'Username hoặc email đã tồn tại.';
				} else {
					// Tạo admin mới
					$admin_data = array(
						'username' => $username,
						'email' => $email,
						'password' => $password,
						'full_name' => $full_name,
						'is_active' => 1
					);

					$new_admin = Model_Admin::create_admin($admin_data);
					if ($new_admin) {
						$data['success_message'] = 'Thêm admin thành công!';
						// Reset form
						$data['form_data'] = array();
					} else {
						$data['error_message'] = 'Có lỗi xảy ra khi thêm admin.';
					}
				}
			}

			// Giữ lại dữ liệu form nếu có lỗi
			if (!empty($data['error_message'])) {
				$data['form_data'] = array(
					'username' => $username,
					'email' => $email,
					'full_name' => $full_name
				);
			}
		}

		$data['title'] = 'Thêm Admin';
		$data['content'] = View::forge('admin/content/add', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Google OAuth login
	 * 
	 * @return void
	 */
	public function action_google_login()
	{
		// Nếu đã đăng nhập thì redirect đến dashboard
		$this->redirect_if_logged_in();
		
		$google_oauth = new Service_Googleoauth();
		$auth_url = $google_oauth->get_auth_url();
		
		Response::redirect($auth_url);
	}
	
	/**
	 * Google OAuth callback
	 * 
	 * @return void
	 */
	public function action_google_callback()
	{
		// Nếu đã đăng nhập thì redirect đến dashboard
		$this->redirect_if_logged_in();
		
		$code = Input::get('code');
		$state = Input::get('state');
		
		if (empty($code) || empty($state)) {
			Session::set_flash('error', 'Lỗi xác thực Google. Vui lòng thử lại.');
			Response::redirect('admin/login');
		}
		
		$google_oauth = new Service_Googleoauth();
		$user_info = $google_oauth->handle_callback($code, $state);
		
		if (!$user_info) {
			Session::set_flash('error', 'Không thể xác thực với Google. Vui lòng thử lại.');
			Response::redirect('admin/login');
		}
		
		// Kiểm tra email có tồn tại trong hệ thống không
		$admin = Model_Admin::find_by_email($user_info['email']);
		
		if (!$admin) {
			// Tạo admin mới từ thông tin Google
			$admin_data = array(
				'username' => $user_info['email'],
				'email' => $user_info['email'],
				'password' => $this->generate_random_password(),
				'full_name' => $user_info['name'],
				'is_active' => 1,
				'google_id' => $user_info['id']
			);
			
			$admin = Model_Admin::create_admin($admin_data);
			
			if (!$admin) {
				Session::set_flash('error', 'Không thể tạo tài khoản từ Google. Vui lòng thử lại.');
				Response::redirect('admin/login');
			}
		} else {
			// Cập nhật Google ID nếu chưa có
			if (empty($admin->google_id)) {
				$admin->google_id = $user_info['id'];
				$admin->save();
			}
		}
		
		// Đăng nhập thành công
		Session::set('admin_id', $admin->id);
		Session::set('admin_username', $admin->username);
		Session::set('admin_full_name', $admin->full_name);
		
		// Cập nhật thời gian đăng nhập cuối
		$admin->update_last_login();
		
		// Redirect đến dashboard
		Response::redirect('admin/dashboard');
	}
	
	/**
	 * Tạo mật khẩu ngẫu nhiên
	 * 
	 * @return string
	 */
	protected function generate_random_password()
	{
		return bin2hex(random_bytes(16));
	}
	
	/**
	 * Sửa thông tin admin
	 * 
	 * @return void
	 */
	public function action_edit($id = null)
	{
		// Kiểm tra đăng nhập
		$this->require_login();

		if (empty($id)) {
			Response::redirect('admin/manage');
		}

		$data = array();
		$data['success_message'] = '';
		$data['error_message'] = '';
		$data['admin'] = Model_Admin::find($id);

		if (!$data['admin']) {
			Session::set_flash('error', 'Không tìm thấy admin.');
			Response::redirect('admin/manage');
		}

		// Xử lý form sửa admin
		if (Input::method() === 'POST') {
			// CSRF token đã được kiểm tra tự động bởi Fuel
			$username = Input::post('username', '');
			$email = Input::post('email', '');
			$password = Input::post('password', '');
			$full_name = Input::post('full_name', '');
			$is_active = Input::post('is_active', 1);

			// Kiểm tra dữ liệu đầu vào
			if (empty($username) || empty($email)) {
				$data['error_message'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
			} else {
				// Kiểm tra username và email đã tồn tại chưa (trừ admin hiện tại)
				$existing_admin = Model_Admin::find_by_username_or_email($username);
				if ($existing_admin && $existing_admin->id != $id) {
					$data['error_message'] = 'Username đã tồn tại.';
				} else {
					$existing_admin = Model_Admin::find_by_username_or_email($email);
					if ($existing_admin && $existing_admin->id != $id) {
						$data['error_message'] = 'Email đã tồn tại.';
					} else {
						// Cập nhật admin
						$update_data = array(
							'username' => $username,
							'email' => $email,
							'full_name' => $full_name,
							'is_active' => $is_active
						);

						// Chỉ cập nhật mật khẩu nếu có nhập
						if (!empty($password)) {
							$update_data['password'] = $password;
						}

						if ($data['admin']->update_admin($update_data)) {
							$data['success_message'] = 'Cập nhật admin thành công!';
							// Reload data
							$data['admin'] = Model_Admin::find($id);
						} else {
							$data['error_message'] = 'Có lỗi xảy ra khi cập nhật admin.';
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
	 * @return void
	 */
	public function action_delete($id = null)
	{
		// Kiểm tra đăng nhập
		$this->require_login();

		// Chỉ cho phép POST request
		if (Input::method() !== 'POST') {
			Response::redirect('admin/manage');
		}

		if (empty($id)) {
			$response = array(
				'success' => false,
				'message' => 'ID không hợp lệ.'
			);
		} else {
			$admin = Model_Admin::find($id);
			
			if (!$admin) {
				$response = array(
					'success' => false,
					'message' => 'Không tìm thấy admin.'
				);
			} else {
				// Không cho phép xóa chính mình
				$current_admin_id = Session::get('admin_id');
				if ($admin->id == $current_admin_id) {
					$response = array(
						'success' => false,
						'message' => 'Không thể xóa chính mình.'
					);
				} else {
					if ($admin->soft_delete()) {
						$response = array(
							'success' => true,
							'message' => 'Xóa admin thành công!'
						);
					} else {
						$response = array(
							'success' => false,
							'message' => 'Có lỗi xảy ra khi xóa admin.'
						);
					}
				}
			}
		}

		// Trả về JSON response
		$response_obj = Response::forge(json_encode($response));
		$response_obj->set_header('Content-Type', 'application/json');
		return $response_obj;
	}

	/**
	 * Trang chủ admin (redirect đến dashboard)
	 * 
	 * @return void
	 */
	public function action_index()
	{
		if ($this->is_logged_in()) {
			Response::redirect('admin/dashboard');
		} else {
			Response::redirect('admin/login');
		}
	}

	/**
	 * Danh sách admin đã xóa (soft delete)
	 *
	 * @return void
	 */
	public function action_deleted()
	{
		// Kiểm tra đăng nhập
		$this->require_login();

		$data = array();
		$page = Input::get('page', 1);
		$limit = 10;
		$offset = ($page - 1) * $limit;

		$data['admins'] = Model_Admin::get_deleted_admins($limit, $offset);
		$data['total_admins'] = Model_Admin::count_deleted();
		$data['current_page'] = $page;
		$data['total_pages'] = ceil($data['total_admins'] / $limit);

		$data['title'] = 'Admin đã xóa';
		$data['content'] = View::forge('admin/content/deleted', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Khôi phục admin (AJAX)
	 *
	 * @param int $id
	 * @return Response
	 */
	public function action_restore($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/deleted');
		}

		if (empty($id)) {
			$response = array('success' => false, 'message' => 'ID không hợp lệ.');
		} else {
			// Lấy admin kể cả đã xóa
			try {
				$query = \DB::query("SELECT * FROM admins WHERE id = :id");
				$result = $query->param('id', $id)->execute();
				if ($result->count() === 0) {
					$response = array('success' => false, 'message' => 'Không tìm thấy admin.');
				} else {
					$data = $result->current();
					$admin = new Model_Admin();
					foreach ($data as $key => $value) {
						$admin->$key = $value;
					}
					if ($admin->restore()) {
						$response = array('success' => true, 'message' => 'Khôi phục admin thành công!');
					} else {
						$response = array('success' => false, 'message' => 'Khôi phục thất bại.');
					}
				}
			} catch (\Exception $e) {
				$response = array('success' => false, 'message' => 'Có lỗi xảy ra.');
			}
		}

		$response_obj = Response::forge(json_encode($response));
		$response_obj->set_header('Content-Type', 'application/json');
		return $response_obj;
	}

	/**
	 * Xóa vĩnh viễn admin (AJAX)
	 *
	 * @param int $id
	 * @return Response
	 */
	public function action_delete_permanent($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/deleted');
		}

		if (empty($id)) {
			$response = array('success' => false, 'message' => 'ID không hợp lệ.');
		} else {
			try {
				$query = \DB::query("SELECT * FROM admins WHERE id = :id");
				$result = $query->param('id', $id)->execute();
				if ($result->count() === 0) {
					$response = array('success' => false, 'message' => 'Không tìm thấy admin.');
				} else {
					$data = $result->current();
					$admin = new Model_Admin();
					foreach ($data as $key => $value) { $admin->$key = $value; }
					if ($admin->hard_delete()) {
						$response = array('success' => true, 'message' => 'Đã xóa vĩnh viễn!');
					} else {
						$response = array('success' => false, 'message' => 'Xóa vĩnh viễn thất bại.');
					}
				}
			} catch (\Exception $e) {
				$response = array('success' => false, 'message' => 'Có lỗi xảy ra.');
			}
		}

		$response_obj = Response::forge(json_encode($response));
		$response_obj->set_header('Content-Type', 'application/json');
		return $response_obj;
	}

	/**
	 * Soft delete hàng loạt (AJAX)
	 */
	public function action_bulk_delete()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/manage');
		}

		$ids = Input::post('ids', array());
		if (!is_array($ids)) {
			$ids = array();
		}

		$count = Model_Admin::bulk_soft_delete($ids);
		$response = array('success' => true, 'affected' => (int) $count, 'message' => 'Đã xóa '.(int)$count.' admin');

		$response_obj = Response::forge(json_encode($response));
		$response_obj->set_header('Content-Type', 'application/json');
		return $response_obj;
	}

	/**
	 * Khôi phục hàng loạt (AJAX)
	 */
	public function action_bulk_restore()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/deleted');
		}

		$ids = Input::post('ids', array());
		if (!is_array($ids)) {
			$ids = array();
		}

		$count = Model_Admin::bulk_restore($ids);
		$response = array('success' => true, 'affected' => (int) $count, 'message' => 'Đã khôi phục '.(int)$count.' admin');

		$response_obj = Response::forge(json_encode($response));
		$response_obj->set_header('Content-Type', 'application/json');
		return $response_obj;
	}

	/**
	 * Xóa vĩnh viễn hàng loạt (AJAX)
	 */
	public function action_bulk_delete_permanent()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/deleted');
		}

		$ids = Input::post('ids', array());
		if (!is_array($ids)) {
			$ids = array();
		}

		$count = Model_Admin::bulk_hard_delete($ids);
		$response = array('success' => true, 'affected' => (int) $count, 'message' => 'Đã xóa vĩnh viễn '.(int)$count.' admin');

		$response_obj = Response::forge(json_encode($response));
		$response_obj->set_header('Content-Type', 'application/json');
		return $response_obj;
	}
}
