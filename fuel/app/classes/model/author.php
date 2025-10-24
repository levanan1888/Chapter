<?php

/**
 * Model Author
 * 
 * Quản lý dữ liệu tác giả
 * 
 * @package    App
 * @subpackage Model
 */
class Model_Author extends \Model
{
	/**
	 * Tên bảng trong database
	 * 
	 * @var string
	 */
	protected static $_table_name = 'authors';

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
		'name',
		'slug',
		'description',
		'avatar',
		'is_active',
		'created_at',
		'updated_at',
		'deleted_at',
		'story_count',
	);

	/**
	 * Các properties của model
	 */
	public $id;
	public $name;
	public $slug;
	public $description;
	public $avatar;
	public $is_active;
	public $created_at;
	public $updated_at;
	public $deleted_at;
	public $story_count;

	/**
	 * Tìm author theo ID
	 * 
	 * @param int $id ID của author
	 * @return Model_Author|null
	 */
	public static function find($id)
	{
		try {
			$query = \DB::query("SELECT * FROM authors WHERE id = :id LIMIT 1");
			$result = $query->param('id', $id)->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$author = new self();
				foreach ($data as $key => $value) {
					if (property_exists($author, $key)) {
						$author->$key = $value;
					}
				}
				return $author;
			}

			return null;
		} catch (\Exception $e) {
			\Log::error('Error finding author: ' . $e->getMessage());
			return null;
		}
	}

	/**
	 * Tìm author theo slug
	 * 
	 * @param string $slug Slug của author
	 * @return Model_Author|null
	 */
	public static function find_by_slug($slug)
	{
		try {
			$query = \DB::query("SELECT * FROM authors WHERE slug = :slug");
			$result = $query->param('slug', $slug)->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$author = new self();
				foreach ($data as $key => $value) {
					$author->$key = $value;
				}
				return $author;
			}

			return null;
		} catch (\Exception $e) {
			\Log::error('Error finding author by slug: ' . $e->getMessage());
			return null;
		}
	}

	/**
	 * Tìm author theo tên
	 * 
	 * @param string $name Tên author
	 * @return Model_Author|null
	 */
	public static function find_by_name($name)
	{
		try {
			$query = \DB::query("SELECT * FROM authors WHERE name = :name");
			$result = $query->param('name', $name)->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$author = new self();
				foreach ($data as $key => $value) {
					$author->$key = $value;
				}
				return $author;
			}

			return null;
		} catch (\Exception $e) {
			\Log::error('Error finding author by name: ' . $e->getMessage());
			return null;
		}
	}

	/**
	 * Lấy danh sách tất cả authors
	 * 
	 * @param int $limit Giới hạn số lượng
	 * @param int $offset Vị trí bắt đầu
	 * @return array
	 */
	public static function get_all_authors($limit = null, $offset = 0, $search = '', $status = '', $sort = 'created_at_desc')
	{
		try {
			$sql = "SELECT a.*, COUNT(s.id) as story_count FROM authors a LEFT JOIN stories s ON a.id = s.author_id AND s.deleted_at IS NULL WHERE 1=1";
			$params = array();
			
			// Filter theo trạng thái
			if ($status === 'active') {
				$sql .= " AND a.is_active = 1 AND a.deleted_at IS NULL";
			} elseif ($status === 'inactive') {
				$sql .= " AND a.is_active = 0 AND a.deleted_at IS NULL";
			} elseif ($status === 'deleted') {
				$sql .= " AND a.deleted_at IS NOT NULL";
			} else {
				// Mặc định hiển thị cả active và inactive (không bao gồm deleted)
				$sql .= " AND a.deleted_at IS NULL";
			}
			
			// Tìm kiếm theo tên
			if (!empty($search)) {
				$sql .= " AND a.name LIKE :search";
				$params['search'] = '%' . $search . '%';
			}
			
			// GROUP BY để sử dụng COUNT
			$sql .= " GROUP BY a.id";
			
			// Sắp xếp
			$sort_parts = explode('_', $sort);
			$sort_field = $sort_parts[0];
			$sort_direction = strtoupper($sort_parts[1]);
			
			$allowed_fields = array('name', 'created_at');
			if (in_array($sort_field, $allowed_fields)) {
				$sql .= " ORDER BY a." . $sort_field . " " . $sort_direction;
			} else {
				$sql .= " ORDER BY a.created_at DESC";
			}
			
			if ($limit) {
				$sql .= " LIMIT :limit OFFSET :offset";
			}
			
			$query = \DB::query($sql);
			
			// Bind parameters
			foreach ($params as $key => $value) {
				$query->param($key, $value);
			}
			
			if ($limit) {
				$query->param('limit', $limit)->param('offset', $offset);
			}
			
			$results = $query->execute();
			$authors = array();

			foreach ($results as $result) {
				$author = new self();
				foreach ($result as $key => $value) {
					if (property_exists($author, $key)) {
						$author->$key = $value;
					}
				}
				$authors[] = $author;
			}

			return $authors;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Đếm tổng số authors với filter
	 * 
	 * @param string $search Từ khóa tìm kiếm
	 * @param string $status Trạng thái
	 * @return int
	 */
	public static function count_all($search = '', $status = '')
	{
		try {
			$sql = "SELECT COUNT(*) as total FROM authors WHERE 1=1";
			$params = array();
			
			// Filter theo trạng thái
			if ($status === 'active') {
				$sql .= " AND is_active = 1 AND deleted_at IS NULL";
			} elseif ($status === 'inactive') {
				$sql .= " AND is_active = 0 AND deleted_at IS NULL";
			} elseif ($status === 'deleted') {
				$sql .= " AND deleted_at IS NOT NULL";
			} else {
				// Mặc định đếm cả active và inactive (không bao gồm deleted)
				$sql .= " AND deleted_at IS NULL";
			}
			
			// Tìm kiếm theo tên
			if (!empty($search)) {
				$sql .= " AND name LIKE :search";
				$params['search'] = '%' . $search . '%';
			}
			
			$query = \DB::query($sql);
			
			// Bind parameters
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
	 * Tạo author mới
	 * 
	 * @param array $data Dữ liệu author
	 * @return Model_Author|null
	 */
	public static function create_author(array $data)
	{
		try {
			// Kiểm tra dữ liệu đầu vào
			if (empty($data['name'])) {
				return null;
			}

			// Tạo slug từ name nếu chưa có
			if (empty($data['slug'])) {
				$data['slug'] = self::create_slug($data['name']);
			}

			// Đặt giá trị mặc định
			$description = isset($data['description']) ? $data['description'] : '';
			$avatar = isset($data['avatar']) ? $data['avatar'] : null;
			$is_active = isset($data['is_active']) ? (int) $data['is_active'] : 1;
			$created_at = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');

			// Thêm vào database với Raw SQL
			$query = \DB::query("INSERT INTO authors (name, slug, description, avatar, is_active, created_at, updated_at) VALUES (:name, :slug, :description, :avatar, :is_active, :created_at, :updated_at)");

			$result = $query->param('name', $data['name'])
							->param('slug', $data['slug'])
							->param('description', $description)
							->param('avatar', $avatar)
							->param('is_active', $is_active)
							->param('created_at', $created_at)
							->param('updated_at', $updated_at)
							->execute();

			if ($result) {
				$author = new self();
				$author->id = $result[0];
				$author->name = $data['name'];
				$author->slug = $data['slug'];
				$author->description = $description;
				$author->avatar = $avatar;
				$author->is_active = $is_active;
				$author->created_at = $created_at;
				$author->updated_at = $updated_at;
				return $author;
			}

			return null;
		} catch (\Exception $e) {
			\Log::error('Error creating author: ' . $e->getMessage());
			return null;
		}
	}

	/**
	 * Cập nhật thông tin author
	 * 
	 * @param array $data Dữ liệu cần cập nhật
	 * @return bool
	 */
	public function update_author(array $data)
	{
		try {
			$updated_at = date('Y-m-d H:i:s');
			$set_parts = array();
			$params = array('id' => $this->id, 'updated_at' => $updated_at);

			// Xây dựng câu lệnh UPDATE động
			if (isset($data['name'])) {
				$set_parts[] = 'name = :name';
				$params['name'] = $data['name'];
			}
			if (isset($data['slug'])) {
				$set_parts[] = 'slug = :slug';
				$params['slug'] = $data['slug'];
			}
			if (isset($data['description'])) {
				$set_parts[] = 'description = :description';
				$params['description'] = $data['description'];
			}
			if (isset($data['avatar'])) {
				$set_parts[] = 'avatar = :avatar';
				$params['avatar'] = $data['avatar'];
			}
			if (isset($data['is_active'])) {
				$set_parts[] = 'is_active = :is_active';
				$params['is_active'] = $data['is_active'];
			}

			if (empty($set_parts)) {
				return false;
			}

			$sql = "UPDATE authors SET " . implode(', ', $set_parts) . ", updated_at = :updated_at WHERE id = :id";
			$query = \DB::query($sql);
			
			foreach ($params as $key => $value) {
				$query->param($key, $value);
			}
			
			$result = $query->execute();
			
			if ($result) {
				// Cập nhật properties của object
				foreach ($data as $key => $value) {
					$this->$key = $value;
				}
				$this->updated_at = $updated_at;
				return true;
			}
			return false;
		} catch (\Exception $e) {
			\Log::error('Error updating author: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Xóa author (soft delete)
	 * 
	 * @return bool
	 */
	public function soft_delete()
	{
		try {
			$updated_at = date('Y-m-d H:i:s');
			$deleted_at = date('Y-m-d H:i:s');
			
			$query = \DB::query("UPDATE authors SET is_active = :is_active, deleted_at = :deleted_at, updated_at = :updated_at WHERE id = :id");
			$result = $query->param('is_active', 0)
							->param('deleted_at', $deleted_at)
							->param('updated_at', $updated_at)
							->param('id', $this->id)
							->execute();
				
			if ($result !== false) {
				$this->is_active = 0;
				$this->deleted_at = $deleted_at;
				$this->updated_at = $updated_at;
				return true;
			}
			return false;
		} catch (\Exception $e) {
			\Log::error('Error soft deleting author: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Restore author from trash
	 * 
	 * @return bool
	 */
	public function restore()
	{
		try {
			$updated_at = date('Y-m-d H:i:s');
			
			$query = \DB::query("UPDATE authors SET is_active = :is_active, deleted_at = NULL, updated_at = :updated_at WHERE id = :id");
			$result = $query->param('is_active', 1)
							->param('updated_at', $updated_at)
							->param('id', $this->id)
							->execute();

			if ($result !== false) {
				$this->is_active = 1;
				$this->deleted_at = null;
				$this->updated_at = $updated_at;
				return true;
			}
			return false;
		} catch (\Exception $e) {
			\Log::error('Error restoring author: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Force delete author permanently
	 * 
	 * @return bool
	 */
	public function force_delete()
	{
		try {
			$query = \DB::query("DELETE FROM authors WHERE id = :id");
			$result = $query->param('id', $this->id)->execute();
			return $result !== false;
		} catch (\Exception $e) {
			\Log::error('Error force deleting author: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Lấy danh sách tác giả đã xóa
	 * 
	 * @param int $limit Số lượng tác giả trên mỗi trang
	 * @param int $offset Vị trí bắt đầu
	 * @param string $search Từ khóa tìm kiếm
	 * @param string $sort Sắp xếp
	 * @return array
	 */
	public static function get_deleted_authors($limit = null, $offset = 0, $search = '', $sort = 'deleted_at_desc')
	{
		try {
			$sql = "SELECT a.*, COUNT(s.id) as story_count FROM authors a LEFT JOIN stories s ON a.id = s.author_id AND s.deleted_at IS NULL WHERE a.deleted_at IS NOT NULL";
			$params = array();
			
			// Tìm kiếm theo tên
			if (!empty($search)) {
				$sql .= " AND a.name LIKE :search";
				$params['search'] = '%' . $search . '%';
			}
			
			// GROUP BY để sử dụng COUNT
			$sql .= " GROUP BY a.id";
			
			// Sắp xếp
			$sort_parts = explode('_', $sort);
			$sort_field = $sort_parts[0];
			$sort_direction = strtoupper($sort_parts[1]);
			
			$allowed_fields = array('name', 'created_at', 'deleted_at');
			if (in_array($sort_field, $allowed_fields)) {
				$sql .= " ORDER BY a." . $sort_field . " " . $sort_direction;
			} else {
				$sql .= " ORDER BY a.deleted_at DESC";
			}
			
			if ($limit) {
				$sql .= " LIMIT :limit OFFSET :offset";
			}
			
			$query = \DB::query($sql);
			
			// Bind parameters
			foreach ($params as $key => $value) {
				$query->param($key, $value);
			}
			
			if ($limit) {
				$query->param('limit', $limit)->param('offset', $offset);
			}
			
			$results = $query->execute();
			$authors = array();

			foreach ($results as $result) {
				$author = new self();
				foreach ($result as $key => $value) {
					if (property_exists($author, $key)) {
						$author->$key = $value;
					}
				}
				$authors[] = $author;
			}

			return $authors;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Đếm số tác giả đã xóa
	 * 
	 * @param string $search Từ khóa tìm kiếm
	 * @return int
	 */
	public static function count_deleted($search = '')
	{
		try {
			$sql = "SELECT COUNT(*) as total FROM authors WHERE deleted_at IS NOT NULL";
			$params = array();
			
			// Tìm kiếm theo tên
			if (!empty($search)) {
				$sql .= " AND name LIKE :search";
				$params['search'] = '%' . $search . '%';
			}
			
			$query = \DB::query($sql);
			
			// Bind parameters
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
	 * Tạo slug từ tên
	 * 
	 * @param string $name Tên author
	 * @return string
	 */
	public static function create_slug($name)
	{
		// Chuyển về chữ thường
		$slug = strtolower($name);
		
		// Thay thế ký tự đặc biệt
		$slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
		
		// Thay thế khoảng trắng và dấu gạch ngang
		$slug = preg_replace('/[\s-]+/', '-', $slug);
		
		// Loại bỏ dấu gạch ngang ở đầu và cuối
		$slug = trim($slug, '-');
		
		// Kiểm tra slug trùng lặp
		$original_slug = $slug;
		$counter = 1;
		while (self::slug_exists($slug)) {
			$slug = $original_slug . '-' . $counter;
			$counter++;
		}
		
		return $slug;
	}

	/**
	 * Kiểm tra slug đã tồn tại chưa
	 * 
	 * @param string $slug Slug cần kiểm tra
	 * @return bool
	 */
	public static function slug_exists($slug)
	{
		try {
			$query = \DB::query("SELECT COUNT(*) as total FROM authors WHERE slug = :slug");
			$result = $query->param('slug', $slug)->execute();
			return (int) $result->current()['total'] > 0;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Lấy danh sách truyện của author
	 * 
	 * @param int $limit Giới hạn số lượng
	 * @param int $offset Vị trí bắt đầu
	 * @return array
	 */
	public function get_stories($limit = null, $offset = 0)
	{
		try {
			$sql = "SELECT * FROM stories WHERE author_id = :author_id AND deleted_at IS NULL ORDER BY created_at DESC";
			if ($limit) {
				$sql .= " LIMIT :limit OFFSET :offset";
			}
			
			$query = \DB::query($sql);
			$query->param('author_id', $this->id);
			if ($limit) {
				$query->param('limit', $limit)->param('offset', $offset);
			}
			
			$results = $query->execute();
			$stories = array();

			foreach ($results as $result) {
				$story = new Model_Story();
				foreach ($result as $key => $value) {
					$story->$key = $value;
				}
				$stories[] = $story;
			}

			return $stories;
		} catch (\Exception $e) {
			\Log::error('Error getting author stories: ' . $e->getMessage());
			return array();
		}
	}

	/**
	 * Lấy số lượng truyện của author
	 * 
	 * @return int
	 */
	public function get_story_count()
	{
		try {
			$query = \DB::query("SELECT COUNT(*) as total FROM stories WHERE author_id = :author_id AND deleted_at IS NULL");
			$result = $query->param('author_id', $this->id)->execute();
			return (int) $result->current()['total'];
		} catch (\Exception $e) {
			\Log::error('Error getting author story count: ' . $e->getMessage());
			return 0;
		}
	}
}
