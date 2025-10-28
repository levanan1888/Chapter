<?php

/**
 * User Controller
 * 
 * Xử lý đăng nhập, đăng ký cho người dùng thông thường
 * 
 * @package    App
 * @subpackage Controller
 */
class Controller_User extends Controller
{
	/**
	 * Kiểm tra user đã đăng nhập chưa
	 * 
	 * @return bool
	 */
	protected function is_logged_in()
	{
		return Session::get('user_id') !== null;
	}

	/**
	 * Redirect đến trang đăng nhập nếu chưa đăng nhập
	 * 
	 * @return void
	 */
	protected function require_login()
	{
		if (!$this->is_logged_in()) {
			Response::redirect('user/login');
		}
	}

	/**
	 * Redirect đến trang chủ nếu đã đăng nhập
	 * 
	 * @return void
	 */
	protected function redirect_if_logged_in()
	{
		if ($this->is_logged_in()) {
			Response::redirect('client');
		}
	}

	/**
	 * Lấy thông tin user hiện tại
	 * 
	 * @return Model_Admin|null
	 */
	protected function get_current_user()
	{
		$user_id = Session::get('user_id');
		if ($user_id) {
			return Model_Admin::find_by_id($user_id);
		}
		return null;
	}

	/**
	 * Trang đăng nhập user
	 * 
	 * @return void
	 */
	public function action_login()
	{
		// Nếu đã đăng nhập thì redirect đến trang chủ
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
		if (Session::get_flash('account_locked')) {
			$data['account_locked'] = true;
		}

		// Xử lý form đăng nhập
		if (Input::method() === 'POST') {
			// Kiểm tra CSRF token
			if (!Security::check_token()) {
				$data['error_message'] = 'Token không hợp lệ. Vui lòng thử lại.';
			} else {
				$username = Input::post('username', '');
				$password = Input::post('password', '');

				// Kiểm tra dữ liệu đầu vào
				if (empty($username) || empty($password)) {
					$data['error_message'] = 'Vui lòng nhập đầy đủ thông tin đăng nhập.';
				} else {
					// Tìm user theo username hoặc email với user_type = 'user' (bao gồm cả tài khoản bị soft delete)
					$user = $this->find_user_by_username_or_email_any_status($username);

					if ($user) {
						// Kiểm tra tài khoản có bị soft delete hoặc không hoạt động không
						if ($user->deleted_at !== null) {
							$data['error_message'] = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên để được hỗ trợ.';
							$data['account_locked'] = true;
						} elseif ($user->is_active == 0) {
							$data['error_message'] = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên để được hỗ trợ.';
							$data['account_locked'] = true;
						} elseif ($user->check_password($password)) {
							// Đăng nhập thành công
							Session::set('user_id', $user->id);
							Session::set('user_username', $user->username);
							Session::set('user_full_name', $user->full_name);

							// Cập nhật thời gian đăng nhập cuối
							$user->update_last_login();

							// Redirect đến trang chủ
							Response::redirect('client');
						} else {
							$data['error_message'] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
						}
					} else {
						$data['error_message'] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
					}
				}
			}
		}

		$data['title'] = 'Đăng nhập';
		$data['content'] = View::forge('user/login', $data, false);
		return View::forge('layouts/client', $data);
	}

	/**
	 * Trang đăng ký user
	 * 
	 * @return void
	 */
	public function action_register()
	{
		// Nếu đã đăng nhập thì redirect đến trang chủ
		$this->redirect_if_logged_in();

		$data = array();
		$data['error_message'] = '';
		$data['success_message'] = '';
		$data['form_data'] = array();

		if (Input::method() === 'POST') {
			// Kiểm tra CSRF token
			if (!Security::check_token()) {
				$data['error_message'] = 'Token không hợp lệ. Vui lòng thử lại.';
			} else {
				$username = Input::post('username', '');
				$email = Input::post('email', '');
				$password = Input::post('password', '');
				$confirm_password = Input::post('confirm_password', '');
				$full_name = Input::post('full_name', '');

				// Kiểm tra dữ liệu đầu vào
				if (empty($username) || empty($email) || empty($password)) {
					$data['error_message'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
				} elseif ($password !== $confirm_password) {
					$data['error_message'] = 'Mật khẩu xác nhận không khớp.';
				} elseif (strlen($password) < 6) {
					$data['error_message'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
				} else {
					// Kiểm tra email đã tồn tại chưa
					$existing_email = $this->find_user_by_email($email);
					if ($existing_email) {
						$data['error_message'] = 'Email này đã được đăng ký. Vui lòng sử dụng email khác.';
					} else {
						// Kiểm tra username đã tồn tại chưa
						$existing_username = $this->find_user_by_username($username);
						if ($existing_username) {
							$data['error_message'] = 'Tên đăng nhập này đã được sử dụng. Vui lòng chọn tên đăng nhập khác.';
						} else {
							$user_data = array(
								'username' => $username,
								'email' => $email,
								'password' => $password,
								'full_name' => $full_name,
								'is_active' => 1,
								'user_type' => 'user'
							);

							$new_user = Model_Admin::create_admin($user_data);
							if ($new_user) {
								// Sau khi tạo, chuyển sang đăng nhập
								Session::set_flash('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
								Response::redirect('user/login');
							} else {
								$data['error_message'] = 'Không thể tạo tài khoản. Vui lòng thử lại.';
							}
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
		}

		$data['title'] = 'Đăng ký';
		$data['content'] = View::forge('user/register', $data, false);
		return View::forge('layouts/client', $data);
	}

	/**
	 * Trang thông tin user
	 * 
	 * @return void
	 */
	public function action_profile()
	{
		// Yêu cầu đăng nhập
		$this->require_login();

		$data = array();
		$user = $this->get_current_user();

		if (!$user) {
			Response::redirect('user/login');
		}

		$data['user'] = $user;
		$data['title'] = 'Thông tin tài khoản';
		$data['content'] = View::forge('user/profile', $data, false);
		return View::forge('layouts/client', $data);
	}

	/**
	 * Đăng xuất user
	 * 
	 * @return void
	 */
	public function action_logout()
	{
		// Xóa session
		Session::delete('user_id');
		Session::delete('user_username');
		Session::delete('user_full_name');

		// Redirect đến trang chủ
		Response::redirect('client');
	}

	/**
	 * Google OAuth login
	 * 
	 * @return void
	 */
	public function action_google_login()
	{
		// Nếu đã đăng nhập thì redirect đến trang chủ
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
		// Nếu đã đăng nhập thì redirect đến trang chủ
		$this->redirect_if_logged_in();
		
		$code = Input::get('code');
		$state = Input::get('state');
		
		if (empty($code) || empty($state)) {
			Session::set_flash('error', 'Lỗi xác thực Google. Vui lòng thử lại.');
			Response::redirect('user/login');
		}
		
		$google_oauth = new Service_Googleoauth();
		$user_info = $google_oauth->handle_callback($code, $state);
		
		if (!$user_info) {
			Session::set_flash('error', 'Không thể xác thực với Google. Vui lòng thử lại.');
			Response::redirect('user/login');
		}
		
		// Kiểm tra email có tồn tại trong hệ thống không (với user_type = 'user')
		$user = $this->find_user_by_email_any_status($user_info['email']);
		
		if (!$user) {
			// Tạo user mới từ thông tin Google
			$user_data = array(
				'username' => $user_info['email'],
				'email' => $user_info['email'],
				'password' => $this->generate_random_password(),
				'full_name' => $user_info['name'],
				'is_active' => 1,
				'user_type' => 'user',
				'google_id' => $user_info['id']
			);
			
			$user = Model_Admin::create_admin($user_data);
			
			if (!$user) {
				Session::set_flash('error', 'Không thể tạo tài khoản từ Google. Vui lòng thử lại.');
				Response::redirect('user/login');
			}
		} else {
			// Kiểm tra tài khoản có bị soft delete hoặc không hoạt động không
			if ($user->deleted_at !== null) {
				Session::set_flash('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên để được hỗ trợ.');
				Session::set_flash('account_locked', true);
				Response::redirect('user/login');
			} elseif ($user->is_active == 0) {
				Session::set_flash('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên để được hỗ trợ.');
				Session::set_flash('account_locked', true);
				Response::redirect('user/login');
			}
			
			// Cập nhật Google ID nếu chưa có
			if (empty($user->google_id)) {
				$user->google_id = $user_info['id'];
				$user->save();
			}
		}
		
		// Đăng nhập thành công
		Session::set('user_id', $user->id);
		Session::set('user_username', $user->username);
		Session::set('user_full_name', $user->full_name);
		
		// Cập nhật thời gian đăng nhập cuối
		$user->update_last_login();
		
		// Redirect đến trang chủ
		Response::redirect('client');
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
	 * Tìm user theo username hoặc email với user_type = 'user'
	 * 
	 * @param string $username_or_email Username hoặc email
	 * @return Model_Admin|null
	 */
	protected function find_user_by_username_or_email($username_or_email)
	{
		try {
			$query = \DB::query("SELECT * FROM admins WHERE (username = :username_or_email OR email = :username_or_email) AND user_type = :user_type AND is_active = :is_active AND deleted_at IS NULL");
			$result = $query->param('username_or_email', $username_or_email)
							->param('user_type', 'user')
							->param('is_active', 1)
							->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$user = new Model_Admin();
				foreach ($data as $key => $value) {
					$user->$key = $value;
				}
				return $user;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Tìm user theo email với user_type = 'user'
	 * 
	 * @param string $email Email
	 * @return Model_Admin|null
	 */
	protected function find_user_by_email($email)
	{
		try {
			$query = \DB::query("SELECT * FROM admins WHERE email = :email AND user_type = :user_type AND is_active = :is_active AND deleted_at IS NULL");
			$result = $query->param('email', $email)
							->param('user_type', 'user')
							->param('is_active', 1)
							->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$user = new Model_Admin();
				foreach ($data as $key => $value) {
					$user->$key = $value;
				}
				return $user;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Tìm user theo username với user_type = 'user'
	 * 
	 * @param string $username Username
	 * @return Model_Admin|null
	 */
	protected function find_user_by_username($username)
	{
		try {
			$query = \DB::query("SELECT * FROM admins WHERE username = :username AND user_type = :user_type AND is_active = :is_active AND deleted_at IS NULL");
			$result = $query->param('username', $username)
							->param('user_type', 'user')
							->param('is_active', 1)
							->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$user = new Model_Admin();
				foreach ($data as $key => $value) {
					$user->$key = $value;
				}
				return $user;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Tìm user theo username hoặc email với user_type = 'user' (bao gồm cả tài khoản bị soft delete)
	 * 
	 * @param string $username_or_email Username hoặc email
	 * @return Model_Admin|null
	 */
	protected function find_user_by_username_or_email_any_status($username_or_email)
	{
		try {
			$query = \DB::query("SELECT * FROM admins WHERE (username = :username_or_email OR email = :username_or_email) AND user_type = :user_type");
			$result = $query->param('username_or_email', $username_or_email)
							->param('user_type', 'user')
							->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$user = new Model_Admin();
				foreach ($data as $key => $value) {
					$user->$key = $value;
				}
				return $user;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Tìm user theo email với user_type = 'user' (bao gồm cả tài khoản bị soft delete)
	 * 
	 * @param string $email Email
	 * @return Model_Admin|null
	 */
	protected function find_user_by_email_any_status($email)
	{
		try {
			$query = \DB::query("SELECT * FROM admins WHERE email = :email AND user_type = :user_type");
			$result = $query->param('email', $email)
							->param('user_type', 'user')
							->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$user = new Model_Admin();
				foreach ($data as $key => $value) {
					$user->$key = $value;
				}
				return $user;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * API kiểm tra trạng thái user hiện tại
	 * 
	 * @return void
	 */
	public function action_check_status()
	{
		// Chỉ cho phép AJAX request
		if (!Input::is_ajax()) {
			Response::redirect('client');
		}

		$user_id = Session::get('user_id');
		if (!$user_id) {
			$this->json_response(false, 'Chưa đăng nhập');
			return;
		}

		// Tìm user với trạng thái hiện tại
		$user = $this->find_user_by_id_any_status($user_id);
		
		if (!$user) {
			// User không tồn tại
			Session::delete('user_id');
			Session::delete('user_username');
			Session::delete('user_full_name');
			$this->json_response(false, 'Tài khoản không tồn tại');
			return;
		}

		// Kiểm tra trạng thái
		if ($user->deleted_at !== null) {
			// Tài khoản bị soft delete
			Session::delete('user_id');
			Session::delete('user_username');
			Session::delete('user_full_name');
			$this->json_response(false, 'Tài khoản đã bị khóa', 'locked');
			return;
		}

		if ($user->is_active == 0) {
			// Tài khoản bị vô hiệu hóa
			Session::delete('user_id');
			Session::delete('user_username');
			Session::delete('user_full_name');
			$this->json_response(false, 'Tài khoản đã bị vô hiệu hóa', 'inactive');
			return;
		}

		// Tài khoản OK
		$this->json_response(true, 'Tài khoản hoạt động bình thường');
	}

	/**
	 * Tìm user theo ID (bao gồm cả tài khoản bị soft delete)
	 * 
	 * @param int $id ID của user
	 * @return Model_Admin|null
	 */
	protected function find_user_by_id_any_status($id)
	{
		try {
			$query = \DB::query("SELECT * FROM admins WHERE id = :id AND user_type = :user_type");
			$result = $query->param('id', $id)
							->param('user_type', 'user')
							->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$user = new Model_Admin();
				foreach ($data as $key => $value) {
					$user->$key = $value;
				}
				return $user;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Trả về JSON response
	 * 
	 * @param bool $success
	 * @param string $message
	 * @param string $type
	 * @return void
	 */
	protected function json_response($success, $message, $type = '')
	{
		$response = array(
			'success' => $success,
			'message' => $message
		);

		if (!empty($type)) {
			$response['type'] = $type;
		}

		Response::forge(json_encode($response), 200, array(
			'Content-Type' => 'application/json'
		))->send();
	}

	/**
	 * Trang quên mật khẩu
	 * 
	 * @return void
	 */
	public function action_forgot_password()
	{
		// Nếu đã đăng nhập thì redirect đến trang chủ
		$this->redirect_if_logged_in();

		$data = array();
		$data['error_message'] = '';
		$data['success_message'] = '';
		$data['form_data'] = array();

		if (Input::method() === 'POST') {
			// Kiểm tra CSRF token
			if (!Security::check_token()) {
				$data['error_message'] = 'Token không hợp lệ. Vui lòng thử lại.';
			} else {
				$email = Input::post('email', '');

				// Kiểm tra dữ liệu đầu vào
				if (empty($email)) {
					$data['error_message'] = 'Vui lòng nhập địa chỉ email.';
				} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$data['error_message'] = 'Địa chỉ email không hợp lệ.';
				} else {
					// Tìm user theo email
					$user = $this->find_user_by_email($email);

					if ($user) {
						// Tạo token đặt lại mật khẩu
						$reset_token = Model_PasswordResetToken::create_reset_token($email);

						if ($reset_token) {
							// Gửi email đặt lại mật khẩu
							$email_service = new Service_Email();
							$email_sent = $email_service->send_password_reset($email, $reset_token->token, $user->full_name);

							if ($email_sent) {
								// Lưu email vào session để sử dụng ở trang verify token
								Session::set('reset_password_email', $email);
								
								// Redirect đến trang nhập mã token
								\Log::info('Password reset email sent to: ' . $email);
								Response::redirect('user/verify-token');
							} else {
								$data['error_message'] = 'Không thể gửi email. Vui lòng thử lại sau.';
								\Log::error('Failed to send password reset email to: ' . $email);
							}
						} else {
							$data['error_message'] = 'Không thể tạo mã xác thực. Vui lòng thử lại.';
						}
					} else {
						// Không tìm thấy user nhưng vẫn hiển thị thông báo thành công để bảo mật
						$data['success_message'] = 'Nếu email tồn tại trong hệ thống, chúng tôi đã gửi mã xác thực đến email của bạn.';
						\Log::info('Password reset requested for non-existent email: ' . $email);
					}
				}

				// Lưu lại dữ liệu form nếu lỗi
				if (!empty($data['error_message'])) {
					$data['form_data'] = array(
						'email' => $email,
					);
				}
			}
		}

		$data['title'] = 'Quên mật khẩu';
		$data['content'] = View::forge('user/forgot_password', $data, false);
		return View::forge('layouts/client', $data);
	}

	/**
	 * Trang đặt lại mật khẩu
	 * 
	 * @return void
	 */
	public function action_reset_password()
	{
		// Nếu đã đăng nhập thì redirect đến trang chủ
		$this->redirect_if_logged_in();

		// Lấy email và token từ session
		$email = Session::get('reset_password_email', '');
		$token = Session::get('reset_password_token', '');

		$data = array();
		$data['error_message'] = '';
		$data['success_message'] = '';
		$data['form_data'] = array();
		$data['valid_token'] = false;

		// Kiểm tra email và token trong session
		if (empty($email) || empty($token)) {
			$data['error_message'] = 'Phiên làm việc đã hết hạn. Vui lòng yêu cầu đặt lại mật khẩu mới.';
		} else {
			$data['valid_token'] = true;
			$data['email'] = $email;
			$data['token'] = $token;
		}

		// Xử lý form đặt lại mật khẩu
		if (Input::method() === 'POST' && $data['valid_token']) {
			// Kiểm tra CSRF token
			if (!Security::check_token()) {
				$data['error_message'] = 'Token không hợp lệ. Vui lòng thử lại.';
			} else {
				$new_password = Input::post('password', '');
				$confirm_password = Input::post('confirm_password', '');

				// Kiểm tra dữ liệu đầu vào
				if (empty($new_password)) {
					$data['error_message'] = 'Vui lòng nhập mật khẩu mới.';
				} elseif (strlen($new_password) < 6) {
					$data['error_message'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
				} elseif ($new_password !== $confirm_password) {
					$data['error_message'] = 'Mật khẩu xác nhận không khớp.';
				} else {
					// Tìm user và cập nhật mật khẩu
					$user = $this->find_user_by_email($email);

					if ($user) {
						// Cập nhật mật khẩu
						$update_data = array('password' => $new_password);
						
						if ($user->update_admin($update_data)) {
							// Xóa session reset password
							Session::delete('reset_password_email');
							Session::delete('reset_password_token');

							// Gửi email thông báo thành công
							$email_service = new Service_Email();
							$email_service->send_password_reset_success($email, $user->full_name);

							$data['success_message'] = 'Mật khẩu đã được đặt lại thành công! Bạn có thể đăng nhập với mật khẩu mới.';
							$data['valid_token'] = false; // Ẩn form sau khi thành công
							
							\Log::info('Password reset successful for user: ' . $email);
						} else {
							$data['error_message'] = 'Không thể cập nhật mật khẩu. Vui lòng thử lại.';
						}
					} else {
						$data['error_message'] = 'Không tìm thấy tài khoản.';
					}
				}
			}
		}

		$data['title'] = 'Đặt lại mật khẩu';
		$data['content'] = View::forge('user/reset_password', $data, false);
		return View::forge('layouts/client', $data);
	}

	/**
	 * Trang xác thực mã token
	 * 
	 * @return void
	 */
	public function action_verify_token()
	{
		// Nếu đã đăng nhập thì redirect đến trang chủ
		$this->redirect_if_logged_in();

		$data = array();
		$data['error_message'] = '';
		$data['success_message'] = '';
		$data['form_data'] = array();
		$data['email'] = '';

		// Lấy email từ session hoặc POST
		if (Input::method() === 'POST') {
			$email = Input::post('email', '');
		} else {
			$email = Session::get('reset_password_email', '');
		}

		$data['email'] = $email;

		if (Input::method() === 'POST') {
			// Kiểm tra CSRF token
			if (!Security::check_token()) {
				$data['error_message'] = 'Token không hợp lệ. Vui lòng thử lại.';
			} else {
				$token = Input::post('token', '');

				// Kiểm tra dữ liệu đầu vào
				if (empty($email)) {
					$data['error_message'] = 'Email không hợp lệ. Vui lòng thử lại.';
				} elseif (empty($token)) {
					$data['error_message'] = 'Vui lòng nhập mã xác thực.';
				} else {
					// Tìm token trong database
					$reset_token = Model_PasswordResetToken::find_by_email_and_token($email, $token);

					if ($reset_token) {
						// Token hợp lệ, lưu vào session và redirect đến trang đặt lại mật khẩu
						Session::set('reset_password_email', $email);
						Session::set('reset_password_token', $token);
						
						// Xóa token đã sử dụng
						$reset_token->delete();
						
						\Log::info('Token verified successfully for: ' . $email);
						
						// Redirect đến trang đặt lại mật khẩu
						Response::redirect('user/reset-password');
					} else {
						$data['error_message'] = 'Mã xác thực không đúng hoặc đã hết hạn. Vui lòng thử lại.';
						\Log::warning('Invalid token attempted for: ' . $email);
					}
				}

				// Lưu lại dữ liệu form nếu lỗi
				if (!empty($data['error_message'])) {
					$data['form_data'] = array(
						'email' => $email,
						'token' => $token,
					);
				}
			}
		}

		$data['title'] = 'Xác thực mã';
		$data['content'] = View::forge('user/verify_token', $data, false);
		return View::forge('layouts/client', $data);
	}
}

