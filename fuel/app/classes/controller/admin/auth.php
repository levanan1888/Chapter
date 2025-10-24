<?php

/**
 * Admin Auth Controller
 * 
 * Xử lý đăng nhập, đăng xuất, đăng ký admin
 * 
 * @package    App
 * @subpackage Controller\Admin
 */
class Controller_Admin_Auth extends Controller_Admin_Base
{
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
				// Tìm user theo username hoặc email (bao gồm cả tài khoản bị soft delete)
				$user = Model_Admin::find_by_username_or_email_any_status($username);

				if ($user && $user->check_password($password)) {
					// Kiểm tra tài khoản có bị soft delete không
					if ($user->deleted_at !== null) {
						$data['error_message'] = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên để được hỗ trợ.';
					} elseif ($user->is_active == 0) {
						$data['error_message'] = 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên để được hỗ trợ.';
					} elseif ($user->user_type !== 'admin') {
						$data['error_message'] = 'Bạn không đủ quyền đăng nhập vào hệ thống quản trị.';
					} else {
						// Đăng nhập thành công - chỉ admin mới được phép
						Session::set('admin_id', $user->id);
						Session::set('admin_username', $user->username);
						Session::set('admin_full_name', $user->full_name);

						// Cập nhật thời gian đăng nhập cuối
						$user->update_last_login();

						// Redirect đến dashboard
						Response::redirect('admin/dashboard');
					}
				} else {
					$data['error_message'] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
				}
			}
		}

		$data['title'] = 'Đăng nhập Admin';
		$data['is_login_page'] = true;
		$data['content'] = View::forge('admin/auth/login', $data, false);
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
	 * Tạo mật khẩu ngẫu nhiên
	 * 
	 * @return string
	 */
	protected function generate_random_password()
	{
		return bin2hex(random_bytes(16));
	}
}
