<?php

/**
 * Model Chapter
 * 
 * Quản lý dữ liệu chương truyện
 * 
 * @package    App
 * @subpackage Model
 */
class Model_Chapter extends \Model
{
	/**
	 * Tên bảng trong database
	 * 
	 * @var string
	 */
	protected static $_table_name = 'chapters';

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
		'story_id',
		'title',
		'chapter_number',
		'images',
		'views',
		'created_at',
		'updated_at',
		'deleted_at',
	);

	/**
	 * Set property value safely
	 * 
	 * @param string $property
	 * @param mixed $value
	 * @return void
	 */
	protected function set_property($property, $value)
	{
		if (in_array($property, static::$_properties)) {
			$this->$property = $value;
		}
	}

	/**
	 * Các properties của model
	 */
	public $id;
	public $story_id;
	public $title;
	public $chapter_number;
	public $images;
	public $views;
	public $created_at;
	public $updated_at;
	public $deleted_at;

	/**
	 * Tìm chapter theo ID
	 * 
	 * @param int $id ID của chapter
	 * @return Model_Chapter|null
	 */
	public static function find($id)
	{
		try {
			$query = \DB::query("SELECT * FROM chapters WHERE id = :id AND is_active = :is_active");
			$result = $query->param('id', $id)->param('is_active', 1)->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$chapter = new self();
				foreach ($data as $key => $value) {
					if ($key === 'images') {
						$chapter->$key = json_decode($value, true);
					} else {
						$chapter->$key = $value;
					}
				}
				return $chapter;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Tìm chapter theo story_id và chapter_number
	 * 
	 * @param int $story_id ID của story
	 * @param int $chapter_number Số chương
	 * @return Model_Chapter|null
	 */
	public static function find_by_story_and_number($story_id, $chapter_number)
	{
		try {
			$query = \DB::query("SELECT * FROM chapters WHERE story_id = :story_id AND chapter_number = :chapter_number AND is_active = :is_active");
			$result = $query->param('story_id', $story_id)
							->param('chapter_number', $chapter_number)
							->param('is_active', 1)
							->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$chapter = new self();
				foreach ($data as $key => $value) {
					if ($key === 'images') {
						$chapter->$key = json_decode($value, true);
					} else {
						$chapter->$key = $value;
					}
				}
				return $chapter;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Tìm chapter theo slug
	 * 
	 * @param string $slug Slug của chapter
	 * @return Model_Chapter|null
	 */
	public static function find_by_slug($slug)
	{
		try {
			$query = \DB::query("SELECT * FROM chapters WHERE slug = :slug AND is_active = :is_active");
			$result = $query->param('slug', $slug)->param('is_active', 1)->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$chapter = new self();
				foreach ($data as $key => $value) {
					if ($key === 'images') {
						$chapter->$key = json_decode($value, true);
					} else {
						$chapter->$key = $value;
					}
				}
				return $chapter;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Lấy danh sách chapters của story
	 * 
	 * @param int $story_id ID của story
	 * @param int $limit Giới hạn số lượng
	 * @param int $offset Vị trí bắt đầu
	 * @return array
	 */
	public static function get_chapters_by_story($story_id, $limit = null, $offset = 0)
	{
		try {
			$sql = "SELECT * FROM chapters WHERE story_id = :story_id AND is_active = :is_active ORDER BY chapter_number ASC";
			if ($limit) {
				$sql .= " LIMIT :limit OFFSET :offset";
			}
			
			$query = \DB::query($sql);
			$query->param('story_id', $story_id)->param('is_active', 1);
			if ($limit) {
				$query->param('limit', $limit)->param('offset', $offset);
			}
			
			$results = $query->execute();
			$chapters = array();

			foreach ($results as $result) {
				$chapter = new self();
				foreach ($result as $key => $value) {
					if ($key === 'images') {
						$chapter->$key = json_decode($value, true);
					} else {
						$chapter->$key = $value;
					}
				}
				$chapters[] = $chapter;
			}

			return $chapters;
		} catch (\Exception $e) {
			return array();
		}
	}

	/**
	 * Lấy chapter trước đó
	 * 
	 * @return Model_Chapter|null
	 */
	public function get_previous_chapter()
	{
		try {
			$query = \DB::query("SELECT * FROM chapters WHERE story_id = :story_id AND chapter_number < :chapter_number AND is_active = :is_active ORDER BY chapter_number DESC LIMIT 1");
			$result = $query->param('story_id', $this->story_id)
							->param('chapter_number', $this->chapter_number)
							->param('is_active', 1)
							->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$chapter = new self();
				foreach ($data as $key => $value) {
					if ($key === 'images') {
						$chapter->$key = json_decode($value, true);
					} else {
						$chapter->$key = $value;
					}
				}
				return $chapter;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Lấy chapter tiếp theo
	 * 
	 * @return Model_Chapter|null
	 */
	public function get_next_chapter()
	{
		try {
			$query = \DB::query("SELECT * FROM chapters WHERE story_id = :story_id AND chapter_number > :chapter_number AND is_active = :is_active ORDER BY chapter_number ASC LIMIT 1");
			$result = $query->param('story_id', $this->story_id)
							->param('chapter_number', $this->chapter_number)
							->param('is_active', 1)
							->execute();

			if ($result->count() > 0) {
				$data = $result->current();
				$chapter = new self();
				foreach ($data as $key => $value) {
					if ($key === 'images') {
						$chapter->$key = json_decode($value, true);
					} else {
						$chapter->$key = $value;
					}
				}
				return $chapter;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Đếm tổng số chapters của story
	 * 
	 * @param int $story_id ID của story
	 * @return int
	 */
	public static function count_by_story($story_id)
	{
		try {
			$query = \DB::query("SELECT COUNT(*) as total FROM chapters WHERE story_id = :story_id AND is_active = :is_active");
			$result = $query->param('story_id', $story_id)->param('is_active', 1)->execute();
			return (int) $result->current()['total'];
		} catch (\Exception $e) {
			return 0;
		}
	}

	/**
	 * Đếm tổng số chapters
	 * 
	 * @return int
	 */
	public static function count_all()
	{
		try {
			$query = \DB::query("SELECT COUNT(*) as total FROM chapters WHERE is_active = :is_active");
			$result = $query->param('is_active', 1)->execute();
			return (int) $result->current()['total'];
		} catch (\Exception $e) {
			return 0;
		}
	}

	/**
	 * Tạo chapter mới
	 * 
	 * @param array $data Dữ liệu chapter
	 * @return Model_Chapter|null
	 */
	public static function create_chapter(array $data)
	{
		try {
			// Kiểm tra dữ liệu đầu vào
			if (empty($data['story_id']) || empty($data['title']) || empty($data['chapter_number'])) {
				return null;
			}

			// Tạo slug từ title nếu chưa có
			if (empty($data['slug'])) {
				$data['slug'] = self::create_slug($data['title']);
			}

			// Đặt giá trị mặc định
			$images = isset($data['images']) ? json_encode($data['images']) : json_encode(array());
			$view_count = isset($data['view_count']) ? $data['view_count'] : 0;
			$is_active = isset($data['is_active']) ? $data['is_active'] : 1;
			$created_at = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');

			// Thêm vào database với Raw SQL
			$query = \DB::query("INSERT INTO chapters (story_id, title, slug, chapter_number, images, view_count, is_active, created_at, updated_at) VALUES (:story_id, :title, :slug, :chapter_number, :images, :view_count, :is_active, :created_at, :updated_at)");
			$result = $query->param('story_id', $data['story_id'])
							->param('title', $data['title'])
							->param('slug', $data['slug'])
							->param('chapter_number', $data['chapter_number'])
							->param('images', $images)
							->param('view_count', $view_count)
							->param('is_active', $is_active)
							->param('created_at', $created_at)
							->param('updated_at', $updated_at)
							->execute();

			if ($result) {
				$chapter = new self();
				$chapter->id = $result[0];
				$chapter->story_id = $data['story_id'];
				$chapter->title = $data['title'];
				$chapter->slug = $data['slug'];
				$chapter->chapter_number = $data['chapter_number'];
				$chapter->images = isset($data['images']) ? $data['images'] : array();
				$chapter->view_count = $view_count;
				$chapter->is_active = $is_active;
				$chapter->created_at = $created_at;
				$chapter->updated_at = $updated_at;
				return $chapter;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Cập nhật thông tin chapter
	 * 
	 * @param array $data Dữ liệu cần cập nhật
	 * @return bool
	 */
	public function update_chapter(array $data)
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
			if (isset($data['chapter_number'])) {
				$set_parts[] = 'chapter_number = :chapter_number';
				$params['chapter_number'] = $data['chapter_number'];
			}
			if (isset($data['images'])) {
				$set_parts[] = 'images = :images';
				$params['images'] = json_encode($data['images']);
			}
			if (isset($data['is_active'])) {
				$set_parts[] = 'is_active = :is_active';
				$params['is_active'] = $data['is_active'];
			}

			if (empty($set_parts)) {
				return false;
			}

			$sql = "UPDATE chapters SET " . implode(', ', $set_parts) . ", updated_at = :updated_at WHERE id = :id";
			$query = \DB::query($sql);
			
			foreach ($params as $key => $value) {
				$query->param($key, $value);
			}
			
			$result = $query->execute();
			
			if ($result) {
				// Cập nhật properties của object
				foreach ($data as $key => $value) {
					if ($key === 'images') {
						$this->$key = $value;
					} else {
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
	 * Xóa chapter (soft delete)
	 * 
	 * @return bool
	 */
	public function soft_delete()
	{
		try {
			$updated_at = date('Y-m-d H:i:s');
			
			$query = \DB::query("UPDATE chapters SET is_active = :is_active, updated_at = :updated_at WHERE id = :id");
			$result = $query->param('is_active', 0)
							->param('updated_at', $updated_at)
							->param('id', $this->id)
							->execute();
				
			if ($result) {
				$this->is_active = 0;
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
			
			$query = \DB::query("UPDATE chapters SET view_count = view_count + 1, updated_at = :updated_at WHERE id = :id");
			$result = $query->param('updated_at', $updated_at)
							->param('id', $this->id)
							->execute();
				
			if ($result) {
				$this->view_count++;
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
	 * @param string $title Title của chapter
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
			$query = \DB::query("SELECT COUNT(*) as total FROM chapters WHERE slug = :slug");
			$result = $query->param('slug', $slug)->execute();
			return (int) $result->current()['total'] > 0;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Lấy story của chapter
	 * 
	 * @return Model_Story|null
	 */
	public function get_story()
	{
		return Model_Story::find($this->story_id);
	}

	/**
	 * Lấy danh sách ảnh của chapter
	 * 
	 * @return array
	 */
	public function get_images()
	{
		if (is_string($this->images)) {
			return json_decode($this->images, true);
		}
		return $this->images ?: array();
	}

	/**
	 * Thêm ảnh vào chapter
	 * 
	 * @param string $image_path Đường dẫn ảnh
	 * @return bool
	 */
	public function add_image($image_path)
	{
		$images = $this->get_images();
		$images[] = $image_path;
		return $this->update_chapter(array('images' => $images));
	}

	/**
	 * Xóa ảnh khỏi chapter
	 * 
	 * @param string $image_path Đường dẫn ảnh
	 * @return bool
	 */
	public function remove_image($image_path)
	{
		$images = $this->get_images();
		$key = array_search($image_path, $images);
		if ($key !== false) {
			unset($images[$key]);
			$images = array_values($images); // Re-index array
			return $this->update_chapter(array('images' => $images));
		}
		return false;
	}

	/**
	 * Cập nhật danh sách ảnh
	 * 
	 * @param array $images Danh sách ảnh mới
	 * @return bool
	 */
	public function update_images(array $images)
	{
		return $this->update_chapter(array('images' => $images));
	}
}
