<?php

/**
 * Admin Author Controller
 * 
 * Xử lý quản lý tác giả (CRUD)
 * 
 * @package    App
 * @subpackage Controller\Admin
 */
class Controller_Admin_Author extends Controller_Admin_Base
{
	/**
	 * Quản lý tác giả
	 * 
	 * @return void
	 */
	public function action_index()
	{
		$this->require_login();

		$data = array();
		
		// Lấy tham số tìm kiếm và lọc
		$search = Input::get('search', '');
		$status = Input::get('status', '');
		$sort = Input::get('sort', 'created_at_desc');
		
		// Phân trang
		$page = Input::get('page', 1);
		$limit = 10;
		$offset = ($page - 1) * $limit;

		// Lấy danh sách tác giả với filter
		$data['authors'] = Model_Author::get_all_authors($limit, $offset, $search, $status, $sort);
		$data['total_authors'] = Model_Author::count_all($search, $status);
		$data['current_page'] = $page;
		$data['total_pages'] = ceil($data['total_authors'] / $limit);
		
		// Truyền filter data để giữ lại trong form
		$data['search'] = $search;
		$data['status'] = $status;
		$data['sort'] = $sort;

		$data['title'] = 'Quản lý Tác giả';
		
		$data['content'] = View::forge('admin/content/authors', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Thêm tác giả mới
	 * 
	 * @return void
	 */
	public function action_add()
	{
		try {
			$this->require_login();

		$data = array();
		$data['success_message'] = '';
		$data['error_message'] = '';
		$data['form_data'] = array();

		// Xử lý form thêm tác giả
		if (Input::method() === 'POST') {
			// Kiểm tra CSRF token
			if (!Security::check_token()) {
				$data['error_message'] = 'Token bảo mật không hợp lệ. Vui lòng thử lại.';
			} else {
				$name = Input::post('name', '');
				$description = Input::post('description', '');
				// Xử lý checkbox - nếu có trong POST thì là 1, không có thì là 0
				$is_active = Input::post('is_active') ? 1 : 0;

				// Tạo slug từ tên tác giả
				$slug = $this->create_slug($name);

				// Lưu form data để hiển thị lại nếu có lỗi
				$data['form_data'] = array(
					'name' => $name,
					'slug' => $slug,
					'description' => $description,
					'is_active' => $is_active
				);

				// Validation đơn giản - chỉ kiểm tra required
				if (!empty($name)) {
					// Xử lý upload avatar
					$avatar = null;
					if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
						$avatar = $this->upload_avatar($_FILES['avatar']);
						if ($avatar === null) {
							$data['error_message'] = 'Lỗi khi upload ảnh đại diện. Vui lòng kiểm tra định dạng và kích thước file.';
						}
					}

					if (empty($data['error_message'])) {
						// Tạo dữ liệu author
						$author_data = array(
							'name' => $name,
							'slug' => $slug,
							'description' => $description,
							'avatar' => $avatar,
							'is_active' => $is_active
						);

						// Tạo author mới
						$new_author = Model_Author::create_author($author_data);
						if ($new_author) {
							Session::set_flash('success', 'Thêm tác giả thành công!');
							Response::redirect('admin/authors');
						} else {
							$data['error_message'] = 'Có lỗi xảy ra khi thêm tác giả.';
						}
					}
				} else {
					$data['error_message'] = 'Tên tác giả không được để trống.';
				}
			}
		}

		$data['title'] = 'Thêm Tác giả';
		$data['content'] = View::forge('admin/content/author_add', $data, false);
		return View::forge('layouts/admin', $data);
		
		} catch (\Exception $e) {
			// Log error
			\Log::error('Author add error: ' . $e->getMessage());
			
			// Show error page
			throw new \HttpNotFoundException('Có lỗi xảy ra khi thêm tác giả: ' . $e->getMessage());
		}
	}

	/**
	 * Sửa tác giả
	 * 
	 * @param int $id ID của tác giả
	 * @return void
	 */
	public function action_edit($id = null)
	{
		$this->require_login();

		// Sanitize id
		$id = (int) $id;
		if (empty($id) || $id <= 0) {
			Response::redirect('admin/authors');
		}

		$data = array();
		$data['success_message'] = '';
		$data['error_message'] = '';
		$data['author'] = Model_Author::find($id);

		if (!$data['author']) {
			Session::set_flash('error', 'Không tìm thấy tác giả.');
			Response::redirect('admin/authors');
		}

		// Xử lý form sửa tác giả
		if (Input::method() === 'POST') {
			// Kiểm tra CSRF token
			if (!Security::check_token()) {
				$data['error_message'] = 'Token bảo mật không hợp lệ. Vui lòng thử lại.';
			} else {
				$name = Input::post('name', '');
				$description = Input::post('description', '');
				// Xử lý checkbox - nếu có trong POST thì là 1, không có thì là 0
				$is_active = Input::post('is_active') ? 1 : 0;

			// Validation đơn giản - chỉ kiểm tra required
			if (!empty($name)) {
				// Xử lý upload avatar
				$avatar = $data['author']->avatar; // Giữ avatar cũ
				if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
					\Log::info('Uploading new avatar for author: ' . $id);
					$new_avatar = $this->upload_avatar($_FILES['avatar']);
					if ($new_avatar !== null) {
						\Log::info('Avatar uploaded successfully: ' . $new_avatar);
						// Xóa avatar cũ nếu có
						if ($avatar && file_exists(DOCROOT . $avatar)) {
							unlink(DOCROOT . $avatar);
							\Log::info('Old avatar deleted: ' . $avatar);
						}
						$avatar = $new_avatar;
					} else {
						\Log::error('Avatar upload failed');
						$data['error_message'] = 'Lỗi khi upload ảnh đại diện. Vui lòng kiểm tra định dạng và kích thước file.';
					}
				} else {
					\Log::info('No new avatar uploaded, keeping current: ' . $avatar);
				}

				if (empty($data['error_message'])) {
					// Tạo slug từ tên tác giả
					$slug = $this->create_slug($name);
					
					// Cập nhật tác giả
					$update_data = array(
						'name' => $name,
						'slug' => $slug,
						'description' => $description,
						'avatar' => $avatar,
						'is_active' => $is_active,
					);

					if ($data['author']->update_author($update_data)) {
						Session::set_flash('success', 'Cập nhật tác giả thành công!');
						Response::redirect('admin/authors');
					} else {
						$data['error_message'] = 'Có lỗi xảy ra khi cập nhật tác giả.';
					}
				}
				} else {
					$data['error_message'] = 'Tên tác giả không được để trống.';
				}
			}
		}

		$data['title'] = 'Sửa Tác giả';
		$data['content'] = View::forge('admin/content/author_edit', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Xóa tác giả (AJAX)
	 * 
	 * @param int $id ID của tác giả
	 * @return Response
	 */
	public function action_delete($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/authors');
		}

		// Kiểm tra CSRF token
		if (!Security::check_token()) {
			Session::set_flash('error', 'Token bảo mật không hợp lệ. Vui lòng thử lại.');
			Response::redirect('admin/authors');
		}

		if (empty($id)) {
			\Session::set_flash('error', 'ID không hợp lệ.');
			\Response::redirect('admin/authors');
		}

		$author = Model_Author::find($id);
		
		if (!$author) {
			\Session::set_flash('error', 'Không tìm thấy tác giả.');
			\Response::redirect('admin/authors');
		}

		if ($author->soft_delete()) {
			\Session::set_flash('success', 'Xóa tác giả thành công!');
			\Response::redirect('admin/authors');
		} else {
			\Session::set_flash('error', 'Có lỗi xảy ra khi xóa tác giả.');
			\Response::redirect('admin/authors');
		}
	}

	/**
	 * Màn sọt rác - hiển thị tác giả đã xóa
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

		// Lấy danh sách tác giả đã xóa
		$data['authors'] = Model_Author::get_deleted_authors($limit, $offset, $search, $sort);
		\Log::info("Found " . count($data['authors']) . " deleted authors for trash view");
		$data['total_authors'] = Model_Author::count_deleted($search);
		$data['current_page'] = $page;
		$data['total_pages'] = ceil($data['total_authors'] / $limit);
		
		// Truyền filter data để giữ lại trong form
		$data['search'] = $search;
		$data['sort'] = $sort;

		$data['title'] = 'Sọt rác - Tác giả đã xóa';
		
		$data['content'] = View::forge('admin/content/authors_trash', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * Khôi phục tác giả từ sọt rác
	 * 
	 * @param int $id ID của tác giả
	 * @return void
	 */
	public function action_restore($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/authors/trash');
		}

		if (empty($id)) {
			\Session::set_flash('error', 'ID không hợp lệ.');
			\Response::redirect('admin/authors/trash');
		}

		$author = Model_Author::find($id);
		
		if (!$author) {
			\Session::set_flash('error', 'Không tìm thấy tác giả.');
			\Response::redirect('admin/authors/trash');
		}

		if ($author->restore()) {
			\Session::set_flash('success', 'Khôi phục tác giả thành công!');
			\Response::redirect('admin/authors');
		} else {
			\Session::set_flash('error', 'Có lỗi xảy ra khi khôi phục tác giả.');
			\Response::redirect('admin/authors/trash');
		}
	}

	/**
	 * Xóa vĩnh viễn tác giả
	 * 
	 * @param int $id ID của tác giả
	 * @return void
	 */
	public function action_force_delete($id = null)
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/authors/trash');
		}

		if (empty($id)) {
			\Session::set_flash('error', 'ID không hợp lệ.');
			\Response::redirect('admin/authors/trash');
		}

		$author = Model_Author::find($id);
		
		if (!$author) {
			\Session::set_flash('error', 'Không tìm thấy tác giả.');
			\Response::redirect('admin/authors/trash');
		}

		if ($author->force_delete()) {
			\Session::set_flash('success', 'Xóa vĩnh viễn tác giả thành công!');
			\Response::redirect('admin/authors/trash');
		} else {
			\Session::set_flash('error', 'Có lỗi xảy ra khi xóa vĩnh viễn tác giả.');
			\Response::redirect('admin/authors/trash');
		}
	}

	/**
	 * Chi tiết tác giả
	 * 
	 * @param int $id ID của tác giả
	 * @return void
	 */
	public function action_view($id = null)
	{
		$this->require_login();

		if (empty($id)) {
			Response::redirect('admin/authors');
		}

		$author = Model_Author::find($id);
		if (!$author) {
			Session::set_flash('error', 'Không tìm thấy tác giả.');
			Response::redirect('admin/authors');
		}

		$data = array();
		$data['author'] = $author;
		
		// Lấy danh sách truyện của tác giả
		$data['stories'] = $author->get_stories();
		$data['story_count'] = $author->get_story_count();

		$data['title'] = 'Chi tiết Tác giả - ' . $author->name;
		$data['content'] = View::forge('admin/content/author_view', $data, false);
		return View::forge('layouts/admin', $data);
	}

	/**
	 * API lấy danh sách tác giả (AJAX)
	 * 
	 * @return Response
	 */
	public function action_api_list()
	{
		$this->require_login();

		$page = Input::get('page', 1);
		$limit = Input::get('limit', 10);
		$offset = ($page - 1) * $limit;

		$authors = Model_Author::get_all_authors($limit, $offset);
		$total = Model_Author::count_all();

		$data = array(
			'authors' => $authors,
			'pagination' => array(
				'current_page' => $page,
				'total_pages' => ceil($total / $limit),
				'total_items' => $total,
				'items_per_page' => $limit
			)
		);

		return $this->success_response('Danh sách tác giả', $data);
	}

	/**
	 * API tạo tác giả nhanh (AJAX)
	 * 
	 * @return Response
	 */
	public function action_api_create()
	{
		$this->require_login();

		$name = Input::post('name', '');
		$description = Input::post('description', '');

		if (empty($name)) {
			return $this->error_response('Vui lòng nhập tên tác giả.');
		}

		$author_data = array(
			'name' => $name,
			'description' => $description,
		);

		$new_author = Model_Author::create_author($author_data);
		if ($new_author) {
			return $this->success_response('Tạo tác giả thành công!', $new_author);
		} else {
			return $this->error_response('Có lỗi xảy ra khi tạo tác giả.');
		}
	}

	/**
	 * API tìm kiếm tác giả (AJAX)
	 * 
	 * @return Response
	 */
	public function action_api_search()
	{
		$this->require_login();

		$keyword = Input::get('q', '');
		$limit = Input::get('limit', 10);

		if (empty($keyword)) {
			return $this->error_response('Vui lòng nhập từ khóa tìm kiếm.');
		}

		// Tìm kiếm tác giả theo tên
		$authors = Model_Author::get_all_authors();
		$filtered_authors = array();
		
		foreach ($authors as $author) {
			if (stripos($author->name, $keyword) !== false) {
				$filtered_authors[] = $author;
				if (count($filtered_authors) >= $limit) {
					break;
				}
			}
		}

		return $this->success_response('Kết quả tìm kiếm', $filtered_authors);
	}

	/**
	 * Xóa hàng loạt tác giả
	 * 
	 * @return void
	 */
	public function action_bulk_delete()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/authors');
		}

		$author_ids = Input::post('author_ids', array());
		
		if (empty($author_ids)) {
			Session::set_flash('error', 'Vui lòng chọn ít nhất một tác giả để xóa.');
			Response::redirect('admin/authors');
		}

		$deleted_count = 0;
		$error_count = 0;

		foreach ($author_ids as $id) {
			$author = Model_Author::find($id);
			if ($author && !$author->deleted_at) {
				if ($author->soft_delete()) {
					$deleted_count++;
				} else {
					$error_count++;
				}
			} else {
				$error_count++;
			}
		}

		if ($deleted_count > 0) {
			// Tạo CSRF token mới sau khi xử lý thành công
			$new_csrf_token = Security::fetch_token();
			
			$data = array(
				'affected' => (int) $deleted_count,
				'csrf_token' => $new_csrf_token
			);
			
			return $this->success_response("Đã xóa thành công {$deleted_count} tác giả.", $data);
		}
		
		if ($error_count > 0) {
			return $this->error_response("Có {$error_count} tác giả không thể xóa.");
		}
		
		return $this->error_response('Không có tác giả nào được xóa.');
	}

	/**
	 * Khôi phục hàng loạt tác giả
	 * 
	 * @return void
	 */
	public function action_bulk_restore()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/authors/trash');
		}

		$author_ids = Input::post('author_ids', array());
		
		\Log::info("Bulk restore author IDs: " . implode(', ', $author_ids));
		
		if (empty($author_ids)) {
			Session::set_flash('error', 'Vui lòng chọn ít nhất một tác giả để khôi phục.');
			Response::redirect('admin/authors/trash');
		}

		$restored_count = 0;
		$error_count = 0;

		foreach ($author_ids as $id) {
			$author = Model_Author::find($id);
			\Log::info("Restore author ID: {$id}, Found: " . ($author ? 'Yes' : 'No') . ", Deleted_at: " . ($author ? $author->deleted_at : 'N/A'));
			if ($author && $author->deleted_at) {
				if ($author->restore()) {
					$restored_count++;
				} else {
					$error_count++;
				}
			} else {
				$error_count++;
			}
		}

		if ($restored_count > 0) {
			Session::set_flash('success', "Đã khôi phục thành công {$restored_count} tác giả.");
		}
		
		if ($error_count > 0) {
			Session::set_flash('error', "Có {$error_count} tác giả không thể khôi phục.");
		}

		Response::redirect('admin/authors/trash');
	}

	/**
	 * Xóa vĩnh viễn hàng loạt tác giả
	 * 
	 * @return void
	 */
	public function action_bulk_force_delete()
	{
		$this->require_login();

		if (Input::method() !== 'POST') {
			Response::redirect('admin/authors/trash');
		}

		$author_ids = Input::post('author_ids', array());
		
		\Log::info("Bulk force delete author IDs: " . implode(', ', $author_ids));
		
		if (empty($author_ids)) {
			Session::set_flash('error', 'Vui lòng chọn ít nhất một tác giả để xóa vĩnh viễn.');
			Response::redirect('admin/authors/trash');
		}

		$deleted_count = 0;
		$error_count = 0;

		foreach ($author_ids as $id) {
			$author = Model_Author::find($id);
			\Log::info("Force delete author ID: {$id}, Found: " . ($author ? 'Yes' : 'No') . ", Deleted_at: " . ($author ? $author->deleted_at : 'N/A') . ", Is_active: " . ($author ? $author->is_active : 'N/A'));
			
			if ($author && $author->deleted_at !== null && $author->deleted_at !== '') {
				if ($author->force_delete()) {
					$deleted_count++;
					\Log::info("Successfully force deleted author ID: {$id}");
				} else {
					$error_count++;
					\Log::error("Failed to force delete author ID: {$id}");
				}
			} else {
				$error_count++;
				\Log::warning("Author ID {$id} not found or not deleted: " . ($author ? "deleted_at = '{$author->deleted_at}', is_active = {$author->is_active}" : "not found"));
			}
		}

		if ($deleted_count > 0) {
			Session::set_flash('success', "Đã xóa vĩnh viễn thành công {$deleted_count} tác giả.");
		}
		
		if ($error_count > 0) {
			Session::set_flash('error', "Có {$error_count} tác giả không thể xóa vĩnh viễn.");
		}

		Response::redirect('admin/authors/trash');
	}

	/**
	 * Tạo slug từ tên
	 * 
	 * @param string $name Tên tác giả
	 * @return string Slug
	 */
	private function generate_slug($name)
	{
		// Chuyển về chữ thường
		$slug = strtolower($name);
		
		// Loại bỏ dấu tiếng Việt
		$slug = $this->remove_vietnamese_accents($slug);
		
		// Thay thế khoảng trắng và ký tự đặc biệt bằng dấu gạch ngang
		$slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
		
		// Loại bỏ dấu gạch ngang ở đầu và cuối
		$slug = trim($slug, '-');
		
		// Đảm bảo slug không trống
		if (empty($slug)) {
			$slug = 'author-' . time();
		}
		
		return $slug;
	}

	/**
	 * Loại bỏ dấu tiếng Việt
	 * 
	 * @param string $str Chuỗi cần xử lý
	 * @return string Chuỗi đã loại bỏ dấu
	 */
	private function remove_vietnamese_accents($str)
	{
		$accents = array(
			'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
			'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
			'ì', 'í', 'ị', 'ỉ', 'ĩ',
			'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
			'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
			'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
			'đ',
			'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ',
			'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ',
			'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ',
			'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ',
			'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ',
			'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ',
			'Đ'
		);
		
		$no_accents = array(
			'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
			'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
			'i', 'i', 'i', 'i', 'i',
			'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
			'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
			'y', 'y', 'y', 'y', 'y',
			'd',
			'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A',
			'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E',
			'I', 'I', 'I', 'I', 'I',
			'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O',
			'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U',
			'Y', 'Y', 'Y', 'Y', 'Y',
			'D'
		);
		
		return str_replace($accents, $no_accents, $str);
	}

	/**
	 * Upload avatar
	 * 
	 * @param array $file File upload
	 * @return string|null Đường dẫn avatar hoặc null nếu lỗi
	 */
	private function upload_avatar($file)
	{
		try {
			// Tạo thư mục upload
			$upload_dir = DOCROOT . 'uploads/authors/';
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
			$filename = 'author_' . time() . '_' . uniqid() . '.' . $extension;
			$filepath = $upload_dir . $filename;

			// Move uploaded file
			if (move_uploaded_file($file['tmp_name'], $filepath)) {
				return 'uploads/authors/' . $filename;
			}

			return null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Tạo slug từ tên tác giả
	 */
	private function create_slug($name)
	{
		// Chuyển về lowercase
		$slug = strtolower($name);
		
		// Loại bỏ dấu tiếng Việt
		$slug = $this->remove_vietnamese_accents($slug);
		
		// Thay thế khoảng trắng và ký tự đặc biệt bằng dấu gạch ngang
		$slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
		
		// Loại bỏ dấu gạch ngang ở đầu và cuối
		$slug = trim($slug, '-');
		
		// Đảm bảo slug không rỗng
		if (empty($slug)) {
			$slug = 'author-' . time();
		}
		
		return $slug;
	}

}
