<?php

/**
 * Model Category
 * 
 * Quản lý dữ liệu danh mục truyện
 * 
 * @package    App
 * @subpackage Model
 */
class Model_Category extends \Model
{
	/**
	 * Tên bảng trong database
	 * 
	 * @var string
	 */
	protected static $_table_name = 'categories';

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
		'color',
		'is_active',
		'sort_order',
		'created_at',
		'updated_at',
		'deleted_at',
	);

	/**
	 * Các properties của model
	 */
	public $id;
	public $name;
	public $slug;
	public $description;
	public $color;
	public $is_active;
	public $sort_order;
	public $created_at;
	public $updated_at;
	public $deleted_at;
	// Computed, non-persisted property used in admin listing
	public $story_count = 0;

	/**
	 * Tìm category theo ID
	 * 
	 * @param int $id ID của category
	 * @return Model_Category|null
	 */
	public static function find($id)
	{
		try {
			$query = \DB::query("SELECT * FROM categories WHERE id = :id AND is_active = :is_active");
			$result = $query->param('id', $id)->param('is_active', 1)->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$category = new self();
				foreach ($data as $key => $value) {
					$category->$key = $value;
				}
				return $category;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Tìm category theo slug
	 * 
	 * @param string $slug Slug của category
	 * @return Model_Category|null
	 */
	public static function find_by_slug($slug)
	{
		try {
			$query = \DB::query("SELECT * FROM categories WHERE slug = :slug AND is_active = :is_active");
			$result = $query->param('slug', $slug)->param('is_active', 1)->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$category = new self();
				foreach ($data as $key => $value) {
					$category->$key = $value;
				}
				return $category;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Tìm category đã xóa theo ID (dùng cho restore)
	 * 
	 * @param int $id ID của category
	 * @return Model_Category|null
	 */
	public static function find_deleted($id)
	{
		try {
			$query = \DB::query("SELECT * FROM categories WHERE id = :id AND is_active = :is_active AND deleted_at IS NOT NULL");
			$result = $query->param('id', $id)->param('is_active', 0)->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$category = new self();
				foreach ($data as $key => $value) {
					$category->$key = $value;
				}
				return $category;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Lấy danh sách tất cả categories
	 * 
	 * @param int $limit Giới hạn số lượng
	 * @param int $offset Vị trí bắt đầu
	 * @return array
	 */
	public static function get_all_categories($limit = null, $offset = 0)
	{
		try {
			$sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, name ASC";
			if ($limit) {
				$sql .= " LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
			}
			$results = \DB::query($sql)->execute();			$categories = array();
			foreach ($results as $row) {
				$category = new self();
				foreach ($row as $key => $value) {
					if (!is_string($key)) {
						continue;
					}
					$category->$key = $value;
				}
				$categories[] = $category;
			}
			return $categories;
		} catch (\Exception $e) {
			\Log::error('get_all_categories failed: ' . $e->getMessage());
			return array();
		}
	}

	/**
	 * Lấy danh sách categories có tìm kiếm, lọc trạng thái và phân trang
	 *
	 * @param string|null $keyword
	 * @param string $status 'active' | 'inactive' | 'all'
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public static function get_categories($keyword = null, $status = 'active', $limit = 12, $offset = 0)
	{
		try {
			$whereParts = array();
			$params = array();

			if ($status === 'active') {
				$whereParts[] = 'is_active = :is_active';
				$params['is_active'] = 1;
			} elseif ($status === 'inactive') {
				$whereParts[] = 'is_active = :is_active';
				$params['is_active'] = 0;
			}

			if ($keyword !== null && $keyword !== '') {
				$whereParts[] = '(name LIKE :kw OR slug LIKE :kw)';
				$params['kw'] = '%' . $keyword . '%';
			}

			$sql = 'SELECT * FROM categories';
			if (!empty($whereParts)) {
				$sql .= ' WHERE ' . implode(' AND ', $whereParts);
			}
			$sql .= ' ORDER BY sort_order ASC, name ASC';
			$sql .= ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;

			$query = \DB::query($sql);
			foreach ($params as $k => $v) {
				$query->param($k, $v);
			}

			$results = $query->execute();
			$categories = array();
			foreach ($results as $row) {
				$category = new self();
				foreach ($row as $key => $value) {
					if (!is_string($key)) {
						continue;
					}
					$category->$key = $value;
				}
				$categories[] = $category;
			}
			return $categories;
		} catch (\Exception $e) {
			\Log::error('get_categories failed: ' . $e->getMessage());
			return array();
		}
	}

	/**
	 * Đếm tổng số categories theo bộ lọc
	 *
	 * @param string|null $keyword
	 * @param string $status
	 * @return int
	 */
	public static function count_filtered($keyword = null, $status = 'active')
	{
		try {
			$whereParts = array();
			$params = array();

			if ($status === 'active') {
				$whereParts[] = 'is_active = :is_active';
				$params['is_active'] = 1;
			} elseif ($status === 'inactive') {
				$whereParts[] = 'is_active = :is_active';
				$params['is_active'] = 0;
			}

			if ($keyword !== null && $keyword !== '') {
				$whereParts[] = '(name LIKE :kw OR slug LIKE :kw)';
				$params['kw'] = '%' . $keyword . '%';
			}

			$sql = 'SELECT COUNT(*) as total FROM categories';
			if (!empty($whereParts)) {
				$sql .= ' WHERE ' . implode(' AND ', $whereParts);
			}

			$query = \DB::query($sql);
			foreach ($params as $k => $v) {
				$query->param($k, $v);
			}

			$result = $query->execute();
			return (int) $result->current()['total'];
		} catch (\Exception $e) {
			\Log::error('count_filtered failed: ' . $e->getMessage());
			return 0;
		}
	}

	/**
	 * Đếm tổng số categories
	 * 
	 * @return int
	 */
	public static function count_all()
	{
		try {
			$query = \DB::query("SELECT COUNT(*) as total FROM categories WHERE is_active = :is_active");
			$result = $query->param('is_active', 1)->execute();
			return (int) $result->current()['total'];
		} catch (\Exception $e) {
			return 0;
		}
	}

	/**
	 * Tạo category mới
	 * 
	 * @param array $data Dữ liệu category
	 * @return Model_Category|null
	 */
	public static function create_category(array $data)
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
			$color = isset($data['color']) ? $data['color'] : '#007bff';
			$is_active = isset($data['is_active']) ? $data['is_active'] : 1;
			$sort_order = isset($data['sort_order']) ? $data['sort_order'] : 0;
			$created_at = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');

			// Thêm vào database với Raw SQL
			$query = \DB::query("INSERT INTO categories (name, slug, description, color, is_active, sort_order, created_at, updated_at) VALUES (:name, :slug, :description, :color, :is_active, :sort_order, :created_at, :updated_at)");
			$result = $query->param('name', $data['name'])
							->param('slug', $data['slug'])
							->param('description', $description)
							->param('color', $color)
							->param('is_active', $is_active)
							->param('sort_order', $sort_order)
							->param('created_at', $created_at)
							->param('updated_at', $updated_at)
							->execute();

			if ($result) {
				$category = new self();
				$category->id = $result[0];
				$category->name = $data['name'];
				$category->slug = $data['slug'];
				$category->description = $description;
				$category->color = $color;
				$category->is_active = $is_active;
				$category->sort_order = $sort_order;
				$category->created_at = $created_at;
				$category->updated_at = $updated_at;
				return $category;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Cập nhật thông tin category
	 * 
	 * @param array $data Dữ liệu cần cập nhật
	 * @return bool
	 */
	public function update_category(array $data)
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
			if (isset($data['color'])) {
				$set_parts[] = 'color = :color';
				$params['color'] = $data['color'];
			}
			if (isset($data['is_active'])) {
				$set_parts[] = 'is_active = :is_active';
				$params['is_active'] = $data['is_active'];
			}
			if (isset($data['sort_order'])) {
				$set_parts[] = 'sort_order = :sort_order';
				$params['sort_order'] = $data['sort_order'];
			}

			if (empty($set_parts)) {
				return false;
			}

			$sql = "UPDATE categories SET " . implode(', ', $set_parts) . ", updated_at = :updated_at WHERE id = :id";
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
			return false;
		}
	}

	/**
	 * Xóa category (soft delete)
	 * 
	 * @return bool
	 */
	public function soft_delete()
	{
		try {
			$updated_at = date('Y-m-d H:i:s');
			$deleted_at = date('Y-m-d H:i:s');
			
			$query = \DB::query("UPDATE categories SET is_active = :is_active, deleted_at = :deleted_at, updated_at = :updated_at WHERE id = :id");
			$result = $query->param('is_active', 0)
							->param('deleted_at', $deleted_at)
							->param('updated_at', $updated_at)
							->param('id', $this->id)
							->execute();
				
			if ($result) {
				$this->is_active = 0;
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
	 * Khôi phục category từ thùng rác
	 * 
	 * @return bool
	 */
	public function restore()
	{
		try {
			$updated_at = date('Y-m-d H:i:s');
			
			$query = \DB::query("UPDATE categories SET is_active = :is_active, deleted_at = NULL, updated_at = :updated_at WHERE id = :id");
			$result = $query->param('is_active', 1)
							->param('updated_at', $updated_at)
							->param('id', $this->id)
							->execute();
				
			if ($result) {
				$this->is_active = 1;
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
	 * Xóa vĩnh viễn category
	 * 
	 * @return bool
	 */
	public function force_delete()
	{
		try {
			$query = \DB::query("DELETE FROM categories WHERE id = :id");
			$result = $query->param('id', $this->id)->execute();
			return (bool) $result;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Lấy danh sách categories đã xóa (trong thùng rác)
	 * 
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public static function get_deleted_categories($limit = null, $offset = 0)
	{
		try {
			$sql = "SELECT * FROM categories WHERE is_active = 0 AND deleted_at IS NOT NULL ORDER BY deleted_at DESC";
			if ($limit) {
				$sql .= " LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
			}
			$results = \DB::query($sql)->execute();
			$categories = array();
			foreach ($results as $row) {
				$category = new self();
				foreach ($row as $key => $value) {
					if (!is_string($key)) {
						continue;
					}
					$category->$key = $value;
				}
				$categories[] = $category;
			}
			return $categories;
		} catch (\Exception $e) {
			\Log::error('get_deleted_categories failed: ' . $e->getMessage());
			return array();
		}
	}

	/**
	 * Đếm số categories đã xóa
	 * 
	 * @return int
	 */
	public static function count_deleted()
	{
		try {
			$query = \DB::query("SELECT COUNT(*) as total FROM categories WHERE is_active = 0 AND deleted_at IS NOT NULL");
			$result = $query->execute();
			return (int) $result->current()['total'];
		} catch (\Exception $e) {
			return 0;
		}
	}

	/**
	 * Tạo slug từ tên
	 * 
	 * @param string $name Tên category
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
			$query = \DB::query("SELECT COUNT(*) as total FROM categories WHERE slug = :slug");
			$result = $query->param('slug', $slug)->execute();
			return (int) $result->current()['total'] > 0;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Lấy số lượng truyện trong category
	 * 
	 * @return int
	 */
	public function get_story_count()
	{
		try {
			$query = \DB::query("SELECT COUNT(*) as total FROM story_categories sc 
\t\t\t\t\t\t\t\t\t\t\tINNER JOIN stories s ON sc.story_id = s.id 
\t\t\t\t\t\t\t\t\t\t\tWHERE sc.category_id = :category_id AND s.deleted_at IS NULL");
			$result = $query->param('category_id', $this->id)->execute();
			return (int) $result->current()['total'];
		} catch (\Exception $e) {
			return 0;
		}
	}

	/**
	 * Lấy số lượng truyện theo category id (dùng khi dữ liệu không phải object)
	 * 
	 * @param int $category_id
	 * @return int
	 */
	public static function get_story_count_by_id($category_id)
	{
		try {
			$query = \DB::query("SELECT COUNT(*) as total FROM story_categories sc 
\t\t\t\t\t\t\t\t\t\t\tINNER JOIN stories s ON sc.story_id = s.id 
\t\t\t\t\t\t\t\t\t\t\tWHERE sc.category_id = :category_id AND s.deleted_at IS NULL");
			$result = $query->param('category_id', (int) $category_id)->execute();
			return (int) $result->current()['total'];
		} catch (\Exception $e) {
			return 0;
		}
	}
}
