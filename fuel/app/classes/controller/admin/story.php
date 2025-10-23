<?php

/**
 * Admin Story Controller
 * 
 * Xử lý quản lý truyện (CRUD)
 * 
 * @package    App
 * @subpackage Controller\Admin
 */
class Controller_Admin_Story extends Controller_Admin_Base
{
	/**
	 * Quản lý truyện
	 * 
	 * @return void
	 */
	public function action_index()
	{
		$this->require_login();

		$data = array();
		
		// Lấy tham số filter
		$search = Input::get('search', '');
		$category_id = Input::get('category', '');
		$status = Input::get('status', '');
		$sort = Input::get('sort', 'created_at_desc');
		
		// Phân trang
		$page = Input::get('page', 1);
		$limit = 10;
		$offset = ($page - 1) * $limit;

		// Dữ liệu filter (hiển thị trong form)
		$data['categories'] = Model_Category::get_all_categories();
		$data['selected_category'] = $category_id;
		$data['selected_status'] = $status;
		$data['search'] = $search;
		$data['sort'] = $sort;

		// Lấy danh sách truyện với filter
		$data['stories'] = Model_Story::get_stories_with_filter($limit, $offset, $search, $category_id, $status, $sort);
		$data['total_stories'] = Model_Story::count_stories_with_filter($search, $category_id, $status);
		$data['current_page'] = $page;
		$data['total_pages'] = ceil($data['total_stories'] / $limit);

		$data['title'] = 'Quản lý Truyện';
		$data['content'] = View::forge('admin/content/stories', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Thêm truyện mới
	 * 
	 * @return void
	 */
	public function action_add()
	{
		$this->require_login();

		$data = array();
		$data['authors'] = Model_Author::get_all_authors();
		$data['categories'] = Model_Category::get_all_categories();

		// Xử lý form thêm truyện
		if (Input::method() === 'POST') {
			$title = trim(Input::post('title', ''));
			$description = trim(Input::post('description', ''));
			$author_id = Input::post('author_id', '');
			$status = Input::post('status', 'ongoing');
			$category_ids = Input::post('category_ids', array());
			$is_featured = Input::post('is_featured') ? 1 : 0;
			$is_hot = Input::post('is_hot') ? 1 : 0;
			$is_visible = Input::post('is_visible') ? 1 : 0;

			// Validation
			$errors = array();
			
			// Kiểm tra title
			if (empty($title)) {
				$errors[] = 'Tên truyện không được để trống.';
			} elseif (strlen($title) < 2) {
				$errors[] = 'Tên truyện phải có ít nhất 2 ký tự.';
			} elseif (strlen($title) > 255) {
				$errors[] = 'Tên truyện không được vượt quá 255 ký tự.';
			}
			
			// Kiểm tra author_id
			if (empty($author_id)) {
				$errors[] = 'Vui lòng chọn tác giả.';
			} else {
				$author = Model_Author::find($author_id);
				if (!$author) {
					$errors[] = 'Tác giả không tồn tại.';
				}
			}
			
			// Kiểm tra status
			$valid_statuses = array('ongoing', 'completed', 'paused');
			if (!in_array($status, $valid_statuses)) {
				$errors[] = 'Trạng thái không hợp lệ.';
			}
			
			// Kiểm tra categories
			if (!empty($category_ids) && is_array($category_ids)) {
				foreach ($category_ids as $category_id) {
					$category = Model_Category::find($category_id);
					if (!$category) {
						$errors[] = 'Danh mục không tồn tại.';
						break;
					}
				}
			}

			if (empty($errors)) {
				// Tạo truyện mới
				$story_data = array(
					'title' => $title,
					'description' => $description,
					'author_id' => $author_id,
					'status' => $status,
					'is_featured' => $is_featured,
					'is_hot' => $is_hot,
					'is_visible' => $is_visible,
				);

				\Log::info('Creating story with data: ' . json_encode($story_data));
				\Log::info('Category IDs: ' . json_encode($category_ids));

				$new_story = Model_Story::create_story($story_data);
				if ($new_story) {
					\Log::info('Story created successfully with ID: ' . $new_story->id);
					
					// Thêm categories
					if (!empty($category_ids) && is_array($category_ids)) {
						foreach ($category_ids as $category_id) {
							$result = $new_story->add_category($category_id);
							\Log::info("Adding category {$category_id}: " . ($result ? 'SUCCESS' : 'FAILED'));
						}
					} else {
						\Log::info('No categories to add');
					}

					// Upload ảnh bìa feature
					$cover_image = null;
					if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
						$cover_image = $this->upload_cover_image($_FILES['cover_image'], $new_story->id);
						if ($cover_image) {
							$new_story->cover_image = $cover_image;
							$new_story->save();
						}
					}

					// Upload ảnh chương đầu tiên
					$chapter_images = array();
					$image_order = Input::post('image_order', '');
					if (isset($_FILES['chapter_images']) && !empty($_FILES['chapter_images']['name'][0])) {
						$chapter_images = $this->process_chapter_images($_FILES['chapter_images'], $new_story->id, $image_order);
						if (!empty($chapter_images)) {
							// Tạo chương đầu tiên với ảnh đã upload
							$chapter_data = array(
								'story_id' => $new_story->id,
								'title' => 'Chapter 1',
								'chapter_number' => 1,
								'images' => $chapter_images,
								'views' => 0,
							);
							Model_Chapter::create_chapter($chapter_data);
						}
					}

					Session::set_flash('success', 'Thêm truyện thành công!');
					Response::redirect('admin/stories');
				} else {
					\Log::error('Failed to create story');
					$data['error_message'] = 'Có lỗi xảy ra khi thêm truyện.';
				}
			} else {
				// Có lỗi validation
				$data['error_message'] = implode('<br>', $errors);
			}

			// Giữ lại dữ liệu form nếu có lỗi
			if (!empty($data['error_message'])) {
				$data['form_data'] = array(
					'title' => $title,
					'description' => $description,
					'author_id' => $author_id,
					'status' => $status,
					'category_ids' => $category_ids,
					'is_featured' => $is_featured,
					'is_hot' => $is_hot,
					'is_visible' => $is_visible,
				);
			}
		}

		$data['title'] = 'Thêm Truyện';
		$data['content'] = View::forge('admin/content/story_add', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Sửa truyện
	 * 
	 * @param int $id ID của truyện
	 * @return void
	 */
	public function action_edit($id = null)
	{
		$this->require_login();

		if (empty($id)) {
			Response::redirect('admin/stories');
		}

		$data = array();
		$data['success_message'] = '';
		$data['error_message'] = '';
		$data['story'] = Model_Story::find($id);
		$data['authors'] = Model_Author::get_all_authors();
		$data['categories'] = Model_Category::get_all_categories();

		if (!$data['story']) {
			Session::set_flash('error', 'Không tìm thấy truyện.');
			Response::redirect('admin/stories');
		}

		// Lấy categories hiện tại của truyện
		$current_categories = $data['story']->get_categories();
		$data['current_category_ids'] = array();
		foreach ($current_categories as $category) {
			$data['current_category_ids'][] = $category->id;
		}

		// Xử lý form sửa truyện
		if (Input::method() === 'POST') {
			$title = trim(Input::post('title', ''));
			$description = trim(Input::post('description', ''));
			$author_id = Input::post('author_id', '');
			$status = Input::post('status', 'ongoing');
			$category_ids = Input::post('category_ids', array());
			$is_featured = Input::post('is_featured') ? 1 : 0;
			$is_hot = Input::post('is_hot') ? 1 : 0;
			$is_visible = Input::post('is_visible') ? 1 : 0;

			// Validation
			$errors = array();
			
			// Kiểm tra title
			if (empty($title)) {
				$errors[] = 'Tên truyện không được để trống.';
			} elseif (strlen($title) < 2) {
				$errors[] = 'Tên truyện phải có ít nhất 2 ký tự.';
			} elseif (strlen($title) > 255) {
				$errors[] = 'Tên truyện không được vượt quá 255 ký tự.';
			}
			
			// Kiểm tra author_id
			if (empty($author_id)) {
				$errors[] = 'Vui lòng chọn tác giả.';
			} else {
				$author = Model_Author::find($author_id);
				if (!$author) {
					$errors[] = 'Tác giả không tồn tại.';
				}
			}
			
			// Kiểm tra status
			$valid_statuses = array('ongoing', 'completed', 'paused');
			if (!in_array($status, $valid_statuses)) {
				$errors[] = 'Trạng thái không hợp lệ.';
			}
			
			// Kiểm tra categories
			if (!empty($category_ids) && is_array($category_ids)) {
				foreach ($category_ids as $category_id) {
					$category = Model_Category::find($category_id);
					if (!$category) {
						$errors[] = 'Danh mục không tồn tại.';
						break;
					}
				}
			}

			if (empty($errors)) {
				// Cập nhật truyện
				$update_data = array(
					'title' => $title,
					'description' => $description,
					'author_id' => $author_id,
					'status' => $status,
					'is_featured' => $is_featured,
					'is_hot' => $is_hot,
					'is_visible' => $is_visible,
				);

				if ($data['story']->update_story($update_data)) {
					// Cập nhật categories
					$data['story']->remove_all_categories();
					if (!empty($category_ids) && is_array($category_ids)) {
						foreach ($category_ids as $category_id) {
							$data['story']->add_category($category_id);
						}
					}

					Session::set_flash('success', 'Cập nhật truyện thành công!');
					Response::redirect('admin/stories');
				} else {
					$data['error_message'] = 'Có lỗi xảy ra khi cập nhật truyện.';
				}
			} else {
				// Có lỗi validation
				$data['error_message'] = implode('<br>', $errors);
			}
		}

		// Reload data sau khi có lỗi validation
		if (isset($data['error_message']) && !empty($data['error_message'])) {
					$data['story'] = Model_Story::find($id);
					$current_categories = $data['story']->get_categories();
					$data['current_category_ids'] = array();
					foreach ($current_categories as $category) {
						$data['current_category_ids'][] = $category->id;
			}
		}

		$data['title'] = 'Sửa Truyện';
		$data['content'] = View::forge('admin/content/story_edit', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Xem chi tiết truyện
	 * 
	 * @param int $id ID của truyện
	 * @return void
	 */
	public function action_view($id = null)
	{
		$this->require_login();

		if (empty($id)) {
			Session::set_flash('error', 'ID không hợp lệ.');
			Response::redirect('admin/stories');
		}

		$story = Model_Story::find($id);
		if (!$story) {
			Session::set_flash('error', 'Không tìm thấy truyện.');
			Response::redirect('admin/stories');
		}

		$data = array();
		$data['story'] = $story;
		
		// Lấy danh sách chương của truyện
		$data['chapters'] = $story->get_chapters();
		$data['chapter_count'] = count($data['chapters']);

		$data['title'] = 'Chi tiết Truyện - ' . $story->title;
		$data['content'] = View::forge('admin/content/story_view', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Xóa truyện (soft delete)
	 * 
	 * @param int $id ID của truyện
	 * @return void
	 */
	public function action_delete($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/stories');
		}

		if (empty($id)) {
			Session::set_flash('error', 'ID không hợp lệ.');
			Response::redirect('admin/stories');
		}

		$story = Model_Story::find($id);
		
		if (!$story) {
			Session::set_flash('error', 'Không tìm thấy truyện.');
			Response::redirect('admin/stories');
		}

		if ($story->soft_delete()) {
			Session::set_flash('success', 'Xóa truyện thành công!');
			Response::redirect('admin/stories');
		} else {
			Session::set_flash('error', 'Có lỗi xảy ra khi xóa truyện.');
			Response::redirect('admin/stories');
		}
	}

	/**
	 * Xóa hàng loạt truyện
	 * 
	 * @return void
	 */
	public function action_bulk_delete()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/stories');
		}

		$story_ids = Input::post('story_ids', array());
		
		if (empty($story_ids)) {
			Session::set_flash('error', 'Vui lòng chọn ít nhất một truyện để xóa.');
			Response::redirect('admin/stories');
		}

		$deleted_count = 0;
		$error_count = 0;

		foreach ($story_ids as $id) {
			$story = Model_Story::find($id);
			if ($story && $story->deleted_at === null) {
				if ($story->soft_delete()) {
					$deleted_count++;
				} else {
					$error_count++;
				}
			} else {
				$error_count++;
			}
		}

		if ($deleted_count > 0) {
			Session::set_flash('success', "Đã xóa thành công {$deleted_count} truyện.");
		}
		if ($error_count > 0) {
			Session::set_flash('error', "Có {$error_count} truyện không thể xóa.");
		}

		Response::redirect('admin/stories');
	}

	/**
	 * Sọt rác - Danh sách truyện đã xóa
	 * 
	 * @return void
	 */
	public function action_trash()
	{
		$this->require_login();

		$data = array();
		
		// Lấy tham số tìm kiếm và lọc
		$search = Input::get('search', '');
		$sort = Input::get('sort', 'deleted_at_desc');
		
		// Phân trang
		$page = Input::get('page', 1);
		$limit = 10;
		$offset = ($page - 1) * $limit;

		// Lấy danh sách truyện đã xóa
		$data['stories'] = Model_Story::get_deleted_stories($limit, $offset, $search, $sort);
		$data['total_stories'] = Model_Story::count_deleted($search);
		$data['current_page'] = $page;
		$data['total_pages'] = ceil($data['total_stories'] / $limit);
		
		// Truyền filter data để giữ lại trong form
		$data['search'] = $search;
		$data['sort'] = $sort;

		$data['title'] = 'Sọt rác - Truyện đã xóa';
		
		$data['content'] = View::forge('admin/content/stories_trash', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Khôi phục truyện từ sọt rác
	 * 
	 * @param int $id ID của truyện
	 * @return void
	 */
	public function action_restore($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/stories/trash');
		}

		if (empty($id)) {
			Session::set_flash('error', 'ID không hợp lệ.');
			Response::redirect('admin/stories/trash');
		}

		$story = Model_Story::find_deleted($id);
		
		if (!$story) {
			Session::set_flash('error', 'Không tìm thấy truyện.');
			Response::redirect('admin/stories/trash');
		}

		if ($story->restore()) {
			Session::set_flash('success', 'Khôi phục truyện thành công!');
			Response::redirect('admin/stories/trash');
		} else {
			Session::set_flash('error', 'Có lỗi xảy ra khi khôi phục truyện.');
			Response::redirect('admin/stories/trash');
		}
	}

	/**
	 * Xóa vĩnh viễn truyện
	 * 
	 * @param int $id ID của truyện
	 * @return void
	 */
	public function action_force_delete($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/stories/trash');
		}

		if (empty($id)) {
			Session::set_flash('error', 'ID không hợp lệ.');
			Response::redirect('admin/stories/trash');
		}

		$story = Model_Story::find_deleted($id);
		
		if (!$story) {
			Session::set_flash('error', 'Không tìm thấy truyện.');
			Response::redirect('admin/stories/trash');
		}

		if ($story->force_delete()) {
			Session::set_flash('success', 'Xóa vĩnh viễn truyện thành công!');
			Response::redirect('admin/stories/trash');
		} else {
			Session::set_flash('error', 'Có lỗi xảy ra khi xóa vĩnh viễn truyện.');
			Response::redirect('admin/stories/trash');
		}
	}

	/**
	 * Khôi phục hàng loạt truyện từ sọt rác
	 * 
	 * @return void
	 */
	public function action_bulk_restore()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/stories/trash');
		}

		$story_ids = Input::post('story_ids', array());
		
		if (empty($story_ids)) {
			Session::set_flash('error', 'Vui lòng chọn ít nhất một truyện để khôi phục.');
			Response::redirect('admin/stories/trash');
		}

		$restored_count = 0;
		$error_count = 0;

		foreach ($story_ids as $id) {
			$story = Model_Story::find_deleted($id);
			if ($story && $story->deleted_at) {
				if ($story->restore()) {
					$restored_count++;
				} else {
					$error_count++;
				}
			} else {
				$error_count++;
			}
		}

		if ($restored_count > 0) {
			Session::set_flash('success', "Đã khôi phục thành công {$restored_count} truyện.");
		}
		if ($error_count > 0) {
			Session::set_flash('error', "Có {$error_count} truyện không thể khôi phục.");
		}

		Response::redirect('admin/stories/trash');
	}

	/**
	 * Xóa vĩnh viễn hàng loạt truyện
	 * 
	 * @return void
	 */
	public function action_bulk_force_delete()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/stories/trash');
		}

		$story_ids = Input::post('story_ids', array());
		
		if (empty($story_ids)) {
			Session::set_flash('error', 'Vui lòng chọn ít nhất một truyện để xóa vĩnh viễn.');
			Response::redirect('admin/stories/trash');
		}

		$deleted_count = 0;
		$error_count = 0;

		foreach ($story_ids as $id) {
			$story = Model_Story::find_deleted($id);
			if ($story && $story->deleted_at !== null && $story->deleted_at !== '') {
				if ($story->force_delete()) {
					$deleted_count++;
				} else {
					$error_count++;
				}
			} else {
				$error_count++;
			}
		}

		if ($deleted_count > 0) {
			Session::set_flash('success', "Đã xóa vĩnh viễn thành công {$deleted_count} truyện.");
		}
		if ($error_count > 0) {
			Session::set_flash('error', "Có {$error_count} truyện không thể xóa vĩnh viễn.");
		}

		Response::redirect('admin/stories/trash');
	}

	/**
	 * API lấy danh sách truyện (AJAX)
	 * 
	 * @return Response
	 */
	public function action_api_list()
	{
		$this->require_login();

		$page = Input::get('page', 1);
		$limit = Input::get('limit', 10);
		$offset = ($page - 1) * $limit;

		$stories = Model_Story::get_all_stories($limit, $offset);
		$total = Model_Story::count_all();

		$data = array(
			'stories' => $stories,
			'pagination' => array(
				'current_page' => $page,
				'total_pages' => ceil($total / $limit),
				'total_items' => $total,
				'items_per_page' => $limit
			)
		);

		return $this->success_response('Danh sách truyện', $data);
	}

	/**
	 * API tìm kiếm truyện (AJAX)
	 * 
	 * @return Response
	 */
	public function action_api_search()
	{
		$this->require_login();

		$keyword = Input::get('q', '');
		$page = Input::get('page', 1);
		$limit = Input::get('limit', 10);
		$offset = ($page - 1) * $limit;

		if (empty($keyword)) {
			return $this->error_response('Vui lòng nhập từ khóa tìm kiếm.');
		}

		$stories = Model_Story::search_stories($keyword, $limit, $offset);
		$total = count(Model_Story::search_stories($keyword));

		$data = array(
			'stories' => $stories,
			'keyword' => $keyword,
			'pagination' => array(
				'current_page' => $page,
				'total_pages' => ceil($total / $limit),
				'total_items' => $total,
				'items_per_page' => $limit
			)
		);

		return $this->success_response('Kết quả tìm kiếm', $data);
	}

	/**
	 * Toggle trạng thái hiển thị của truyện (AJAX)
	 * 
	 * @return void
	 */
	public function action_toggle_visibility()
	{
		$this->require_login();

		// Chỉ cho phép POST request
		if (Input::method() !== 'POST') {
			return $this->error_response('Method not allowed', 405);
		}

		// Kiểm tra CSRF token
		if (!Security::check_token()) {
			return $this->error_response('Invalid CSRF token', 403);
		}

		$story_id = Input::post('story_id');
		
		// Validation
		if (empty($story_id) || !is_numeric($story_id)) {
			return $this->error_response('Invalid story ID', 400);
		}

		try {
			// Tìm story
			$story = Model_Story::find($story_id);
			if (!$story) {
				return $this->error_response('Story not found', 404);
			}

			// Toggle visibility
			$success = $story->toggle_visibility();
			
			if ($success) {
				// Tạo CSRF token mới sau khi xử lý thành công
				$new_csrf_token = Security::fetch_token();
				
				$data = array(
					'story_id' => $story->id,
					'is_visible' => $story->is_visible,
					'visibility_text' => $story->is_visible ? 'Hiển thị' : 'Ẩn',
					'visibility_class' => $story->is_visible ? 'success' : 'secondary',
					'csrf_token' => $new_csrf_token
				);
				
				return $this->success_response('Trạng thái hiển thị đã được cập nhật', $data);
			} else {
				return $this->error_response('Không thể cập nhật trạng thái hiển thị', 500);
			}
		} catch (\Exception $e) {
			\Log::error('Toggle story visibility failed: ' . $e->getMessage());
			return $this->error_response('Có lỗi xảy ra khi cập nhật trạng thái', 500);
		}
	}

	/**
	 * Upload ảnh bìa
	 * 
	 * @param array $file File upload
	 * @param int $story_id ID của truyện
	 * @return string|null Đường dẫn ảnh hoặc null nếu lỗi
	 */
	private function upload_cover_image($file, $story_id)
	{
		try {
			// Tạo thư mục upload
			$upload_dir = DOCROOT . 'uploads/story_covers/';
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true);
			}

			// Validate file
			$allowed_types = array('image/jpeg', 'image/png', 'image/gif');
			if (!in_array($file['type'], $allowed_types)) {
				return null;
			}

			// Validate file size (2MB)
			if ($file['size'] > 2 * 1024 * 1024) {
				return null;
			}

			// Generate filename
			$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
			$filename = 'story_' . $story_id . '_' . time() . '.' . $extension;
			$filepath = $upload_dir . $filename;

			// Move uploaded file
			if (move_uploaded_file($file['tmp_name'], $filepath)) {
				return 'uploads/story_covers/' . $filename;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Xử lý nhiều ảnh chương từ $_FILES
	 * 
	 * @param array $files $_FILES['chapter_images']
	 * @param int $story_id ID của truyện
	 * @param string $image_order JSON string của thứ tự ảnh (optional)
	 * @return array Mảng đường dẫn ảnh
	 */
	private function process_chapter_images($files, $story_id, $image_order = '')
	{
		$uploaded_images = array();

		try {
			// Tạo thư mục upload
			$upload_dir = DOCROOT . 'uploads/chapters/story_' . $story_id . '/';
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true);
			}

			$allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');

			// Xử lý multiple files
			$file_count = count($files['name']);
			$temp_images = array();
			
			for ($i = 0; $i < $file_count; $i++) {
				if ($files['error'][$i] == 0) {
					// Validate file type
					if (!in_array($files['type'][$i], $allowed_types)) {
						continue;
					}

					// Validate file size (2MB)
					if ($files['size'][$i] > 2 * 1024 * 1024) {
						continue;
					}

					// Generate filename
					$extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
					$filename = 'chapter_1_page_' . str_pad($i + 1, 3, '0', STR_PAD_LEFT) . '_' . time() . '.' . $extension;
					$filepath = $upload_dir . $filename;

					// Move uploaded file
					if (move_uploaded_file($files['tmp_name'][$i], $filepath)) {
						$temp_images[$i] = 'uploads/chapters/story_' . $story_id . '/' . $filename;
					}
				}
			}

			// Sắp xếp theo thứ tự nếu có
			if (!empty($image_order)) {
				$order_array = json_decode($image_order, true);
				if (is_array($order_array)) {
					foreach ($order_array as $index) {
						if (isset($temp_images[$index])) {
							$uploaded_images[] = $temp_images[$index];
						}
					}
				} else {
					// Nếu không có thứ tự, sử dụng thứ tự mặc định
					$uploaded_images = array_values($temp_images);
				}
			} else {
				// Nếu không có thứ tự, sử dụng thứ tự mặc định
				$uploaded_images = array_values($temp_images);
			}

			return $uploaded_images;
		} catch (\Exception $e) {
			return array();
		}
	}
}
