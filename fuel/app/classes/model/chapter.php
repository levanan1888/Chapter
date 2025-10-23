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
        'background_image',
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
    public $background_image;
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
            // Legacy find that filtered by is_active (may not exist). Keep for BC but prefer find_admin.
            $query = \DB::query("SELECT * FROM chapters WHERE id = :id");
            $result = $query->param('id', $id)->execute();

            if ($result->count() > 0) {
                $data = $result->current();
                $chapter = new self();
                foreach ($data as $key => $value) {
                    // Store raw DB value; consumers should call get_images() to decode
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
     * Admin: Find chapter by ID without status filtering
     *
     * @param int $id
     * @return Model_Chapter|null
     */
    public static function find_admin($id)
    {
        try {
            $query = \DB::query("SELECT * FROM chapters WHERE id = :id");
            $result = $query->param('id', $id)->execute();

            if ($result->count() > 0) {
                $data = $result->current();
                $chapter = new self();
                foreach ($data as $key => $value) {
                    $chapter->$key = $value;
                }
                return $chapter;
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Model_Chapter::find_admin error: ' . $e->getMessage());
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
			$query = \DB::query("SELECT * FROM chapters WHERE story_id = :story_id AND chapter_number = :chapter_number AND deleted_at IS NULL");
			$result = $query->param('story_id', $story_id)
							->param('chapter_number', $chapter_number)
							->execute();

            if ($result->count() > 0) {
                $data = $result->current();
                $chapter = new self();
                foreach ($data as $key => $value) {
                    $chapter->$key = $value;
                }
                return $chapter;
            }

			return null;
		} catch (\Exception $e) {
			\Log::error('Model_Chapter::find_by_story_and_number error: ' . $e->getMessage());
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
			// Original method filtered by is_active, which may not exist depending on schema.
			// Keep existing behavior but add guard logs to aid debugging.
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
			\Log::info('Model_Chapter::get_chapters_by_story executed', array(
				'sql' => $sql,
				'story_id' => $story_id,
				'limit' => $limit,
				'offset' => $offset,
				'row_count' => $results->count()
			));
			$chapters = array();

            foreach ($results as $result) {
                $chapter = new self();
                foreach ($result as $key => $value) {
                    $chapter->$key = $value;
                }
                $chapters[] = $chapter;
            }

			return $chapters;
		} catch (\Exception $e) {
			\Log::error('Model_Chapter::get_chapters_by_story error: ' . $e->getMessage());
			return array();
		}
	}

	/**
	 * Admin: Lấy danh sách chapters của story (bỏ qua trạng thái/is_active)
	 * 
	 * @param int $story_id
	 * @param int|null $limit
	 * @param int $offset
	 * @return array
	 */
	public static function get_chapters_by_story_admin($story_id, $limit = null, $offset = 0)
	{
		try {
			$sql = "SELECT * FROM chapters WHERE story_id = :story_id ORDER BY chapter_number ASC";
			if ($limit) {
				$sql .= " LIMIT :limit OFFSET :offset";
			}

			$query = \DB::query($sql);
			$query->param('story_id', $story_id);
			if ($limit) {
				$query->param('limit', $limit)->param('offset', $offset);
			}

            $results = $query->execute();
			\Log::info('Model_Chapter::get_chapters_by_story_admin executed', array(
				'sql' => $sql,
				'story_id' => $story_id,
				'limit' => $limit,
				'offset' => $offset,
				'row_count' => $results->count()
			));

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
			\Log::error('Model_Chapter::get_chapters_by_story_admin error: ' . $e->getMessage());
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
			$query = \DB::query("SELECT * FROM chapters WHERE story_id = :story_id AND chapter_number < :chapter_number AND deleted_at IS NULL ORDER BY chapter_number DESC LIMIT 1");
			$result = $query->param('story_id', $this->story_id)
							->param('chapter_number', $this->chapter_number)
							->execute();

            if ($result->count() > 0) {
                $data = $result->current();
                $chapter = new self();
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
	 * Lấy chapter tiếp theo
	 * 
	 * @return Model_Chapter|null
	 */
	public function get_next_chapter()
	{
		try {
			$query = \DB::query("SELECT * FROM chapters WHERE story_id = :story_id AND chapter_number > :chapter_number AND deleted_at IS NULL ORDER BY chapter_number ASC LIMIT 1");
			$result = $query->param('story_id', $this->story_id)
							->param('chapter_number', $this->chapter_number)
							->execute();

            if ($result->count() > 0) {
                $data = $result->current();
                $chapter = new self();
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
			$val = (int) $result->current()['total'];
			\Log::info('Model_Chapter::count_by_story', array('story_id' => $story_id, 'count' => $val));
			return $val;
		} catch (\Exception $e) {
			\Log::error('Model_Chapter::count_by_story error: ' . $e->getMessage());
			return 0;
		}
	}

	/**
	 * Admin: Đếm chapters của story (bỏ qua trạng thái)
	 */
	public static function count_by_story_admin($story_id)
	{
		try {
			$query = \DB::query("SELECT COUNT(*) as total FROM chapters WHERE story_id = :story_id");
			$result = $query->param('story_id', $story_id)->execute();
			$val = (int) $result->current()['total'];
			\Log::info('Model_Chapter::count_by_story_admin', array('story_id' => $story_id, 'count' => $val));
			return $val;
		} catch (\Exception $e) {
			\Log::error('Model_Chapter::count_by_story_admin error: ' . $e->getMessage());
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

			// Đặt giá trị mặc định
			$images = isset($data['images']) ? json_encode($data['images']) : json_encode(array());
            $background_image = isset($data['background_image']) ? $data['background_image'] : null;
            $views = isset($data['views']) ? (int) $data['views'] : 0;
			$created_at = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');

			// Thêm vào database với Raw SQL
            $query = \DB::query("INSERT INTO chapters (story_id, title, chapter_number, images, background_image, views, created_at, updated_at) VALUES (:story_id, :title, :chapter_number, :images, :background_image, :views, :created_at, :updated_at)");
			$result = $query->param('story_id', $data['story_id'])
							->param('title', $data['title'])
							->param('chapter_number', $data['chapter_number'])
							->param('images', $images)
                            ->param('background_image', $background_image)
                            ->param('views', $views)
							->param('created_at', $created_at)
							->param('updated_at', $updated_at)
							->execute();

			if ($result) {
				$chapter = new self();
				$chapter->id = $result[0];
				$chapter->story_id = $data['story_id'];
				$chapter->title = $data['title'];
				$chapter->chapter_number = $data['chapter_number'];
				$chapter->images = isset($data['images']) ? $data['images'] : array();
                $chapter->background_image = $background_image;
                $chapter->views = $views;
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
			\Log::info('Model_Chapter::update_chapter called', array(
				'chapter_id' => $this->id,
				'data' => $data
			));
			
			$updated_at = date('Y-m-d H:i:s');
			$set_parts = array();
			$params = array('id' => $this->id, 'updated_at' => $updated_at);

			// Xây dựng câu lệnh UPDATE động
			if (isset($data['title'])) {
				$set_parts[] = 'title = :title';
				$params['title'] = $data['title'];
			}
			if (isset($data['chapter_number'])) {
				$set_parts[] = 'chapter_number = :chapter_number';
				$params['chapter_number'] = $data['chapter_number'];
			}
			if (isset($data['images'])) {
				$set_parts[] = 'images = :images';
				$params['images'] = json_encode($data['images']);
			}
            if (array_key_exists('background_image', $data)) {
                $set_parts[] = 'background_image = :background_image';
                $params['background_image'] = $data['background_image'];
            }
            if (isset($data['views'])) {
                $set_parts[] = 'views = :views';
                $params['views'] = (int) $data['views'];
            }

			if (empty($set_parts)) {
				return false;
			}

			$sql = "UPDATE chapters SET " . implode(', ', $set_parts) . ", updated_at = :updated_at WHERE id = :id";
			\Log::info('Model_Chapter::update_chapter SQL', array(
				'sql' => $sql,
				'params' => $params
			));
			
			$query = \DB::query($sql);
			
			foreach ($params as $key => $value) {
				$query->param($key, $value);
			}
			
			$result = $query->execute();
			\Log::info('Model_Chapter::update_chapter result', array(
				'result' => $result,
				'affected_rows' => $result
			));
			
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
			\Log::error('Model_Chapter::update_chapter exception', array(
				'chapter_id' => $this->id,
				'error' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			));
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
            $deleted_at = $updated_at;

            $query = \DB::query("UPDATE chapters SET deleted_at = :deleted_at, updated_at = :updated_at WHERE id = :id");
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
     * Restore a soft-deleted chapter
     */
    public function restore()
    {
        try {
            $updated_at = date('Y-m-d H:i:s');
            $query = \DB::query("UPDATE chapters SET deleted_at = NULL, updated_at = :updated_at WHERE id = :id");
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
     * Permanently delete chapter
     */
    public function force_delete()
    {
        try {
            $query = \DB::query("DELETE FROM chapters WHERE id = :id");
            $result = $query->param('id', $this->id)->execute();
            return (bool) $result;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Admin: list chapters with filters
     */
    public static function get_chapters_by_story_admin_with_filter($story_id, $limit, $offset, $search = '', $status = 'all', $sort = 'created_at_desc')
    {
        try {
            $where = array('story_id = :story_id');
            $params = array('story_id' => $story_id);

            if (!empty($search)) {
                $where[] = 'title LIKE :search';
                $params['search'] = '%' . $search . '%';
            }

            if ($status === 'active') {
                $where[] = 'deleted_at IS NULL';
            } elseif ($status === 'deleted') {
                $where[] = 'deleted_at IS NOT NULL';
            }

            $order = 'created_at DESC';
            switch ($sort) {
                case 'created_at_asc':
                    $order = 'created_at ASC';
                    break;
                case 'chapter_number_asc':
                    $order = 'chapter_number ASC';
                    break;
                case 'chapter_number_desc':
                    $order = 'chapter_number DESC';
                    break;
                case 'updated_at_desc':
                    $order = 'updated_at DESC';
                    break;
            }

            $limitInt = (int) $limit;
            $offsetInt = (int) $offset;
            $sql = 'SELECT * FROM chapters WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $order . ' LIMIT ' . $limitInt . ' OFFSET ' . $offsetInt;

            $query = \DB::query($sql);
            foreach ($params as $k => $v) {
                $query->param($k, $v);
            }
            $results = $query->execute();
            \Log::info('Model_Chapter::get_chapters_by_story_admin_with_filter', array(
                'sql' => $sql,
                'params' => $params,
                'row_count' => $results->count()
            ));
            $chapters = array();
            foreach ($results as $row) {
                $chapter = new self();
                foreach ($row as $k => $v) {
                    $chapter->$k = $v;
                }
                $chapters[] = $chapter;
            }
            return $chapters;
        } catch (\Exception $e) {
            \Log::error('Model_Chapter::get_chapters_by_story_admin_with_filter error: ' . $e->getMessage());
            return array();
        }
    }

    public static function count_by_story_admin_with_filter($story_id, $search = '', $status = 'all')
    {
        try {
            $where = array('story_id = :story_id');
            $params = array('story_id' => $story_id);

            if (!empty($search)) {
                $where[] = 'title LIKE :search';
                $params['search'] = '%' . $search . '%';
            }
            if ($status === 'active') {
                $where[] = 'deleted_at IS NULL';
            } elseif ($status === 'deleted') {
                $where[] = 'deleted_at IS NOT NULL';
            }

            $sql = 'SELECT COUNT(*) as total FROM chapters WHERE ' . implode(' AND ', $where);
            $query = \DB::query($sql);
            foreach ($params as $k => $v) {
                $query->param($k, $v);
            }
            $result = $query->execute();
            \Log::info('Model_Chapter::count_by_story_admin_with_filter', array('sql' => $sql, 'params' => $params, 'total' => $result->current()['total']));
            return (int) $result->current()['total'];
        } catch (\Exception $e) {
            \Log::error('Model_Chapter::count_by_story_admin_with_filter error: ' . $e->getMessage());
            return 0;
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
			
            $query = \DB::query("UPDATE chapters SET views = views + 1, updated_at = :updated_at WHERE id = :id");
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
        // images can be stored as JSON string (DB) or already-decoded array (runtime)
        if (is_array($this->images)) {
            return $this->images;
        }

        if (is_string($this->images) && $this->images !== '') {
            $decoded = json_decode($this->images, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return array();
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
