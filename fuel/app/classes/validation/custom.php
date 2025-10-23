<?php

/**
 * Lớp Validation Tùy Chỉnh
 * 
 * Các quy tắc validation tùy chỉnh dùng với FuelPHP Validation
 * Thêm vào validator bằng `$val->add_callable('Validation_Custom')`.
 */
class Validation_Custom
{

	/**
	 * Quy tắc validation tùy chỉnh: custom_name
	 * Kiểm tra tên cơ bản
	 * 
	 * @param string $val Giá trị cần kiểm tra
	 * @return bool Trả về true nếu hợp lệ, false nếu không hợp lệ
	 */
    public static function _validation_custom_name($val)
	{
		// Kiểm tra giá trị không rỗng và có độ dài tối thiểu 2 ký tự
		return !empty($val) && strlen(trim($val)) >= 2;
	}

	/**
	 * Quy tắc validation tùy chỉnh: custom_category
	 * Ngăn chặn việc sử dụng chữ "n" trong tên danh mục
	 * 
	 * @param string $val Giá trị cần kiểm tra
	 * @return bool Trả về true nếu không chứa chữ "n", false nếu chứa chữ "n"
	 */
    public static function _validation_custom_category($val)
{
    // Chuẩn hóa xóa khoảng trắng đầu cuối
    $val = trim($val);

    // Kiểm tra chuỗi chỉ chứa ký tự chữ cái tiếng Việt và khoảng trắng
    // \p{L} hỗ trợ tất cả các chữ cái Unicode, bao gồm cả tiếng Việt có dấu
    if (!preg_match('/^[\p{L}\s]+$/u', $val)) {
        return false; // Chuỗi có ký tự không hợp lệ
    }

    // Có thể thêm kiểm tra độ dài, ví dụ từ 2 ký tự trở lên
    if (mb_strlen($val, 'UTF-8') < 2) {
        return false; // Tên quá ngắn
    }

    return true; // Tên hợp lệ
}

	/**
	 * Quy tắc validation tùy chỉnh: chapter_title
	 * Kiểm tra tên chương hợp lệ
	 * 
	 * @param string $val Giá trị cần kiểm tra
	 * @return bool Trả về true nếu hợp lệ, false nếu không hợp lệ
	 */
    public static function _validation_chapter_title($val)
	{
		// Chuẩn hóa xóa khoảng trắng đầu cuối
		$val = trim($val);
		
		// Kiểm tra không rỗng
		if (empty($val)) {
			return false;
		}
		
		// Kiểm tra độ dài tối thiểu và tối đa
		$length = mb_strlen($val, 'UTF-8');
		if ($length < 2 || $length > 200) {
			return false;
		}
		
		// Kiểm tra không chứa ký tự đặc biệt nguy hiểm
		if (preg_match('/[<>"\']/', $val)) {
			return false;
		}
		
		return true;
	}

	/**
	 * Quy tắc validation tùy chỉnh: chapter_number
	 * Kiểm tra số thứ tự chương hợp lệ
	 * 
	 * @param mixed $val Giá trị cần kiểm tra
	 * @return bool Trả về true nếu hợp lệ, false nếu không hợp lệ
	 */
    public static function _validation_chapter_number($val)
	{
		// Kiểm tra là số nguyên dương
		if (!is_numeric($val) || (int)$val != $val || (int)$val <= 0) {
			return false;
		}
		
		// Kiểm tra trong phạm vi hợp lý (1-9999)
		$number = (int)$val;
		if ($number < 1 || $number > 9999) {
			return false;
		}
		
		return true;
	}

    /**
     * Quy tắc validation tùy chỉnh: chapter_number_unique
     * Kiểm tra số thứ tự chương không trùng lặp trong cùng story
     * 
     * Lưu ý: Đọc các field liên quan qua $this->input() để tương thích Fuel Validation.
     * 
     * @param mixed $val Giá trị cần kiểm tra
     * @return bool Trả về true nếu không trùng lặp, false nếu trùng lặp
     */
    public static function _validation_chapter_number_unique($val)
    {
        // Lấy dữ liệu liên quan từ input validation
        $story_id = \Validation::active() ? \Validation::active()->input('story_id') : null;
        $chapter_id = \Validation::active() ? \Validation::active()->input('chapter_id') : null; // có thể null ở màn thêm

        if (empty($story_id)) {
            return false;
        }

        try {
            if (!empty($chapter_id)) {
                $query = \DB::query("SELECT COUNT(*) as total FROM chapters WHERE story_id = :story_id AND chapter_number = :chapter_number AND id != :chapter_id AND deleted_at IS NULL");
                $query->param('story_id', (int)$story_id);
                $query->param('chapter_number', (int)$val);
                $query->param('chapter_id', (int)$chapter_id);
            } else {
                $query = \DB::query("SELECT COUNT(*) as total FROM chapters WHERE story_id = :story_id AND chapter_number = :chapter_number AND deleted_at IS NULL");
                $query->param('story_id', (int)$story_id);
                $query->param('chapter_number', (int)$val);
            }

            $result = $query->execute();
            return ((int)$result->current()['total']) === 0;
        } catch (\Exception $e) {
            \Log::error('Chapter number unique validation error: ' . $e->getMessage());
            return false;
        }
    }

	/**
	 * Quy tắc validation tùy chỉnh: chapter_images
	 * Kiểm tra danh sách ảnh chương hợp lệ
	 * 
	 * @param array $val Giá trị cần kiểm tra
	 * @return bool Trả về true nếu hợp lệ, false nếu không hợp lệ
	 */
    public static function _validation_chapter_images($val)
	{
		// Cho phép mảng rỗng (chương không có ảnh)
		if (empty($val) || !is_array($val)) {
			return true;
		}
		
		// Kiểm tra số lượng ảnh không quá nhiều
		if (count($val) > 50) {
			return false;
		}
		
		// Kiểm tra từng đường dẫn ảnh
		foreach ($val as $image_path) {
			if (!is_string($image_path) || empty($image_path)) {
				return false;
			}
			
			// Kiểm tra định dạng file hợp lệ
			$extension = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
			$allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
			if (!in_array($extension, $allowed_extensions)) {
				return false;
			}
		}
		
		return true;
	}

	/**
	 * Quy tắc validation tùy chỉnh: story_exists
	 * Kiểm tra story tồn tại và có thể tạo chương
	 * 
	 * @param mixed $val Giá trị cần kiểm tra
	 * @return bool Trả về true nếu story tồn tại, false nếu không tồn tại
	 */
    public static function _validation_story_exists($val)
	{
		if (empty($val) || !is_numeric($val)) {
			return false;
		}
		
		try {
			$story = Model_Story::find_admin((int)$val);
			return $story !== null;
		} catch (\Exception $e) {
			\Log::error('Story exists validation error: ' . $e->getMessage());
			return false;
		}
	}


}
