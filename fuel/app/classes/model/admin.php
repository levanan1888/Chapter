<?php

/**
 * Model Admin
 * 
 * Quản lý dữ liệu admin trong hệ thống
 * 
 * @package    App
 * @subpackage Model
 */
class Model_Admin extends \Model
{
	/**
	 * Tên bảng trong database
	 * 
	 * @var string
	 */
	protected static $_table_name = 'admins';

	/**
	 * Khóa chính của bảng
	 * 
	 * @var string
	 */
	protected static $_primary_key = 'id';

	/**
	 * Các trường có thể được gán giá trị
	 * 
	 * @var array
	 */
	protected static $_properties = array(
		'id',
		'username',
		'email',
		'password',
		'full_name',
		'is_active',
		'user_type',
		'last_login',
		'google_id',
		'created_at',
		'updated_at',
		'deleted_at',
	);

	/**
	 * Các properties của model
	 */
	public $id;
	public $username;
	public $email;
	public $password;
	public $full_name;
	public $is_active;
	public $user_type;
	public $last_login;
	public $google_id;
	public $created_at;
	public $updated_at;
	public $deleted_at;

	/**
	 * Các trường được mã hóa khi lưu
	 * 
	 * @var array
	 */
	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);

	/**
	 * Tìm admin theo ID
	 * 
	 * @param int $id ID của admin
	 * @return Model_Admin|null
	 */
	public static function find($id)
	{
		try {
			$query = \DB::query("SELECT * FROM admins WHERE id = :id AND is_active = :is_active AND deleted_at IS NULL");
			$result = $query->param('id', $id)->param('is_active', 1)->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$admin = new self();
				foreach ($data as $key => $value) {
					$admin->$key = $value;
				}
				return $admin;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Tìm admin theo username hoặc email
	 * 
	 * @param string $username_or_email Username hoặc email
	 * @return Model_Admin|null
	 */
	public static function find_by_username_or_email($username_or_email)
	{
		try {
			$query = \DB::query("SELECT * FROM admins WHERE (username = :username_or_email OR email = :username_or_email) AND is_active = :is_active AND deleted_at IS NULL");
			$result = $query->param('username_or_email', $username_or_email)->param('is_active', 1)->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$admin = new self();
				foreach ($data as $key => $value) {
					$admin->$key = $value;
				}
				return $admin;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Tìm admin theo email
	 * 
	 * @param string $email Email của admin
	 * @return Model_Admin|null
	 */
	public static function find_by_email($email)
	{
		try {
			$query = \DB::query("SELECT * FROM admins WHERE email = :email AND is_active = :is_active AND deleted_at IS NULL");
			$result = $query->param('email', $email)->param('is_active', 1)->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$admin = new self();
				foreach ($data as $key => $value) {
					$admin->$key = $value;
				}
				return $admin;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Kiểm tra mật khẩu có đúng không
	 * 
	 * @param string $password Mật khẩu cần kiểm tra
	 * @return bool
	 */
	public function check_password($password)
	{
		return password_verify($password, $this->password);
	}

	/**
	 * Mã hóa mật khẩu
	 * 
	 * @param string $password Mật khẩu gốc
	 * @return string Mật khẩu đã mã hóa
	 */
	public static function hash_password($password)
	{
		return password_hash($password, PASSWORD_DEFAULT);
	}

	/**
	 * Cập nhật thời gian đăng nhập cuối
	 * 
	 * @return bool
	 */
	public function update_last_login()
	{
		try {
			$last_login = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');
			
			$query = \DB::query("UPDATE admins SET last_login = :last_login, updated_at = :updated_at WHERE id = :id");
			$result = $query->param('last_login', $last_login)
							->param('updated_at', $updated_at)
							->param('id', $this->id)
							->execute();
				
			if ($result) {
				$this->last_login = $last_login;
				$this->updated_at = $updated_at;
				return true;
			}
			return false;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Tạo admin mới
	 * 
	 * @param array $data Dữ liệu admin
	 * @return Model_Admin|null
	 */
	public static function create_admin(array $data)
	{
		try {
			// Kiểm tra dữ liệu đầu vào
			if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
				return null;
			}

			// Mã hóa mật khẩu
			$hashed_password = self::hash_password($data['password']);
			
			// Đặt giá trị mặc định
			$is_active = isset($data['is_active']) ? $data['is_active'] : 1;
			$full_name = isset($data['full_name']) ? $data['full_name'] : '';
			$user_type = isset($data['user_type']) ? $data['user_type'] : 'admin';
			$google_id = isset($data['google_id']) ? $data['google_id'] : null;
			$created_at = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');

			// Thêm vào database với Raw SQL
			$query = \DB::query("INSERT INTO admins (username, email, password, full_name, is_active, user_type, google_id, created_at, updated_at) VALUES (:username, :email, :password, :full_name, :is_active, :user_type, :google_id, :created_at, :updated_at)");
			$result = $query->param('username', $data['username'])
							->param('email', $data['email'])
							->param('password', $hashed_password)
							->param('full_name', $full_name)
							->param('is_active', $is_active)
							->param('user_type', $user_type)
							->param('google_id', $google_id)
							->param('created_at', $created_at)
							->param('updated_at', $updated_at)
							->execute();

			if ($result) {
				$admin = new self();
				$admin->id = $result[0];
				$admin->username = $data['username'];
				$admin->email = $data['email'];
				$admin->password = $hashed_password;
				$admin->full_name = $full_name;
				$admin->is_active = $is_active;
				$admin->user_type = $user_type;
				$admin->google_id = $google_id;
				$admin->created_at = $created_at;
				$admin->updated_at = $updated_at;
				return $admin;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Lấy danh sách tất cả admin
	 * 
	 * @param int $limit Giới hạn số lượng
	 * @param int $offset Vị trí bắt đầu
	 * @param string $search Từ khóa tìm kiếm
	 * @param string $status Trạng thái filter
	 * @param string $sort Sắp xếp
	 * @return array
	 */
	public static function get_all_admins($limit = 10, $offset = 0, $search = '', $status = '', $sort = 'created_at_desc')
	{
		try {
			$where_conditions = array("deleted_at IS NULL");
			$params = array('limit' => $limit, 'offset' => $offset);
			
			// Thêm điều kiện tìm kiếm
			if (!empty($search)) {
				$where_conditions[] = "(username LIKE :search OR email LIKE :search OR full_name LIKE :search)";
				$params['search'] = '%' . $search . '%';
			}
			
			// Thêm điều kiện trạng thái
			if ($status === 'active') {
				$where_conditions[] = "is_active = 1";
			} elseif ($status === 'inactive') {
				$where_conditions[] = "is_active = 0";
			}
			
			// Xây dựng ORDER BY
			$order_by = "ORDER BY ";
			switch ($sort) {
				case 'created_at_asc':
					$order_by .= "created_at ASC";
					break;
				case 'username_asc':
					$order_by .= "username ASC";
					break;
				case 'username_desc':
					$order_by .= "username DESC";
					break;
				case 'last_login_desc':
					$order_by .= "last_login DESC";
					break;
				default:
					$order_by .= "created_at DESC";
			}
			
			$sql = "SELECT * FROM admins WHERE " . implode(' AND ', $where_conditions) . " " . $order_by . " LIMIT :limit OFFSET :offset";
			$query = \DB::query($sql);
			
			foreach ($params as $key => $value) {
				$query->param($key, $value);
			}
			
			$results = $query->execute();
			$admins = array();

			foreach ($results as $result) {
				$admin = new self();
				foreach ($result as $key => $value) {
					$admin->$key = $value;
				}
				$admins[] = $admin;
			}

			return $admins;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Đếm tổng số admin
	 * 
	 * @param string $search Từ khóa tìm kiếm
	 * @param string $status Trạng thái filter
	 * @return int
	 */
	public static function count_all($search = '', $status = '')
	{
		try {
			$where_conditions = array("deleted_at IS NULL");
			$params = array();
			
			// Thêm điều kiện tìm kiếm
			if (!empty($search)) {
				$where_conditions[] = "(username LIKE :search OR email LIKE :search OR full_name LIKE :search)";
				$params['search'] = '%' . $search . '%';
			}
			
			// Thêm điều kiện trạng thái
			if ($status === 'active') {
				$where_conditions[] = "is_active = 1";
			} elseif ($status === 'inactive') {
				$where_conditions[] = "is_active = 0";
			}
			
			$sql = "SELECT COUNT(*) as total FROM admins WHERE " . implode(' AND ', $where_conditions);
			$query = \DB::query($sql);
			
			foreach ($params as $key => $value) {
				$query->param($key, $value);
			}
			
			$result = $query->execute();
			return (int) $result->current()['total'];
		} catch (\Exception $e) {
			return 0;
		}
	}

	/**
	 * Lấy danh sách admin đã xóa (soft delete)
	 *
	 * @param int $limit
	 * @param int $offset
	 * @param string $search
	 * @param string $sort
	 * @return array
	 */
	public static function get_deleted_admins($limit = 10, $offset = 0, $search = '', $sort = 'deleted_at_desc')
	{
		try {
			$where_conditions = array("deleted_at IS NOT NULL");
			$params = array('limit' => $limit, 'offset' => $offset);
			
			// Thêm điều kiện tìm kiếm
			if (!empty($search)) {
				$where_conditions[] = "(username LIKE :search OR email LIKE :search OR full_name LIKE :search)";
				$params['search'] = '%' . $search . '%';
			}
			
			// Xây dựng ORDER BY
			$order_by = "ORDER BY ";
			switch ($sort) {
				case 'deleted_at_asc':
					$order_by .= "deleted_at ASC";
					break;
				case 'username_asc':
					$order_by .= "username ASC";
					break;
				case 'username_desc':
					$order_by .= "username DESC";
					break;
				default:
					$order_by .= "deleted_at DESC";
			}
			
			$sql = "SELECT * FROM admins WHERE " . implode(' AND ', $where_conditions) . " " . $order_by . " LIMIT :limit OFFSET :offset";
			$query = \DB::query($sql);
			
			foreach ($params as $key => $value) {
				$query->param($key, $value);
			}
			
			$results = $query->execute();
			$admins = array();

			foreach ($results as $result) {
				$admin = new self();
				foreach ($result as $key => $value) {
					$admin->$key = $value;
				}
				$admins[] = $admin;
			}

			return $admins;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Đếm tổng số admin đã xóa (soft delete)
	 *
	 * @param string $search
	 * @return int
	 */
	public static function count_deleted($search = '')
	{
		try {
			$where_conditions = array("deleted_at IS NOT NULL");
			$params = array();
			
			// Thêm điều kiện tìm kiếm
			if (!empty($search)) {
				$where_conditions[] = "(username LIKE :search OR email LIKE :search OR full_name LIKE :search)";
				$params['search'] = '%' . $search . '%';
			}
			
			$sql = "SELECT COUNT(*) as total FROM admins WHERE " . implode(' AND ', $where_conditions);
			$query = \DB::query($sql);
			
			foreach ($params as $key => $value) {
				$query->param($key, $value);
			}
			
			$result = $query->execute();
			return (int) $result->current()['total'];
		} catch (\Exception $e) {
			return 0;
		}
	}

	/**
	 * Khôi phục admin đã xóa (soft delete)
	 *
	 * @return bool
	 */
	public function restore()
	{
		try {
			$updated_at = date('Y-m-d H:i:s');
			$query = \DB::query("UPDATE admins SET deleted_at = NULL, updated_at = :updated_at WHERE id = :id");
			$result = $query->param('updated_at', $updated_at)
						->param('id', $this->id)
						->execute();

			if ($result) {
				$this->deleted_at = null;
				$this->updated_at = $updated_at;
				return true;
			}
			return false;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Soft delete admin
	 * 
	 * @return bool
	 */
	public function soft_delete()
	{
		try {
			$deleted_at = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');
			
			$query = \DB::query("UPDATE admins SET deleted_at = :deleted_at, updated_at = :updated_at WHERE id = :id");
			$result = $query->param('deleted_at', $deleted_at)
							->param('updated_at', $updated_at)
							->param('id', $this->id)
							->execute();
				
			if ($result) {
				$this->deleted_at = $deleted_at;
				$this->updated_at = $updated_at;
				return true;
			}
			return false;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Xóa vĩnh viễn admin (hard delete)
	 *
	 * @return bool
	 */
	public function hard_delete()
	{
		try {
			$query = \DB::query("DELETE FROM admins WHERE id = :id");
			$result = $query->param('id', $this->id)->execute();
			return (bool) $result;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Soft delete hàng loạt
	 *
	 * @param array $ids
	 * @return int số bản ghi bị ảnh hưởng
	 */
	public static function bulk_soft_delete(array $ids)
	{
		if (empty($ids)) {
			return 0;
		}
		try {
			$deleted_at = date('Y-m-d H:i:s');
			$updated_at = $deleted_at;
			$placeholders = array();
			$params = array('deleted_at' => $deleted_at, 'updated_at' => $updated_at);
			foreach ($ids as $index => $id) {
				$key = 'id_'.$index;
				$placeholders[] = ':'.$key;
				$params[$key] = (int) $id;
			}
			$sql = "UPDATE admins SET deleted_at = :deleted_at, updated_at = :updated_at WHERE id IN (".implode(',', $placeholders).")";
			$query = \DB::query($sql);
			foreach ($params as $k => $v) { $query->param($k, $v); }
			$result = $query->execute();
			return (int) $result;
		} catch (\Exception $e) {
			return 0;
		}
	}

	/**
	 * Khôi phục hàng loạt
	 *
	 * @param array $ids
	 * @return int
	 */
	public static function bulk_restore(array $ids)
	{
		if (empty($ids)) {
			return 0;
		}
		try {
			$updated_at = date('Y-m-d H:i:s');
			$placeholders = array();
			$params = array('updated_at' => $updated_at);
			foreach ($ids as $index => $id) {
				$key = 'id_'.$index;
				$placeholders[] = ':'.$key;
				$params[$key] = (int) $id;
			}
			$sql = "UPDATE admins SET deleted_at = NULL, updated_at = :updated_at WHERE id IN (".implode(',', $placeholders).")";
			$query = \DB::query($sql);
			foreach ($params as $k => $v) { $query->param($k, $v); }
			$result = $query->execute();
			return (int) $result;
		} catch (\Exception $e) {
			return 0;
		}
	}

	/**
	 * Hard delete hàng loạt
	 *
	 * @param array $ids
	 * @return int
	 */
	public static function bulk_hard_delete(array $ids)
	{
		if (empty($ids)) {
			return 0;
		}
		try {
			$placeholders = array();
			$params = array();
			foreach ($ids as $index => $id) {
				$key = 'id_'.$index;
				$placeholders[] = ':'.$key;
				$params[$key] = (int) $id;
			}
			$sql = "DELETE FROM admins WHERE id IN (".implode(',', $placeholders).")";
			$query = \DB::query($sql);
			foreach ($params as $k => $v) { $query->param($k, $v); }
			$result = $query->execute();
			return (int) $result;
		} catch (\Exception $e) {
			return 0;
		}
	}

	/**
	 * Cập nhật thông tin admin
	 * 
	 * @param array $data Dữ liệu cần cập nhật
	 * @return bool
	 */
	public function update_admin(array $data)
	{
		try {
			$updated_at = date('Y-m-d H:i:s');
			$set_parts = array();
			$params = array('id' => $this->id, 'updated_at' => $updated_at);

			// Xây dựng câu lệnh UPDATE động
			if (isset($data['username'])) {
				$set_parts[] = 'username = :username';
				$params['username'] = $data['username'];
			}
			if (isset($data['email'])) {
				$set_parts[] = 'email = :email';
				$params['email'] = $data['email'];
			}
			if (isset($data['full_name'])) {
				$set_parts[] = 'full_name = :full_name';
				$params['full_name'] = $data['full_name'];
			}
			if (isset($data['is_active'])) {
				$set_parts[] = 'is_active = :is_active';
				$params['is_active'] = $data['is_active'];
			}
			if (isset($data['user_type'])) {
				$set_parts[] = 'user_type = :user_type';
				$params['user_type'] = $data['user_type'];
			}
			if (isset($data['password'])) {
				$set_parts[] = 'password = :password';
				$params['password'] = self::hash_password($data['password']);
			}

			if (empty($set_parts)) {
				return false;
			}

			$sql = "UPDATE admins SET " . implode(', ', $set_parts) . ", updated_at = :updated_at WHERE id = :id";
			$query = \DB::query($sql);
			
			foreach ($params as $key => $value) {
				$query->param($key, $value);
			}
			
			$result = $query->execute();
			
			if ($result) {
				// Cập nhật properties của object
				foreach ($data as $key => $value) {
					if ($key !== 'password') {
						$this->$key = $value;
					}
				}
				$this->updated_at = $updated_at;
				return true;
			}
			return false;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Lưu thay đổi admin
	 * 
	 * @return bool
	 */
	public function save()
	{
		try {
			$updated_at = date('Y-m-d H:i:s');
			
			$query = \DB::query("UPDATE admins SET username = :username, email = :email, full_name = :full_name, is_active = :is_active, updated_at = :updated_at WHERE id = :id");
			$result = $query->param('username', $this->username)
							->param('email', $this->email)
							->param('full_name', $this->full_name)
							->param('is_active', $this->is_active)
							->param('updated_at', $updated_at)
							->param('id', $this->id)
							->execute();
				
			if ($result) {
				$this->updated_at = $updated_at;
				return true;
			}
			return false;
		} catch (\Exception $e) {
			return false;
		}
	}
}
