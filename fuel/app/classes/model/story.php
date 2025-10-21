<?php

/**
 * Model Story
 * 
 * Quản lý dữ liệu truyện
 * 
 * @package    App
 * @subpackage Model
 */
class Model_Story extends \Model
{
	/**
	 * Tên bảng trong database
	 * 
	 * @var string
	 */
	protected static $_table_name = 'stories';

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
		'title',
		'slug',
		'description',
		'cover_image',
		'author_id',
		'status',
		'views',
		'is_featured',
		'is_hot',
		'author_name',
		'created_at',
		'updated_at',
		'deleted_at',
	);

	/**
	 * Các properties của model
	 */
	public $id;
	public $title;
	public $slug;
	public $description;
	public $cover_image;
	public $author_id;
	public $status;
	public $views;
	public $is_featured;
	public $is_hot;
public $author_name;
	public $created_at;
	public $updated_at;
	public $deleted_at;

	/**
	 * Tìm story theo ID
	 * 
	 * @param int $id ID của story
	 * @return Model_Story|null
	 */
	public static function find($id)
	{
		try {
			$query = \DB::query("SELECT * FROM stories WHERE id = :id AND deleted_at IS NULL");
			$result = $query->param('id', $id)->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$story = new self();
				foreach ($data as $key => $value) {
					$story->$key = $value;
				}
				return $story;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Tìm story theo slug
	 * 
	 * @param string $slug Slug của story
	 * @return Model_Story|null
	 */
	public static function find_by_slug($slug)
	{
		try {
			$query = \DB::query("SELECT * FROM stories WHERE slug = :slug AND deleted_at IS NULL");
			$result = $query->param('slug', $slug)->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$story = new self();
				foreach ($data as $key => $value) {
					$story->$key = $value;
				}
				return $story;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Lấy danh sách tất cả stories
	 * 
	 * @param int $limit Giới hạn số lượng
	 * @param int $offset Vị trí bắt đầu
	 * @param string $order_by Trường sắp xếp
	 * @param string $order_direction Hướng sắp xếp
	 * @return array
	 */
    public static function get_all_stories($limit = null, $offset = 0, $order_by = 'created_at', $order_direction = 'DESC')
	{
		try {
            // Build SQL safely with whitelisted order fields
            $allowedOrderFields = array('created_at', 'updated_at', 'views', 'title', 'id');
            if (!in_array($order_by, $allowedOrderFields, true)) {
                $order_by = 'created_at';
            }
            $order_direction = strtoupper($order_direction) === 'ASC' ? 'ASC' : 'DESC';

            $sql = "SELECT s.*, a.name AS author_name
                    FROM stories s
                    LEFT JOIN authors a ON s.author_id = a.id
                    WHERE s.deleted_at IS NULL
                    ORDER BY s." . $order_by . " " . $order_direction;

            if ($limit !== null) {
                $limit = (int) $limit;
                $offset = (int) $offset;
                $sql .= " LIMIT " . $limit . " OFFSET " . $offset;
            }

            $query = \DB::query($sql);

			$results = $query->execute();
			$stories = array();
            
            foreach ($results as $result) {
				$story = new self();
				foreach ($result as $key => $value) {
					if ($key !== 'author_name') {
						$story->$key = $value;
					}
				}
				$story->author_name = $result['author_name'];
				$stories[] = $story;
			}

			return $stories;
        } catch (\Exception $e) {
            \Log::error('Model_Story::get_all_stories failed: ' . $e->getMessage());
            return array();
		}
	}

	/**
	 * Lấy truyện mới nhất
	 * 
	 * @param int $limit Giới hạn số lượng
	 * @return array
	 */
	public static function get_latest_stories($limit = 10)
	{
		return self::get_all_stories($limit, 0, 'created_at', 'DESC');
	}

	/**
	 * Lấy truyện hot
	 * 
	 * @param int $limit Giới hạn số lượng
	 * @return array
	 */
	public static function get_hot_stories($limit = 10)
	{
		try {
			$sql = "SELECT s.*, a.name as author_name FROM stories s 
					INNER JOIN authors a ON s.author_id = a.id 
					WHERE s.deleted_at IS NULL AND s.is_hot = :is_hot 
					ORDER BY s.views DESC, s.created_at DESC LIMIT :limit";
			
			$query = \DB::query($sql);
			$results = $query->param('is_hot', 1)
							->param('limit', $limit)
							->execute();
			$stories = array();

			foreach ($results as $result) {
				$story = new self();
				foreach ($result as $key => $value) {
					if ($key !== 'author_name') {
						$story->$key = $value;
					}
				}
				$story->author_name = $result['author_name'];
				$stories[] = $story;
			}

			return $stories;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Lấy truyện được xem nhiều nhất
	 * 
	 * @param int $limit Giới hạn số lượng
	 * @return array
	 */
	public static function get_most_viewed_stories($limit = 10)
	{
		return self::get_all_stories($limit, 0, 'views', 'DESC');
	}

	/**
	 * Tìm kiếm truyện
	 * 
	 * @param string $keyword Từ khóa tìm kiếm
	 * @param int $limit Giới hạn số lượng
	 * @param int $offset Vị trí bắt đầu
	 * @return array
	 */
	public static function search_stories($keyword, $limit = null, $offset = 0)
	{
		try {
			$sql = "SELECT s.*, a.name as author_name FROM stories s 
					INNER JOIN authors a ON s.author_id = a.id 
					WHERE s.deleted_at IS NULL AND (s.title LIKE :keyword OR a.name LIKE :keyword) 
					ORDER BY s.created_at DESC";
			if ($limit) {
				$sql .= " LIMIT :limit OFFSET :offset";
			}
			
			$query = \DB::query($sql);
			$query->param('keyword', '%' . $keyword . '%');
			if ($limit) {
				$query->param('limit', $limit)->param('offset', $offset);
			}
			
			$results = $query->execute();
			$stories = array();

			foreach ($results as $result) {
				$story = new self();
				foreach ($result as $key => $value) {
					if ($key !== 'author_name') {
						$story->$key = $value;
					}
				}
				$story->author_name = $result['author_name'];
				$stories[] = $story;
			}

			return $stories;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Lấy truyện theo category
	 * 
	 * @param int $category_id ID của category
	 * @param int $limit Giới hạn số lượng
	 * @param int $offset Vị trí bắt đầu
	 * @return array
	 */
	public static function get_stories_by_category($category_id, $limit = null, $offset = 0)
	{
		try {
			$sql = "SELECT s.*, a.name as author_name FROM stories s 
					INNER JOIN authors a ON s.author_id = a.id 
					INNER JOIN story_categories sc ON s.id = sc.story_id 
					WHERE s.deleted_at IS NULL AND sc.category_id = :category_id 
					ORDER BY s.created_at DESC";
			if ($limit) {
				$sql .= " LIMIT :limit OFFSET :offset";
			}
			
			$query = \DB::query($sql);
			$query->param('category_id', $category_id);
			if ($limit) {
				$query->param('limit', $limit)->param('offset', $offset);
			}
			
			$results = $query->execute();
			$stories = array();

			foreach ($results as $result) {
				$story = new self();
				foreach ($result as $key => $value) {
					if ($key !== 'author_name') {
						$story->$key = $value;
					}
				}
				$story->author_name = $result['author_name'];
				$stories[] = $story;
			}

			return $stories;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Lấy truyện theo author
	 * 
	 * @param int $author_id ID của author
	 * @param int $limit Giới hạn số lượng
	 * @param int $offset Vị trí bắt đầu
	 * @return array
	 */
	public static function get_stories_by_author($author_id, $limit = null, $offset = 0)
	{
		try {
			$sql = "SELECT s.*, a.name as author_name FROM stories s 
					INNER JOIN authors a ON s.author_id = a.id 
					WHERE s.deleted_at IS NULL AND s.author_id = :author_id 
					ORDER BY s.created_at DESC";
			if ($limit) {
				$sql .= " LIMIT :limit OFFSET :offset";
			}
			
			$query = \DB::query($sql);
			$query->param('author_id', $author_id);
			if ($limit) {
				$query->param('limit', $limit)->param('offset', $offset);
			}
			
			$results = $query->execute();
			$stories = array();

			foreach ($results as $result) {
				$story = new self();
				foreach ($result as $key => $value) {
					if ($key !== 'author_name') {
						$story->$key = $value;
					}
				}
				$story->author_name = $result['author_name'];
				$stories[] = $story;
			}

			return $stories;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Đếm tổng số stories
	 * 
	 * @return int
	 */
    public static function count_all()
	{
		try {
            $query = \DB::query("SELECT COUNT(*) as total FROM stories WHERE deleted_at IS NULL");
            $result = $query->execute();
            $total = (int) $result->current()['total'];
            \Log::info('Model_Story::count_all total=' . $total);
            return $total;
		} catch (\Exception $e) {
            \Log::error('Model_Story::count_all failed: ' . $e->getMessage());
			return 0;
		}
	}

	/**
	 * Tạo story mới
	 * 
	 * @param array $data Dữ liệu story
	 * @return Model_Story|null
	 */
	public static function create_story(array $data)
	{
		try {
			// Kiểm tra dữ liệu đầu vào
			if (empty($data['title']) || empty($data['author_id'])) {
				return null;
			}

			// Tạo slug từ title nếu chưa có
			if (empty($data['slug'])) {
				$data['slug'] = self::create_slug($data['title']);
			}

			// Đặt giá trị mặc định
			$description = isset($data['description']) ? $data['description'] : '';
			$cover_image = isset($data['cover_image']) ? $data['cover_image'] : null;
			$status = isset($data['status']) ? $data['status'] : 'ongoing';
			$views = isset($data['views']) ? $data['views'] : 0;
			$is_featured = isset($data['is_featured']) ? $data['is_featured'] : 0;
			$is_hot = isset($data['is_hot']) ? $data['is_hot'] : 0;
			$created_at = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');

			// Thêm vào database với Raw SQL
			$query = \DB::query("INSERT INTO stories (title, slug, description, cover_image, author_id, status, views, is_featured, is_hot, created_at, updated_at) VALUES (:title, :slug, :description, :cover_image, :author_id, :status, :views, :is_featured, :is_hot, :created_at, :updated_at)");
			$result = $query->param('title', $data['title'])
							->param('slug', $data['slug'])
							->param('description', $description)
							->param('cover_image', $cover_image)
							->param('author_id', $data['author_id'])
							->param('status', $status)
							->param('views', $views)
							->param('is_featured', $is_featured)
							->param('is_hot', $is_hot)
							->param('created_at', $created_at)
							->param('updated_at', $updated_at)
							->execute();

			if ($result) {
				$story = new self();
				$story->id = $result[0];
				$story->title = $data['title'];
				$story->slug = $data['slug'];
				$story->description = $description;
				$story->cover_image = $cover_image;
				$story->author_id = $data['author_id'];
				$story->status = $status;
				$story->views = $views;
				$story->is_featured = $is_featured;
				$story->is_hot = $is_hot;
				$story->created_at = $created_at;
				$story->updated_at = $updated_at;
				return $story;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Cập nhật thông tin story
	 * 
	 * @param array $data Dữ liệu cần cập nhật
	 * @return bool
	 */
	public function update_story(array $data)
	{
		try {
			$updated_at = date('Y-m-d H:i:s');
			$set_parts = array();
			$params = array('id' => $this->id, 'updated_at' => $updated_at);

			// Xây dựng câu lệnh UPDATE động
			if (isset($data['title'])) {
				$set_parts[] = 'title = :title';
				$params['title'] = $data['title'];
			}
			if (isset($data['slug'])) {
				$set_parts[] = 'slug = :slug';
				$params['slug'] = $data['slug'];
			}
			if (isset($data['description'])) {
				$set_parts[] = 'description = :description';
				$params['description'] = $data['description'];
			}
			if (isset($data['cover_image'])) {
				$set_parts[] = 'cover_image = :cover_image';
				$params['cover_image'] = $data['cover_image'];
			}
			if (isset($data['author_id'])) {
				$set_parts[] = 'author_id = :author_id';
				$params['author_id'] = $data['author_id'];
			}
			if (isset($data['status'])) {
				$set_parts[] = 'status = :status';
				$params['status'] = $data['status'];
			}
			if (isset($data['is_featured'])) {
				$set_parts[] = 'is_featured = :is_featured';
				$params['is_featured'] = $data['is_featured'];
			}
			if (isset($data['is_hot'])) {
				$set_parts[] = 'is_hot = :is_hot';
				$params['is_hot'] = $data['is_hot'];
			}

			if (empty($set_parts)) {
				return false;
			}

			$sql = "UPDATE stories SET " . implode(', ', $set_parts) . ", updated_at = :updated_at WHERE id = :id";
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
	 * Xóa story (soft delete)
	 * 
	 * @return bool
	 */
	public function soft_delete()
	{
		try {
			$updated_at = date('Y-m-d H:i:s');
			$deleted_at = date('Y-m-d H:i:s');
			
			$query = \DB::query("UPDATE stories SET deleted_at = :deleted_at, updated_at = :updated_at WHERE id = :id");
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
	 * Tăng view count
	 * 
	 * @return bool
	 */
	public function increment_view_count()
	{
		try {
			$updated_at = date('Y-m-d H:i:s');
			
			$query = \DB::query("UPDATE stories SET views = views + 1, updated_at = :updated_at WHERE id = :id");
			$result = $query->param('updated_at', $updated_at)
							->param('id', $this->id)
							->execute();
				
			if ($result) {
				$this->views++;
				$this->updated_at = $updated_at;
				return true;
			}
			return false;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Tạo slug từ title
	 * 
	 * @param string $title Title của story
	 * @return string
	 */
	public static function create_slug($title)
	{
		// Chuyển về chữ thường
		$slug = strtolower($title);
		
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
			$query = \DB::query("SELECT COUNT(*) as total FROM stories WHERE slug = :slug");
			$result = $query->param('slug', $slug)->execute();
			return (int) $result->current()['total'] > 0;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Lấy author của story
	 * 
	 * @return Model_Author|null
	 */
	public function get_author()
	{
		return Model_Author::find($this->author_id);
	}

	/**
	 * Lấy danh sách categories của story
	 * 
	 * @return array
	 */
	public function get_categories()
	{
		try {
			$query = \DB::query("SELECT c.* FROM categories c 
								INNER JOIN story_categories sc ON c.id = sc.category_id 
								WHERE sc.story_id = :story_id AND c.deleted_at IS NULL 
								ORDER BY c.name ASC");
			$results = $query->param('story_id', $this->id)->execute();
			$categories = array();

			foreach ($results as $result) {
				$category = new Model_Category();
				foreach ($result as $key => $value) {
					$category->$key = $value;
				}
				$categories[] = $category;
			}

			return $categories;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Lấy danh sách chapters của story
	 * 
	 * @param int $limit Giới hạn số lượng
	 * @param int $offset Vị trí bắt đầu
	 * @return array
	 */
	public function get_chapters($limit = null, $offset = 0)
	{
		try {
			$sql = "SELECT * FROM chapters WHERE story_id = :story_id AND deleted_at IS NULL ORDER BY chapter_number ASC";
			if ($limit) {
				$sql .= " LIMIT :limit OFFSET :offset";
			}
			
			$query = \DB::query($sql);
			$query->param('story_id', $this->id);
			if ($limit) {
				$query->param('limit', $limit)->param('offset', $offset);
			}
			
			$results = $query->execute();
			$chapters = array();

			foreach ($results as $result) {
				$chapter = new Model_Chapter();
				foreach ($result as $key => $value) {
					$chapter->$key = $value;
				}
				$chapters[] = $chapter;
			}

			return $chapters;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Lấy số lượng chapters của story
	 * 
	 * @return int
	 */
	public function get_chapter_count()
	{
		try {
			$query = \DB::query("SELECT COUNT(*) as total FROM chapters WHERE story_id = :story_id AND deleted_at IS NULL");
			$result = $query->param('story_id', $this->id)->execute();
			return (int) $result->current()['total'];
		} catch (\Exception $e) {
			return 0;
		}
	}

	/**
	 * Lấy chapter cuối cùng của story
	 * 
	 * @return Model_Chapter|null
	 */
	public function get_latest_chapter()
	{
		try {
			$query = \DB::query("SELECT * FROM chapters WHERE story_id = :story_id AND deleted_at IS NULL ORDER BY chapter_number DESC LIMIT 1");
			$result = $query->param('story_id', $this->id)->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$chapter = new Model_Chapter();
				foreach ($data as $key => $value) {
					$chapter->$key = $value;
				}
				return $chapter;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Thêm category cho story
	 * 
	 * @param int $category_id ID của category
	 * @return bool
	 */
	public function add_category($category_id)
	{
		try {
			$created_at = date('Y-m-d H:i:s');
			
			$query = \DB::query("INSERT INTO story_categories (story_id, category_id, created_at) VALUES (:story_id, :category_id, :created_at)");
			$result = $query->param('story_id', $this->id)
							->param('category_id', $category_id)
							->param('created_at', $created_at)
							->execute();
			
			return (bool) $result;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Xóa tất cả categories của story
	 * 
	 * @return bool
	 */
	public function remove_all_categories()
	{
		try {
			$query = \DB::query("DELETE FROM story_categories WHERE story_id = :story_id");
			$result = $query->param('story_id', $this->id)->execute();
			return (bool) $result;
		} catch (\Exception $e) {
			return false;
		}
	}
}
