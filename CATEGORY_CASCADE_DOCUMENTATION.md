# Cascade Effect cho Category Management

## Tổng quan
Tính năng cascade effect đảm bảo rằng khi xóa hoặc ẩn danh mục, tất cả truyện thuộc danh mục đó cũng sẽ bị ảnh hưởng tương ứng.

## Các hành động được hỗ trợ

### 1. Soft Delete Category (Xóa mềm danh mục)
- **Hành động**: Xóa danh mục vào thùng rác
- **Cascade effect**: 
  - Tất cả truyện thuộc danh mục sẽ bị ẩn (`is_visible = 0`)
  - Liên kết trong bảng `story_categories` vẫn được giữ lại
- **Khôi phục**: Khi restore danh mục, các truyện sẽ được hiển thị lại

### 2. Force Delete Category (Xóa vĩnh viễn)
- **Hành động**: Xóa hoàn toàn danh mục khỏi database
- **Cascade effect**: 
  - Xóa tất cả liên kết trong bảng `story_categories`
  - Truyện không bị xóa, chỉ mất liên kết với danh mục

### 3. Hide/Show Category (Ẩn/Hiện danh mục)
- **Hành động**: Thay đổi trạng thái `is_active` của danh mục
- **Cascade effect**: 
  - Khi ẩn danh mục (`is_active = 0`): Tất cả truyện thuộc danh mục sẽ bị ẩn
  - Khi hiện danh mục (`is_active = 1`): Tất cả truyện thuộc danh mục sẽ được hiển thị

## Các phương thức đã được cập nhật

### Model_Category

#### `soft_delete()`
```php
public function soft_delete()
```
- Xóa danh mục vào thùng rác
- Ẩn tất cả truyện liên quan
- Xóa liên kết trong `story_categories`

#### `restore()`
```php
public function restore()
```
- Khôi phục danh mục từ thùng rác
- Hiển thị lại tất cả truyện liên quan

#### `force_delete()`
```php
public function force_delete()
```
- Xóa vĩnh viễn danh mục
- Xóa tất cả liên kết trong `story_categories`

#### `update_visibility($is_active)`
```php
public function update_visibility($is_active)
```
- Cập nhật trạng thái hiển thị của danh mục
- Áp dụng cascade effect cho các truyện liên quan

#### `update_category(array $data)`
```php
public function update_category(array $data)
```
- Cập nhật thông tin danh mục
- Tự động áp dụng cascade effect khi thay đổi `is_active`

### Các phương thức helper

#### `hide_related_stories()`
```php
private function hide_related_stories()
```
- Ẩn tất cả truyện thuộc danh mục

#### `show_related_stories()`
```php
private function show_related_stories()
```
- Hiển thị lại tất cả truyện thuộc danh mục

#### `remove_story_category_links()`
```php
private function remove_story_category_links()
```
- Xóa liên kết trong bảng `story_categories`

## Giao diện người dùng

### Cảnh báo trong danh sách danh mục
- Danh mục có truyện sẽ hiển thị badge màu vàng với icon cảnh báo
- Tooltip hiển thị số lượng truyện và cảnh báo về cascade effect

### Xác nhận xóa
- Dialog xác nhận chi tiết khi xóa danh mục có truyện
- Hiển thị số lượng truyện sẽ bị ẩn
- Giải thích rõ hậu quả của hành động

## Database Transactions
Tất cả các thao tác cascade đều được thực hiện trong database transactions để đảm bảo tính nhất quán dữ liệu.

## Logging
Tất cả các thao tác cascade đều được ghi log để dễ dàng debug và theo dõi.

## Testing
Sử dụng file `fuel/app/tasks/test_category_cascade.php` để test các tính năng cascade:

```bash
php oil console
include 'fuel/app/tasks/test_category_cascade.php';
```

## Migration
Chạy migration để đảm bảo tất cả truyện có trường `is_visible` được set đúng:

```bash
php oil migrate
```

## Lưu ý quan trọng
1. **Backup dữ liệu**: Luôn backup database trước khi thực hiện các thao tác cascade
2. **Kiểm tra kỹ**: Đảm bảo hiểu rõ hậu quả trước khi xóa danh mục có truyện
3. **Khôi phục**: Có thể khôi phục bằng cách restore danh mục từ thùng rác
4. **Performance**: Cascade effect có thể ảnh hưởng đến performance với danh mục có nhiều truyện
