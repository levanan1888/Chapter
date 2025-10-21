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
		
		// Phân trang
		$page = Input::get('page', 1);
		$limit = 10;
		$offset = ($page - 1) * $limit;

		// Dữ liệu filter (hiển thị trong form)
		$data['categories'] = Model_Category::get_all_categories();
		$data['selected_category'] = Input::get('category');
		$data['selected_status'] = Input::get('status');
		$data['search'] = Input::get('search');

		// Lấy danh sách truyện (hiện tại: tất cả, có thể mở rộng filter sau)
		$data['stories'] = Model_Story::get_all_stories($limit, $offset);
		$data['total_stories'] = Model_Story::count_all();
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
			$title = Input::post('title', '');
			$description = Input::post('description', '');
			$author_id = Input::post('author_id', '');
			$status = Input::post('status', 'ongoing');
			$category_ids = Input::post('category_ids', array());
			$is_featured = Input::post('is_featured', 0);
			$is_hot = Input::post('is_hot', 0);

			// Kiểm tra dữ liệu đầu vào
			if (empty($title) || empty($author_id)) {
				$data['error_message'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
			} else {
				// Tạo truyện mới
				$story_data = array(
					'title' => $title,
					'description' => $description,
					'author_id' => $author_id,
					'status' => $status,
					'is_featured' => $is_featured,
					'is_hot' => $is_hot,
				);

				$new_story = Model_Story::create_story($story_data);
				if ($new_story) {
					// Thêm categories
					if (!empty($category_ids) && is_array($category_ids)) {
						foreach ($category_ids as $category_id) {
							$new_story->add_category($category_id);
						}
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
					if (isset($_FILES['chapter_images']) && !empty($_FILES['chapter_images']['name'][0])) {
						$chapter_images = $this->process_chapter_images($_FILES['chapter_images'], $new_story->id);
						if (!empty($chapter_images)) {
							// Tạo chương đầu tiên với ảnh đã upload
							$chapter_data = array(
								'story_id' => $new_story->id,
								'title' => 'Chapter 1',
								'chapter_number' => 1,
								'images' => json_encode($chapter_images),
								'views' => 0,
							);
							Model_Chapter::create_chapter($chapter_data);
						}
					}

					$data['success_message'] = 'Thêm truyện thành công!';
					// Reset form
					$data['form_data'] = array();
				} else {
					$data['error_message'] = 'Có lỗi xảy ra khi thêm truyện.';
				}
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
			$title = Input::post('title', '');
			$description = Input::post('description', '');
			$author_id = Input::post('author_id', '');
			$status = Input::post('status', 'ongoing');
			$category_ids = Input::post('category_ids', array());
			$is_featured = Input::post('is_featured', 0);
			$is_hot = Input::post('is_hot', 0);

			// Kiểm tra dữ liệu đầu vào
			if (empty($title) || empty($author_id)) {
				$data['error_message'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
			} else {
				// Cập nhật truyện
				$update_data = array(
					'title' => $title,
					'description' => $description,
					'author_id' => $author_id,
					'status' => $status,
					'is_featured' => $is_featured,
					'is_hot' => $is_hot,
				);

				if ($data['story']->update_story($update_data)) {
					// Cập nhật categories
					$data['story']->remove_all_categories();
					if (!empty($category_ids) && is_array($category_ids)) {
						foreach ($category_ids as $category_id) {
							$data['story']->add_category($category_id);
						}
					}

					$data['success_message'] = 'Cập nhật truyện thành công!';
					// Reload data
					$data['story'] = Model_Story::find($id);
					$current_categories = $data['story']->get_categories();
					$data['current_category_ids'] = array();
					foreach ($current_categories as $category) {
						$data['current_category_ids'][] = $category->id;
					}
				} else {
					$data['error_message'] = 'Có lỗi xảy ra khi cập nhật truyện.';
				}
			}
		}

		$data['title'] = 'Sửa Truyện';
		$data['content'] = View::forge('admin/content/story_edit', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Xóa truyện (AJAX)
	 * 
	 * @param int $id ID của truyện
	 * @return Response
	 */
	public function action_delete($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/stories');
		}

		if (empty($id)) {
			return $this->error_response('ID không hợp lệ.');
		}

		$story = Model_Story::find($id);
		
		if (!$story) {
			return $this->error_response('Không tìm thấy truyện.');
		}

		if ($story->soft_delete()) {
			return $this->success_response('Xóa truyện thành công!');
		} else {
			return $this->error_response('Có lỗi xảy ra khi xóa truyện.');
		}
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
	 * @return array Mảng đường dẫn ảnh
	 */
	private function process_chapter_images($files, $story_id)
	{
		$uploaded_images = array();

		try {
			// Tạo thư mục upload
			$upload_dir = DOCROOT . 'uploads/chapters/story_' . $story_id . '/';
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true);
			}

			$allowed_types = array('image/jpeg', 'image/png', 'image/gif');

			// Xử lý multiple files
			$file_count = count($files['name']);
			
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
						$uploaded_images[] = 'uploads/chapters/story_' . $story_id . '/' . $filename;
					}
				}
			}

			return $uploaded_images;
		} catch (\Exception $e) {
			return array();
		}
	}
}
