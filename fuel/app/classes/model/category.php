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
		'story_count',
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
			$query = \DB::query("SELECT * FROM categories WHERE id = :id");
			$result = $query->param('id', $id)->execute();

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
			$query = \DB::query("SELECT * FROM categories WHERE id = :id AND (is_active = 0 OR deleted_at IS NOT NULL)");
			$result = $query->param('id', $id)->execute();

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
	public static function get_categories($keyword = null, $status = 'active', $limit = 12, $offset = 0, $sort = 'created_at_desc')
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
			// For 'all' status, we don't add any is_active filter

		if ($keyword !== null && $keyword !== '') {
			$whereParts[] = '(c.name LIKE :kw OR c.slug LIKE :kw)';
			$params['kw'] = '%' . $keyword . '%';
		}

			$sql = 'SELECT c.*, COUNT(s.id) as story_count FROM categories c LEFT JOIN story_categories sc ON c.id = sc.category_id LEFT JOIN stories s ON sc.story_id = s.id AND s.deleted_at IS NULL';
			$whereParts[] = 'c.deleted_at IS NULL'; // Chỉ hiển thị danh mục chưa bị xóa
			if (!empty($whereParts)) {
				$sql .= ' WHERE ' . implode(' AND ', $whereParts);
			}
			$sql .= ' GROUP BY c.id';
			
            // Add sorting (robustly parse values like "created_at_desc")
            $parts = explode('_', (string) $sort);
            $sort_direction = strtoupper(array_pop($parts));
            $sort_field = implode('_', $parts);

            $allowed_fields = array('name', 'created_at', 'sort_order');
            $allowed_directions = array('ASC', 'DESC');

            if (!in_array($sort_field, $allowed_fields)) {
                $sort_field = 'created_at';
            }
            if (!in_array($sort_direction, $allowed_directions)) {
                $sort_direction = 'DESC';
            }

            $sql .= ' ORDER BY ' . $sort_field . ' ' . $sort_direction;
			
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
					if (property_exists($category, $key)) {
						$category->$key = $value;
					}
				}
				// Add story_count property
				$category->story_count = (int) $row['story_count'];
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
			// For 'all' status, we don't add any is_active filter

		if ($keyword !== null && $keyword !== '') {
			$whereParts[] = '(c.name LIKE :kw OR c.slug LIKE :kw)';
			$params['kw'] = '%' . $keyword . '%';
		}

			$sql = 'SELECT COUNT(*) as total FROM categories';
			$whereParts[] = 'deleted_at IS NULL'; // Chỉ đếm danh mục chưa bị xóa
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
			// Kiểm tra xem có thay đổi is_active không
			$old_is_active = $this->is_active;
			$is_active_changed = isset($data['is_active']) && $data['is_active'] != $old_is_active;
			$new_is_active = isset($data['is_active']) ? $data['is_active'] : $old_is_active;
			
			\Log::info("Category {$this->id} update_category: old_is_active = {$old_is_active}, new_is_active = {$new_is_active}, changed = " . ($is_active_changed ? 'true' : 'false'));
			
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

				// Log thay đổi trạng thái is_active (không ẩn truyện nữa)
				if ($is_active_changed) {
					\Log::info("Category {$this->id} is_active changed from {$old_is_active} to {$new_is_active}");
					// Không còn ẩn truyện khi danh mục bị ẩn
				}

				return true;
			}
			return false;
		} catch (\Exception $e) {
			\Log::error('Model_Category::update_category error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Xóa category (soft delete) với cascade effect
	 * 
	 * @return bool
	 */
	public function soft_delete()
	{
		try {
			\DB::start_transaction();
			
			$updated_at = date('Y-m-d H:i:s');
			$deleted_at = date('Y-m-d H:i:s');
			
			// 1. Xóa danh mục (soft delete)
			$query = \DB::query("UPDATE categories SET is_active = :is_active, deleted_at = :deleted_at, updated_at = :updated_at WHERE id = :id");
			$result = $query->param('is_active', 0)
							->param('deleted_at', $deleted_at)
							->param('updated_at', $updated_at)
							->param('id', $this->id)
							->execute();
				
			if ($result === false) {
				\DB::rollback_transaction();
				return false;
			}

			// 2. Ẩn tất cả truyện thuộc danh mục này
			$this->hide_related_stories();

			// 3. Xóa liên kết trong bảng story_categories
			$this->remove_story_category_links();

			\DB::commit_transaction();
			
			$this->is_active = 0;
			$this->deleted_at = $deleted_at;
			$this->updated_at = $updated_at;
			return true;
		} catch (\Exception $e) {
			\DB::rollback_transaction();
			\Log::error('Model_Category::soft_delete error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Khôi phục category từ thùng rác với cascade effect
	 * 
	 * @return bool
	 */
	public function restore()
	{
		try {
			\DB::start_transaction();
			
			$updated_at = date('Y-m-d H:i:s');
			
			// 1. Khôi phục danh mục
			$query = \DB::query("UPDATE categories SET is_active = :is_active, deleted_at = NULL, updated_at = :updated_at WHERE id = :id");
			$result = $query->param('is_active', 1)
							->param('updated_at', $updated_at)
							->param('id', $this->id)
							->execute();
				
			if ($result === false) {
				\DB::rollback_transaction();
				return false;
			}

			// 2. Hiển thị lại các truyện thuộc danh mục này (nếu chúng không bị ẩn vì lý do khác)
			$this->show_related_stories();

			\DB::commit_transaction();
			
			$this->is_active = 1;
			$this->deleted_at = null;
			$this->updated_at = $updated_at;
			return true;
		} catch (\Exception $e) {
			\DB::rollback_transaction();
			\Log::error('Model_Category::restore error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Xóa vĩnh viễn category với cascade effect
	 * 
	 * @return bool
	 */
	public function force_delete()
	{
		try {
			\DB::start_transaction();
			
			// 1. Xóa liên kết trong bảng story_categories
			$this->remove_story_category_links();
			
			// 2. Xóa vĩnh viễn danh mục
			$query = \DB::query("DELETE FROM categories WHERE id = :id");
			$result = $query->param('id', $this->id)->execute();
			
			if ($result === false) {
				\DB::rollback_transaction();
				return false;
			}

			\DB::commit_transaction();
			return true;
		} catch (\Exception $e) {
			\DB::rollback_transaction();
			\Log::error('Model_Category::force_delete error: ' . $e->getMessage());
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
	public static function get_deleted_categories($limit = null, $offset = 0, $search = '', $sort = 'deleted_at_desc')
	{
		try {
			$sql = "SELECT * FROM categories WHERE deleted_at IS NOT NULL";
			$params = array();
			
			// Tìm kiếm theo tên
			if (!empty($search)) {
				$sql .= " AND name LIKE :search";
				$params['search'] = '%' . $search . '%';
			}
			
			// Sắp xếp
			switch ($sort) {
				case 'deleted_at_asc':
					$sql .= " ORDER BY deleted_at ASC";
					break;
				case 'name_asc':
					$sql .= " ORDER BY name ASC";
					break;
				case 'name_desc':
					$sql .= " ORDER BY name DESC";
					break;
				case 'created_at_desc':
					$sql .= " ORDER BY created_at DESC";
					break;
				default:
					$sql .= " ORDER BY deleted_at DESC";
					break;
			}
			
			if ($limit) {
				$sql .= " LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
			}
			
			\Log::info("Query for deleted categories: " . $sql);
			$query = \DB::query($sql);
			foreach ($params as $key => $value) {
				$query->param($key, $value);
			}
			$results = $query->execute();
			\Log::info("Query returned " . count($results) . " results");
			
			$categories = array();
			foreach ($results as $row) {
				\Log::info("Processing category row: ID=" . $row['id'] . ", is_active=" . $row['is_active'] . ", deleted_at=" . $row['deleted_at']);
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
	public static function count_deleted($search = '')
	{
		try {
			$sql = "SELECT COUNT(*) as total FROM categories WHERE deleted_at IS NOT NULL";
			$params = array();
			
			if (!empty($search)) {
				$sql .= " AND name LIKE :search";
				$params['search'] = '%' . $search . '%';
			}
			
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

	/**
	 * Lấy danh sách truyện trong category
	 * 
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function get_stories($limit = null, $offset = 0)
	{
		try {
			$query = \DB::query("SELECT s.*, a.name as author_name FROM stories s 
									INNER JOIN story_categories sc ON s.id = sc.story_id 
									LEFT JOIN authors a ON s.author_id = a.id
									WHERE sc.category_id = :category_id AND s.deleted_at IS NULL 
									ORDER BY s.created_at DESC");
			
			if ($limit) {
				$query->limit($limit);
			}
			if ($offset) {
				$query->offset($offset);
			}
			
			$result = $query->param('category_id', $this->id)->execute();
			return $result->as_array();
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Ẩn tất cả truyện thuộc danh mục này
	 * Lưu trạng thái hiển thị ban đầu trước khi ẩn
	 * 
	 * @return bool
	 */
	private function hide_related_stories()
	{
		try {
			// Bước 1: Lưu trạng thái hiển thị ban đầu vào original_visibility
			$query1 = \DB::query("UPDATE stories s 
									INNER JOIN story_categories sc ON s.id = sc.story_id 
									SET s.original_visibility = s.is_visible, s.updated_at = :updated_at 
									WHERE sc.category_id = :category_id AND s.deleted_at IS NULL");
			$result1 = $query1->param('category_id', $this->id)
							  ->param('updated_at', date('Y-m-d H:i:s'))
							  ->execute();
			
			// Bước 2: Ẩn tất cả truyện thuộc danh mục
			$query2 = \DB::query("UPDATE stories s 
									INNER JOIN story_categories sc ON s.id = sc.story_id 
									SET s.is_visible = 0, s.updated_at = :updated_at 
									WHERE sc.category_id = :category_id AND s.deleted_at IS NULL");
			$result2 = $query2->param('category_id', $this->id)
							  ->param('updated_at', date('Y-m-d H:i:s'))
							  ->execute();
			
			\Log::info("Hidden stories for category {$this->id}, saved original visibility: {$result1}, hidden stories: {$result2}");
			return true;
		} catch (\Exception $e) {
			\Log::error('Model_Category::hide_related_stories error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Hiển thị lại các truyện thuộc danh mục này
	 * Khôi phục trạng thái hiển thị ban đầu của từng truyện
	 * 
	 * @return bool
	 */
	private function show_related_stories()
	{
		try {
			$query = \DB::query("UPDATE stories s 
									INNER JOIN story_categories sc ON s.id = sc.story_id 
									SET s.is_visible = s.original_visibility, s.updated_at = :updated_at 
									WHERE sc.category_id = :category_id AND s.deleted_at IS NULL");
			$result = $query->param('category_id', $this->id)
							->param('updated_at', date('Y-m-d H:i:s'))
							->execute();
			
			\Log::info("Restored stories visibility for category {$this->id}, affected rows: " . $result);
			return true;
		} catch (\Exception $e) {
			\Log::error('Model_Category::show_related_stories error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Xóa liên kết trong bảng story_categories
	 * 
	 * @return bool
	 */
	private function remove_story_category_links()
	{
		try {
			$query = \DB::query("DELETE FROM story_categories WHERE category_id = :category_id");
			$result = $query->param('category_id', $this->id)->execute();
			
			\Log::info("Removed story-category links for category {$this->id}, affected rows: " . $result);
			return true;
		} catch (\Exception $e) {
			\Log::error('Model_Category::remove_story_category_links error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Cập nhật trạng thái hiển thị của danh mục với cascade effect
	 * 
	 * @param int $is_active 1 = hiển thị, 0 = ẩn
	 * @return bool
	 */
	public function update_visibility($is_active)
	{
		try {
			\DB::start_transaction();
			
			$updated_at = date('Y-m-d H:i:s');
			
			// 1. Cập nhật trạng thái danh mục
			$query = \DB::query("UPDATE categories SET is_active = :is_active, updated_at = :updated_at WHERE id = :id");
			$result = $query->param('is_active', $is_active)
							->param('updated_at', $updated_at)
							->param('id', $this->id)
							->execute();
				
			if ($result === false) {
				\DB::rollback_transaction();
				return false;
			}

			// Không còn cập nhật trạng thái hiển thị của các truyện liên quan
			// Truyện sẽ vẫn hiển thị, chỉ danh mục không hoạt động

			\DB::commit_transaction();
			
			$this->is_active = $is_active;
			$this->updated_at = $updated_at;
			return true;
		} catch (\Exception $e) {
			\DB::rollback_transaction();
			\Log::error('Model_Category::update_visibility error: ' . $e->getMessage());
			return false;
		}
	}
}
