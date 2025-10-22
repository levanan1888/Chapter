<?php

/**
 * Admin Chapter Controller
 * 
 * Xử lý quản lý chương truyện (CRUD)
 * 
 * @package    App
 * @subpackage Controller\Admin
 */
class Controller_Admin_Chapter extends Controller_Admin_Base
{
	/**
	 * Quản lý chương của truyện
	 * 
	 * @param int $story_id ID của truyện
	 * @return void
	 */
	public function action_index($story_id = null)
	{
		$this->require_login();

		if (empty($story_id)) {
			Response::redirect('admin/stories');
		}

		// Use admin finder to allow managing chapters even if story is soft-deleted
		$story = Model_Story::find_admin($story_id);
		if (!$story) {
			Session::set_flash('error', 'Không tìm thấy truyện.');
			Response::redirect('admin/stories');
		}

		$data = array();
		$data['story'] = $story;
		
		// Phân trang
		$page = Input::get('page', 1);
		$limit = 10;
		$offset = ($page - 1) * $limit;

		// Lấy danh sách chương (admin xem tất cả)
		\Log::info('Controller_Admin_Chapter::action_index fetch chapters', array('story_id' => $story_id, 'page' => $page, 'limit' => $limit, 'offset' => $offset));
        // Filters
        $search = Input::get('search', '');
        $status = Input::get('status', 'all'); // all | active | deleted
        $sort = Input::get('sort', 'created_at_desc');

        $data['chapters'] = Model_Chapter::get_chapters_by_story_admin_with_filter($story_id, $limit, $offset, $search, $status, $sort);
        $data['total_chapters'] = Model_Chapter::count_by_story_admin_with_filter($story_id, $search, $status);
		$data['current_page'] = $page;
		$data['total_pages'] = ceil($data['total_chapters'] / $limit);
        $data['search'] = $search;
        $data['status'] = $status;
        $data['sort'] = $sort;

		$data['title'] = 'Quản lý Chương - ' . $story->title;
		$data['content'] = View::forge('admin/content/chapters', $data, false);

		return View::forge('layouts/admin', $data);
	}

	/**
	 * Thêm chương mới
	 * 
	 * @param int $story_id ID của truyện
	 * @return void
	 */
	public function action_add($story_id = null)
	{
		$this->require_login();

		if (empty($story_id)) {
			Response::redirect('admin/stories');
		}

		// Use admin finder to avoid false negatives if story is in trash
		$story = Model_Story::find_admin($story_id);
		if (!$story) {
			Session::set_flash('error', 'Không tìm thấy truyện.');
			Response::redirect('admin/stories');
		}

		$data = array();
		$data['success_message'] = '';
		$data['error_message'] = '';
		$data['story'] = $story;

		// Xử lý form thêm chương
		if (Input::method() === 'POST') {
			$title = Input::post('title', '');
			$chapter_number = Input::post('chapter_number', '');
			$image_order = Input::post('image_order', '');

			// Kiểm tra dữ liệu đầu vào
			if (empty($title) || empty($chapter_number)) {
				$data['error_message'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
			} else {
				// Xử lý upload ảnh
				$uploaded_images = array();
				if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
					$uploaded_images = $this->process_chapter_images($_FILES['images'], $story_id, $image_order);
				}

				// Tạo chương mới
				$chapter_data = array(
					'story_id' => $story_id,
					'title' => $title,
					'chapter_number' => $chapter_number,
					'images' => $uploaded_images,
				);

				$new_chapter = Model_Chapter::create_chapter($chapter_data);
				if ($new_chapter) {
					Session::set_flash('success', 'Thêm chương thành công!');
					Response::redirect('admin/chapters/' . $story_id);
				} else {
					$data['error_message'] = 'Có lỗi xảy ra khi thêm chương.';
				}
			}

			// Giữ lại dữ liệu form nếu có lỗi
			if (!empty($data['error_message'])) {
				$data['form_data'] = array(
					'title' => $title,
					'chapter_number' => $chapter_number,
				);
			}
		}

		$data['title'] = 'Thêm Chương - ' . $story->title;
		$data['content'] = View::forge('admin/content/chapter_add', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Sửa chương
	 * 
	 * @param int $id ID của chương
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
		$data['chapter'] = Model_Chapter::find($id);

		if (!$data['chapter']) {
			Session::set_flash('error', 'Không tìm thấy chương.');
			Response::redirect('admin/stories');
		}

		$data['story'] = $data['chapter']->get_story();

		// Xử lý form sửa chương
		if (Input::method() === 'POST') {
			\Log::info('Chapter edit POST received', array(
				'chapter_id' => $id,
				'post_data' => Input::post(),
				'files' => isset($_FILES['images']) ? array_keys($_FILES['images']) : 'no files',
				'csrf_token' => Input::post(\Config::get('security.csrf_token_key'))
			));

			$title = Input::post('title', '');
			$chapter_number = Input::post('chapter_number', '');
			$image_order = Input::post('image_order', '');

			// Kiểm tra dữ liệu đầu vào
			if (empty($title) || empty($chapter_number)) {
				$data['error_message'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
				\Log::warning('Chapter edit validation failed', array(
					'title' => $title,
					'chapter_number' => $chapter_number
				));
			} else {
				// Xử lý upload ảnh mới
				$new_images = array();
				if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
					// order sẽ được xử lý thủ công bên dưới để trộn cùng ảnh cũ
					$new_images = $this->process_chapter_images($_FILES['images'], $data['chapter']->story_id, '');
					\Log::info('New images processed', array(
						'count' => count($new_images),
						'images' => $new_images
					));
				}

				// Cập nhật chương
				$update_data = array(
					'title' => $title,
					'chapter_number' => $chapter_number,
				);

				// Kết hợp ảnh cũ và mới theo thứ tự gửi từ client
				$current_images = $data['chapter']->get_images();
				$final_images = array();
				if (!empty($image_order)) {
					$order_array = json_decode($image_order, true);
					\Log::info('Image order processing', array(
						'image_order' => $image_order,
						'decoded' => $order_array,
						'current_images' => $current_images
					));
					if (is_array($order_array)) {
						$new_ptr = 0;
						foreach ($order_array as $token) {
							if (is_string($token) && strpos($token, 'existing:') === 0) {
								$path = substr($token, 9);
								if (in_array($path, $current_images, true)) {
									$final_images[] = $path;
								}
							} elseif (is_string($token) && strpos($token, 'new:') === 0) {
								if (isset($new_images[$new_ptr])) {
									$final_images[] = $new_images[$new_ptr];
									$new_ptr++;
								}
							}
						}
						// Append any remaining new images just in case
						for ($i = $new_ptr; $i < count($new_images); $i++) {
							$final_images[] = $new_images[$i];
						}
					}
				}
				// Nếu không có order hợp lệ thì giữ nguyên ảnh cũ và thêm ảnh mới vào cuối
				if (empty($final_images)) {
					$final_images = array_merge($current_images, $new_images);
				}
				\Log::info('Final images array', array(
					'final_images' => $final_images,
					'count' => count($final_images)
				));
				$update_data['images'] = $final_images;

				if ($data['chapter']->update_chapter($update_data)) {
					Session::set_flash('success', 'Cập nhật chương thành công!');
					\Log::info('Chapter updated successfully', array(
						'chapter_id' => $id,
						'update_data' => $update_data
					));
					Response::redirect('admin/chapters/' . $data['chapter']->story_id);
				} else {
					$data['error_message'] = 'Có lỗi xảy ra khi cập nhật chương.';
					\Log::error('Chapter update failed', array(
						'chapter_id' => $id,
						'update_data' => $update_data
					));
				}
			}
		}

		$data['title'] = 'Sửa Chương - ' . $data['story']->title;
		$data['content'] = View::forge('admin/content/chapter_edit', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Xóa chương (AJAX)
	 * 
	 * @param int $id ID của chương
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

		$chapter = Model_Chapter::find($id);
		
		if (!$chapter) {
			return $this->error_response('Không tìm thấy chương.');
		}

		if ($chapter->soft_delete()) {
			return $this->success_response('Xóa chương thành công!');
		} else {
			return $this->error_response('Có lỗi xảy ra khi xóa chương.');
		}
	}

	/**
	 * Quản lý ảnh chương
	 * 
	 * @param int $id ID của chương
	 * @return void
	 */
	public function action_images($id = null)
	{
		$this->require_login();

		if (empty($id)) {
			Response::redirect('admin/stories');
		}

		$chapter = Model_Chapter::find($id);
		if (!$chapter) {
			Session::set_flash('error', 'Không tìm thấy chương.');
			Response::redirect('admin/stories');
		}

		$data = array();
		$data['chapter'] = $chapter;
		$data['story'] = $chapter->get_story();
		$data['images'] = $chapter->get_images();

		$data['title'] = 'Quản lý Ảnh - ' . $chapter->title;
		$data['content'] = View::forge('admin/content/chapter_images', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Upload ảnh chương (AJAX)
	 * 
	 * @param int $id ID của chương
	 * @return Response
	 */
	public function action_upload_image($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			return $this->error_response('Phương thức không hợp lệ.');
		}

		if (empty($id)) {
			return $this->error_response('ID không hợp lệ.');
		}

		$chapter = Model_Chapter::find($id);
		if (!$chapter) {
			return $this->error_response('Không tìm thấy chương.');
		}

		// Xử lý upload file
		$upload = Upload::forge('image', array(
			'path' => DOCROOT . 'uploads/chapters/',
			'randomize' => true,
			'ext_whitelist' => array('jpg', 'jpeg', 'png', 'gif', 'webp'),
			'max_size' => 10 * 1024 * 1024, // 10MB
		));

		if ($upload->run()) {
			$file_path = 'uploads/chapters/' . $upload->data('saved_as');
			
			// Thêm ảnh vào chương
			if ($chapter->add_image($file_path)) {
				return $this->success_response('Upload ảnh thành công!', array(
					'image_path' => $file_path
				));
			} else {
				return $this->error_response('Có lỗi xảy ra khi lưu ảnh.');
			}
		} else {
			return $this->error_response('Upload ảnh thất bại: ' . implode(', ', $upload->errors()));
		}
	}

	/**
	 * Xóa ảnh chương (AJAX)
	 * 
	 * @param int $id ID của chương
	 * @return Response
	 */
	public function action_delete_image($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			return $this->error_response('Phương thức không hợp lệ.');
		}

		if (empty($id)) {
			return $this->error_response('ID không hợp lệ.');
		}

		$chapter = Model_Chapter::find($id);
		if (!$chapter) {
			return $this->error_response('Không tìm thấy chương.');
		}

		$image_path = Input::post('image_path', '');
		if (empty($image_path)) {
			return $this->error_response('Đường dẫn ảnh không hợp lệ.');
		}

		if ($chapter->remove_image($image_path)) {
			// Xóa file vật lý
			$full_path = DOCROOT . $image_path;
			if (file_exists($full_path)) {
				unlink($full_path);
			}
			
			return $this->success_response('Xóa ảnh thành công!');
		} else {
			return $this->error_response('Có lỗi xảy ra khi xóa ảnh.');
		}
	}

	/**
	 * API lấy danh sách chương (AJAX)
	 * 
	 * @param int $story_id ID của truyện
	 * @return Response
	 */
	public function action_api_list($story_id = null)
	{
		$this->require_login();

		if (empty($story_id)) {
			return $this->error_response('ID truyện không hợp lệ.');
		}

		$page = Input::get('page', 1);
		$limit = Input::get('limit', 10);
		$offset = ($page - 1) * $limit;

		$chapters = Model_Chapter::get_chapters_by_story($story_id, $limit, $offset);
		$total = Model_Chapter::count_by_story($story_id);

		$data = array(
			'chapters' => $chapters,
			'pagination' => array(
				'current_page' => $page,
				'total_pages' => ceil($total / $limit),
				'total_items' => $total,
				'items_per_page' => $limit
			)
		);

		return $this->success_response('Danh sách chương', $data);
	}

	/**
	 * Xử lý nhiều ảnh chương từ $_FILES
	 * 
	 * @param array $files $_FILES['images']
	 * @param int $story_id ID của truyện
	 * @param string $image_order JSON string của thứ tự ảnh
	 * @return array Mảng đường dẫn ảnh đã sắp xếp
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

			$allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/pjpeg');

			// Xử lý multiple files
			$file_count = count($files['name']);
			$temp_images = array();
			
			for ($i = 0; $i < $file_count; $i++) {
				if ($files['error'][$i] == 0) {
					// Validate file type
					if (!in_array($files['type'][$i], $allowed_types)) {
						\Log::warning('Chapter image skipped due to invalid mime type', array(
							'index' => $i,
							'type' => $files['type'][$i],
							'name' => $files['name'][$i]
						));
						continue;
					}

					// Validate file size (10MB)
					if ($files['size'][$i] > 10 * 1024 * 1024) {
						\Log::warning('Chapter image skipped due to size > 10MB', array(
							'index' => $i,
							'size' => $files['size'][$i],
							'name' => $files['name'][$i]
						));
						continue;
					}

					// Generate filename
					$extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
					$filename = 'chapter_' . time() . '_' . $i . '.' . $extension;
					$filepath = $upload_dir . $filename;

					// Move uploaded file
					if (move_uploaded_file($files['tmp_name'][$i], $filepath)) {
						$temp_images[$i] = 'uploads/chapters/story_' . $story_id . '/' . $filename;
					} else {
						\Log::error('Failed to move uploaded chapter image', array(
							'index' => $i,
							'tmp_name' => $files['tmp_name'][$i],
							'destination' => $filepath,
							'error' => isset($files['error'][$i]) ? $files['error'][$i] : null,
							'name' => $files['name'][$i]
						));
					}
				}
			}

			// Sắp xếp: FormData đã gửi file theo thứ tự mong muốn, fallback an toàn
			$uploaded_images = array_values($temp_images);
			if (!empty($image_order)) {
				$order_array = json_decode($image_order, true);
				// Chỉ thử sắp xếp lại nếu order là mảng số nguyên khớp index
				if (is_array($order_array) && !empty($order_array) && is_int(reset($order_array))) {
					$reordered = array();
					foreach ($order_array as $index) {
						if (isset($temp_images[$index])) {
							$reordered[] = $temp_images[$index];
						}
					}
					if (!empty($reordered)) {
						$uploaded_images = $reordered;
					}
				}
			}

			return $uploaded_images;
		} catch (\Exception $e) {
			\Log::error('Process chapter images failed: ' . $e->getMessage());
			return array();
		}
	}
}
