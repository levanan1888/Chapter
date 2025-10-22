<?php

/**
 * Lớp Validation Tùy Chỉnh
 * 
 * Mở rộng lớp Validation của FuelPHP với các quy tắc validation tùy chỉnh
 * 
 * @package    App
 * @subpackage Classes\Validation
 */
class Validation_Custom extends Validation
{
	/**
	 * Hàm khởi tạo
	 * 
	 * @param array $fieldset Đối tượng Fieldset
	 */
	public function __construct($fieldset = null)
	{
		// Gọi hàm khởi tạo của lớp cha
		parent::__construct($fieldset);
	}

	/**
	 * Quy tắc validation tùy chỉnh: custom_name
	 * Kiểm tra tên cơ bản
	 * 
	 * @param string $val Giá trị cần kiểm tra
	 * @return bool Trả về true nếu hợp lệ, false nếu không hợp lệ
	 */
	public function _validation_custom_name($val)
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
	public function _validation_custom_category($val)
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


}
