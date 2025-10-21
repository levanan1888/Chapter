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

		$story = Model_Story::find($story_id);
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

		// Lấy danh sách chương
		$data['chapters'] = Model_Chapter::get_chapters_by_story($story_id, $limit, $offset);
		$data['total_chapters'] = Model_Chapter::count_by_story($story_id);
		$data['current_page'] = $page;
		$data['total_pages'] = ceil($data['total_chapters'] / $limit);

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

		$story = Model_Story::find($story_id);
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

			// Kiểm tra dữ liệu đầu vào
			if (empty($title) || empty($chapter_number)) {
				$data['error_message'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
			} else {
				// Tạo chương mới
				$chapter_data = array(
					'story_id' => $story_id,
					'title' => $title,
					'chapter_number' => $chapter_number,
					'images' => array(), // Sẽ được cập nhật sau khi upload ảnh
				);

				$new_chapter = Model_Chapter::create_chapter($chapter_data);
				if ($new_chapter) {
					$data['success_message'] = 'Thêm chương thành công!';
					// Reset form
					$data['form_data'] = array();
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
			$title = Input::post('title', '');
			$chapter_number = Input::post('chapter_number', '');

			// Kiểm tra dữ liệu đầu vào
			if (empty($title) || empty($chapter_number)) {
				$data['error_message'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
			} else {
				// Cập nhật chương
				$update_data = array(
					'title' => $title,
					'chapter_number' => $chapter_number,
				);

				if ($data['chapter']->update_chapter($update_data)) {
					$data['success_message'] = 'Cập nhật chương thành công!';
					// Reload data
					$data['chapter'] = Model_Chapter::find($id);
				} else {
					$data['error_message'] = 'Có lỗi xảy ra khi cập nhật chương.';
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
}
