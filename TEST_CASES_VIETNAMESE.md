# TEST CASES - HỆ THỐNG QUẢN LÝ TRUYỆN TRANH

## 1. MÀN HÌNH DASHBOARD ADMIN

### 1.1 Kiểm tra đăng nhập và hiển thị thông tin cơ bản
**Mục đích:** Kiểm tra dashboard hiển thị đúng thông tin admin và thống kê tổng quan

**Các bước thực hiện:**
1. Truy cập URL: `http://localhost/project-story/public/admin/dashboard`
2. Kiểm tra yêu cầu đăng nhập (nếu chưa đăng nhập sẽ redirect về trang login)
3. Sau khi đăng nhập, kiểm tra các thông tin hiển thị:
   - Tên admin hiện tại
   - Tổng số admin trong hệ thống
   - Tổng số truyện tranh
   - Tổng số chương
   - Tổng số danh mục
   - Tổng số tác giả

**Kết quả mong đợi:**
- Hiển thị đầy đủ thông tin thống kê
- Giao diện responsive và đẹp mắt
- Không có lỗi JavaScript hoặc CSS

### 1.2 Kiểm tra danh sách truyện mới nhất
**Mục đích:** Xác minh danh sách truyện mới nhất hiển thị đúng

**Các bước thực hiện:**
1. Đăng nhập admin
2. Truy cập dashboard
3. Kiểm tra phần "Truyện mới nhất"
4. Xác minh hiển thị tối đa 5 truyện
5. Kiểm tra thông tin mỗi truyện: tên, tác giả, ngày tạo

**Kết quả mong đợi:**
- Hiển thị đúng 5 truyện mới nhất
- Thông tin truyện đầy đủ và chính xác
- Link đến trang chi tiết truyện hoạt động

### 1.3 Kiểm tra danh sách truyện hot
**Mục đích:** Xác minh danh sách truyện được yêu thích nhất

**Các bước thực hiện:**
1. Đăng nhập admin
2. Truy cập dashboard
3. Kiểm tra phần "Truyện hot"
4. Xác minh hiển thị tối đa 5 truyện
5. Kiểm tra sắp xếp theo độ phổ biến

**Kết quả mong đợi:**
- Hiển thị đúng 5 truyện hot nhất
- Sắp xếp theo thứ tự giảm dần của lượt yêu thích
- Link hoạt động chính xác

### 1.4 Kiểm tra biểu đồ thống kê
**Mục đích:** Xác minh biểu đồ thống kê hiển thị đúng dữ liệu

**Các bước thực hiện:**
1. Đăng nhập admin
2. Truy cập dashboard
3. Kiểm tra biểu đồ thống kê (nếu có)
4. Xác minh dữ liệu biểu đồ chính xác

**Kết quả mong đợi:**
- Biểu đồ hiển thị đúng dữ liệu
- Không có lỗi JavaScript
- Biểu đồ responsive trên mobile

---

## 2. MÀN HÌNH QUẢN LÝ DANH MỤC

### 2.1 Kiểm tra hiển thị danh sách danh mục
**Mục đích:** Xác minh danh sách danh mục hiển thị đầy đủ thông tin

**Các bước thực hiện:**
1. Truy cập URL: `http://localhost/project-story/public/admin/categories`
2. Kiểm tra tiêu đề trang: "Quản lý Danh mục"
3. Kiểm tra các nút: "Sọt rác", "Thêm danh mục mới"
4. Kiểm tra bảng danh sách danh mục:
   - Tên danh mục
   - Mô tả
   - Trạng thái (Hoạt động/Không hoạt động)
   - Ngày tạo
   - Thao tác (Sửa/Xóa)

**Kết quả mong đợi:**
- Hiển thị đầy đủ danh sách danh mục
- Giao diện bảng đẹp và dễ đọc
- Các nút thao tác hoạt động

### 2.2 Kiểm tra chức năng tìm kiếm danh mục
**Mục đích:** Xác minh tìm kiếm danh mục hoạt động chính xác

**Các bước thực hiện:**
1. Truy cập trang quản lý danh mục
2. Nhập từ khóa tìm kiếm vào ô "Tìm kiếm"
3. Nhấn nút "Tìm kiếm"
4. Kiểm tra kết quả trả về
5. Thử tìm kiếm với từ khóa không tồn tại

**Kết quả mong đợi:**
- Tìm kiếm trả về kết quả chính xác
- Hiển thị thông báo khi không tìm thấy
- URL có chứa tham số tìm kiếm

### 2.3 Kiểm tra bộ lọc trạng thái
**Mục đích:** Xác minh bộ lọc theo trạng thái hoạt động

**Các bước thực hiện:**
1. Truy cập trang quản lý danh mục
2. Chọn "Hoạt động" trong dropdown trạng thái
3. Nhấn "Tìm kiếm"
4. Kiểm tra chỉ hiển thị danh mục đang hoạt động
5. Thử với "Không hoạt động" và "Đã xóa"

**Kết quả mong đợi:**
- Bộ lọc hoạt động chính xác
- Chỉ hiển thị danh mục theo trạng thái đã chọn
- URL cập nhật với tham số trạng thái

### 2.4 Kiểm tra sắp xếp danh mục
**Mục đích:** Xác minh chức năng sắp xếp danh mục

**Các bước thực hiện:**
1. Truy cập trang quản lý danh mục
2. Chọn "Tên A-Z" trong dropdown sắp xếp
3. Nhấn "Tìm kiếm"
4. Kiểm tra danh sách sắp xếp theo tên
5. Thử với các tùy chọn khác: "Tên Z-A", "Mới nhất", "Cũ nhất"

**Kết quả mong đợi:**
- Sắp xếp theo đúng tiêu chí đã chọn
- Thứ tự hiển thị chính xác
- URL cập nhật với tham số sắp xếp

### 2.5 Kiểm tra thêm danh mục mới
**Mục đích:** Xác minh chức năng thêm danh mục mới

**Các bước thực hiện:**
1. Nhấn nút "Thêm danh mục mới"
2. Kiểm tra chuyển đến trang thêm danh mục
3. Nhập thông tin danh mục:
   - Tên danh mục: "Truyện Hành Động"
   - Mô tả: "Các truyện tranh thể loại hành động"
   - Trạng thái: "Hoạt động"
4. Nhấn "Lưu"
5. Kiểm tra quay về danh sách và hiển thị danh mục mới

**Kết quả mong đợi:**
- Chuyển trang thành công
- Form thêm danh mục hiển thị đầy đủ
- Lưu thành công và hiển thị thông báo
- Danh mục mới xuất hiện trong danh sách

### 2.6 Kiểm tra sửa danh mục
**Mục đích:** Xác minh chức năng chỉnh sửa danh mục

**Các bước thực hiện:**
1. Tìm danh mục cần sửa trong danh sách
2. Nhấn nút "Sửa" (icon bút chì)
3. Kiểm tra chuyển đến trang sửa danh mục
4. Thay đổi thông tin:
   - Sửa tên danh mục
   - Sửa mô tả
   - Thay đổi trạng thái
5. Nhấn "Cập nhật"
6. Kiểm tra quay về danh sách và thông tin đã được cập nhật

**Kết quả mong đợi:**
- Chuyển đến trang sửa với dữ liệu hiện tại
- Cập nhật thành công
- Thông tin mới hiển thị trong danh sách
- Thông báo thành công

### 2.7 Kiểm tra xóa danh mục
**Mục đích:** Xác minh chức năng xóa danh mục

**Các bước thực hiện:**
1. Tìm danh mục cần xóa trong danh sách
2. Nhấn nút "Xóa" (icon thùng rác)
3. Xác nhận xóa trong popup
4. Kiểm tra danh mục biến mất khỏi danh sách
5. Kiểm tra danh mục xuất hiện trong "Sọt rác"

**Kết quả mong đợi:**
- Hiển thị popup xác nhận xóa
- Xóa thành công
- Danh mục chuyển vào trạng thái "Đã xóa"
- Thông báo xóa thành công

### 2.8 Kiểm tra sọt rác danh mục
**Mục đích:** Xác minh chức năng quản lý sọt rác

**Các bước thực hiện:**
1. Nhấn nút "Sọt rác"
2. Kiểm tra chuyển đến trang sọt rác
3. Kiểm tra chỉ hiển thị danh mục đã xóa
4. Thử khôi phục danh mục:
   - Nhấn nút "Khôi phục"
   - Xác nhận khôi phục
5. Thử xóa vĩnh viễn:
   - Nhấn nút "Xóa vĩnh viễn"
   - Xác nhận xóa

**Kết quả mong đợi:**
- Hiển thị danh sách danh mục đã xóa
- Khôi phục thành công
- Xóa vĩnh viễn thành công
- Cập nhật danh sách sau mỗi thao tác

### 2.9 Kiểm tra CSRF token khi chuyển màn hình
**Mục đích:** Xác minh CSRF token hoạt động đúng khi chuyển màn hình khác rồi quay lại

**Các bước thực hiện:**
1. Truy cập trang quản lý danh mục
2. Thử chuyển trạng thái một danh mục (toggle switch)
3. Chuyển sang màn hình khác (ví dụ: Dashboard, Quản lý truyện)
4. Quay lại trang quản lý danh mục
5. Thử chuyển trạng thái danh mục khác
6. Kiểm tra có lỗi CSRF không
7. Mở Developer Tools (F12) để xem console log
8. Kiểm tra CSRF token được refresh tự động

**Kết quả mong đợi:**
- Không có lỗi CSRF khi chuyển trạng thái
- CSRF token được refresh tự động khi quay lại trang
- Console log hiển thị "CSRF token refreshed"
- Toggle switch hoạt động bình thường
- Không cần refresh trang thủ công

### 2.10 Kiểm tra CSRF token với thời gian dài
**Mục đích:** Xác minh CSRF token hoạt động sau khi để trang lâu

**Các bước thực hiện:**
1. Truy cập trang quản lý danh mục
2. Để trang yên trong 10-15 phút
3. Thử chuyển trạng thái danh mục
4. Kiểm tra có lỗi CSRF không
5. Kiểm tra CSRF token được refresh tự động

**Kết quả mong đợi:**
- CSRF token được refresh tự động
- Không có lỗi CSRF
- Toggle switch hoạt động bình thường
- Không cần refresh trang

### 2.11 Kiểm tra CSRF token cho thao tác xóa
**Mục đích:** Xác minh CSRF token hoạt động đúng cho thao tác xóa danh mục

**Các bước thực hiện:**
1. Truy cập trang quản lý danh mục
2. Chuyển sang màn hình khác (Dashboard, Quản lý truyện)
3. Quay lại trang quản lý danh mục
4. Thử xóa một danh mục
5. Kiểm tra có lỗi CSRF không
6. Mở Developer Tools để xem console log

**Kết quả mong đợi:**
- Không có lỗi CSRF khi xóa danh mục
- Thông báo lỗi rõ ràng nếu CSRF token không hợp lệ
- Xóa thành công nếu CSRF token hợp lệ
- Không cần refresh trang thủ công

---

## 3. MÀN HÌNH QUẢN LÝ TRUYỆN

### 3.1 Kiểm tra hiển thị danh sách truyện
**Mục đích:** Xác minh danh sách truyện hiển thị đầy đủ thông tin

**Các bước thực hiện:**
1. Truy cập URL: `http://localhost/project-story/public/admin/stories`
2. Kiểm tra tiêu đề trang: "Quản lý Truyện"
3. Kiểm tra các nút: "Sọt rác", "Thêm truyện mới"
4. Kiểm tra bảng danh sách truyện:
   - Tên truyện
   - Tác giả
   - Danh mục
   - Trạng thái
   - Lượt xem
   - Ngày tạo
   - Thao tác

**Kết quả mong đợi:**
- Hiển thị đầy đủ danh sách truyện
- Thông tin truyện chi tiết và chính xác
- Giao diện bảng đẹp và responsive

### 3.2 Kiểm tra tìm kiếm truyện
**Mục đích:** Xác minh tìm kiếm truyện theo tên, tác giả

**Các bước thực hiện:**
1. Truy cập trang quản lý truyện
2. Nhập từ khóa tìm kiếm
3. Nhấn "Tìm kiếm"
4. Kiểm tra kết quả trả về
5. Thử tìm kiếm theo tên tác giả

**Kết quả mong đợi:**
- Tìm kiếm chính xác theo tên truyện
- Tìm kiếm chính xác theo tên tác giả
- Hiển thị thông báo khi không tìm thấy

### 3.3 Kiểm tra bộ lọc truyện
**Mục đích:** Xác minh bộ lọc theo danh mục, trạng thái

**Các bước thực hiện:**
1. Truy cập trang quản lý truyện
2. Chọn danh mục trong dropdown
3. Chọn trạng thái trong dropdown
4. Nhấn "Tìm kiếm"
5. Kiểm tra kết quả lọc

**Kết quả mong đợi:**
- Bộ lọc theo danh mục hoạt động
- Bộ lọc theo trạng thái hoạt động
- Kết hợp nhiều bộ lọc chính xác

### 3.4 Kiểm tra thêm truyện mới
**Mục đích:** Xác minh chức năng thêm truyện mới

**Các bước thực hiện:**
1. Nhấn nút "Thêm truyện mới"
2. Nhập thông tin truyện:
   - Tên truyện: "One Piece"
   - Tác giả: "Eiichiro Oda"
   - Danh mục: "Hành động"
   - Mô tả: "Câu chuyện về Luffy và băng hải tặc Mũ Rơm"
   - Upload ảnh bìa
3. Nhấn "Lưu"
4. Kiểm tra truyện xuất hiện trong danh sách

**Kết quả mong đợi:**
- Form thêm truyện hiển thị đầy đủ
- Upload ảnh thành công
- Lưu thành công
- Truyện mới hiển thị trong danh sách

### 3.5 Kiểm tra sửa truyện
**Mục đích:** Xác minh chức năng chỉnh sửa truyện

**Các bước thực hiện:**
1. Tìm truyện cần sửa
2. Nhấn nút "Sửa"
3. Thay đổi thông tin truyện
4. Nhấn "Cập nhật"
5. Kiểm tra thông tin đã được cập nhật

**Kết quả mong đợi:**
- Form sửa hiển thị dữ liệu hiện tại
- Cập nhật thành công
- Thông tin mới hiển thị chính xác

### 3.6 Kiểm tra xóa truyện
**Mục đích:** Xác minh chức năng xóa truyện

**Các bước thực hiện:**
1. Tìm truyện cần xóa
2. Nhấn nút "Xóa"
3. Xác nhận xóa
4. Kiểm tra truyện biến mất khỏi danh sách

**Kết quả mong đợi:**
- Hiển thị popup xác nhận
- Xóa thành công
- Truyện chuyển vào sọt rác

### 3.7 Kiểm tra CSRF token cho thao tác xóa truyện
**Mục đích:** Xác minh CSRF token hoạt động đúng cho thao tác xóa truyện

**Các bước thực hiện:**
1. Truy cập trang quản lý truyện
2. Chuyển sang màn hình khác (Dashboard, Quản lý danh mục)
3. Quay lại trang quản lý truyện
4. Thử xóa một truyện
5. Kiểm tra có lỗi CSRF không

**Kết quả mong đợi:**
- Không có lỗi CSRF khi xóa truyện
- Thông báo lỗi rõ ràng nếu CSRF token không hợp lệ
- Xóa thành công nếu CSRF token hợp lệ

---

## 4. MÀN HÌNH QUẢN LÝ CHƯƠNG

### 4.1 Kiểm tra hiển thị danh sách chương
**Mục đích:** Xác minh danh sách chương hiển thị đầy đủ thông tin

**Các bước thực hiện:**
1. Truy cập URL: `http://localhost/project-story/public/admin/chapters`
2. Kiểm tra tiêu đề trang: "Quản lý Chương"
3. Kiểm tra bảng danh sách chương:
   - Tên chương
   - Truyện
   - Số thứ tự
   - Trạng thái
   - Ngày tạo
   - Thao tác

**Kết quả mong đợi:**
- Hiển thị đầy đủ danh sách chương
- Thông tin chương chi tiết
- Giao diện bảng đẹp

### 4.2 Kiểm tra tìm kiếm chương
**Mục đích:** Xác minh tìm kiếm chương theo tên, truyện

**Các bước thực hiện:**
1. Truy cập trang quản lý chương
2. Nhập từ khóa tìm kiếm
3. Nhấn "Tìm kiếm"
4. Kiểm tra kết quả

**Kết quả mong đợi:**
- Tìm kiếm chính xác
- Hiển thị kết quả phù hợp

### 4.3 Kiểm tra thêm chương mới
**Mục đích:** Xác minh chức năng thêm chương mới

**Các bước thực hiện:**
1. Nhấn nút "Thêm chương mới"
2. Nhập thông tin chương:
   - Tên chương: "Chương 1: Bắt đầu cuộc phiêu lưu"
   - Chọn truyện
   - Số thứ tự: 1
   - Nội dung chương
   - Upload ảnh (nếu có)
3. Nhấn "Lưu"
4. Kiểm tra chương xuất hiện trong danh sách

**Kết quả mong đợi:**
- Form thêm chương hiển thị đầy đủ
- Lưu thành công
- Chương mới hiển thị trong danh sách

### 4.4 Kiểm tra sửa chương
**Mục đích:** Xác minh chức năng chỉnh sửa chương

**Các bước thực hiện:**
1. Tìm chương cần sửa
2. Nhấn nút "Sửa"
3. Thay đổi thông tin chương
4. Nhấn "Cập nhật"
5. Kiểm tra thông tin đã được cập nhật

**Kết quả mong đợi:**
- Form sửa hiển thị dữ liệu hiện tại
- Cập nhật thành công
- Thông tin mới chính xác

### 4.5 Kiểm tra xóa chương
**Mục đích:** Xác minh chức năng xóa chương

**Các bước thực hiện:**
1. Tìm chương cần xóa
2. Nhấn nút "Xóa"
3. Xác nhận xóa
4. Kiểm tra chương biến mất

**Kết quả mong đợi:**
- Hiển thị popup xác nhận
- Xóa thành công
- Chương biến mất khỏi danh sách

### 4.6 Kiểm tra CSRF token cho thao tác xóa chương
**Mục đích:** Xác minh CSRF token hoạt động đúng cho thao tác xóa chương

**Các bước thực hiện:**
1. Truy cập trang quản lý chương
2. Chuyển sang màn hình khác (Dashboard, Quản lý truyện)
3. Quay lại trang quản lý chương
4. Thử xóa một chương
5. Kiểm tra có lỗi CSRF không

**Kết quả mong đợi:**
- Không có lỗi CSRF khi xóa chương
- Thông báo lỗi rõ ràng nếu CSRF token không hợp lệ
- Xóa thành công nếu CSRF token hợp lệ

---

## 5. MÀN HÌNH QUẢN LÝ TÁC GIẢ

### 5.1 Kiểm tra hiển thị danh sách tác giả
**Mục đích:** Xác minh danh sách tác giả hiển thị đầy đủ thông tin

**Các bước thực hiện:**
1. Truy cập URL: `http://localhost/project-story/public/admin/authors`
2. Kiểm tra tiêu đề trang: "Quản lý Tác giả"
3. Kiểm tra bảng danh sách tác giả:
   - Tên tác giả
   - Mô tả
   - Số truyện
   - Trạng thái
   - Ngày tạo
   - Thao tác

**Kết quả mong đợi:**
- Hiển thị đầy đủ danh sách tác giả
- Thông tin tác giả chi tiết
- Giao diện đẹp và responsive

### 5.2 Kiểm tra tìm kiếm tác giả
**Mục đích:** Xác minh tìm kiếm tác giả theo tên

**Các bước thực hiện:**
1. Truy cập trang quản lý tác giả
2. Nhập tên tác giả cần tìm
3. Nhấn "Tìm kiếm"
4. Kiểm tra kết quả

**Kết quả mong đợi:**
- Tìm kiếm chính xác theo tên
- Hiển thị kết quả phù hợp

### 5.3 Kiểm tra thêm tác giả mới
**Mục đích:** Xác minh chức năng thêm tác giả mới

**Các bước thực hiện:**
1. Nhấn nút "Thêm tác giả mới"
2. Nhập thông tin tác giả:
   - Tên tác giả: "Toriyama Akira"
   - Mô tả: "Tác giả của Dragon Ball"
   - Upload ảnh đại diện
3. Nhấn "Lưu"
4. Kiểm tra tác giả xuất hiện trong danh sách

**Kết quả mong đợi:**
- Form thêm tác giả hiển thị đầy đủ
- Upload ảnh thành công
- Lưu thành công
- Tác giả mới hiển thị trong danh sách

### 5.4 Kiểm tra sửa tác giả
**Mục đích:** Xác minh chức năng chỉnh sửa tác giả

**Các bước thực hiện:**
1. Tìm tác giả cần sửa
2. Nhấn nút "Sửa"
3. Thay đổi thông tin tác giả
4. Nhấn "Cập nhật"
5. Kiểm tra thông tin đã được cập nhật

**Kết quả mong đợi:**
- Form sửa hiển thị dữ liệu hiện tại
- Cập nhật thành công
- Thông tin mới chính xác

### 5.5 Kiểm tra xóa tác giả
**Mục đích:** Xác minh chức năng xóa tác giả

**Các bước thực hiện:**
1. Tìm tác giả cần xóa
2. Nhấn nút "Xóa"
3. Xác nhận xóa
4. Kiểm tra tác giả biến mất

**Kết quả mong đợi:**
- Hiển thị popup xác nhận
- Xóa thành công
- Tác giả biến mất khỏi danh sách

### 5.6 Kiểm tra CSRF token cho thao tác xóa tác giả
**Mục đích:** Xác minh CSRF token hoạt động đúng cho thao tác xóa tác giả

**Các bước thực hiện:**
1. Truy cập trang quản lý tác giả
2. Chuyển sang màn hình khác (Dashboard, Quản lý truyện)
3. Quay lại trang quản lý tác giả
4. Thử xóa một tác giả
5. Kiểm tra có lỗi CSRF không

**Kết quả mong đợi:**
- Không có lỗi CSRF khi xóa tác giả
- Thông báo lỗi rõ ràng nếu CSRF token không hợp lệ
- Xóa thành công nếu CSRF token hợp lệ

---

## 6. MÀN HÌNH ĐĂNG NHẬP/ĐĂNG XUẤT

### 6.1 Kiểm tra đăng nhập với thông tin đúng
**Mục đích:** Xác minh đăng nhập thành công với tài khoản hợp lệ

**Các bước thực hiện:**
1. Truy cập URL: `http://localhost/project-story/public/admin/login`
2. Nhập thông tin đăng nhập:
   - Username: "admin"
   - Password: "password123"
3. Nhấn nút "Đăng nhập"
4. Kiểm tra chuyển hướng đến dashboard
5. Kiểm tra hiển thị tên admin trong header

**Kết quả mong đợi:**
- Đăng nhập thành công
- Chuyển hướng đến dashboard
- Hiển thị thông tin admin
- Không có lỗi

### 6.2 Kiểm tra đăng nhập với thông tin sai
**Mục đích:** Xác minh xử lý đăng nhập thất bại

**Các bước thực hiện:**
1. Truy cập trang đăng nhập
2. Nhập thông tin sai:
   - Username: "wrong_user"
   - Password: "wrong_pass"
3. Nhấn nút "Đăng nhập"
4. Kiểm tra thông báo lỗi
5. Kiểm tra vẫn ở trang đăng nhập

**Kết quả mong đợi:**
- Hiển thị thông báo lỗi
- Không chuyển hướng
- Vẫn ở trang đăng nhập

### 6.3 Kiểm tra đăng nhập với trường trống
**Mục đích:** Xác minh validation form đăng nhập

**Các bước thực hiện:**
1. Truy cập trang đăng nhập
2. Để trống username
3. Nhấn nút "Đăng nhập"
4. Kiểm tra thông báo lỗi validation
5. Thử với password trống

**Kết quả mong đợi:**
- Hiển thị thông báo lỗi validation
- Form không submit
- Các trường bắt buộc được highlight

### 6.4 Kiểm tra đăng xuất
**Mục đích:** Xác minh chức năng đăng xuất

**Các bước thực hiện:**
1. Đăng nhập thành công
2. Nhấn nút "Đăng xuất" (thường ở góc phải header)
3. Xác nhận đăng xuất
4. Kiểm tra chuyển hướng về trang đăng nhập
5. Thử truy cập lại dashboard

**Kết quả mong đợi:**
- Đăng xuất thành công
- Chuyển hướng về trang đăng nhập
- Không thể truy cập dashboard khi chưa đăng nhập
- Session được xóa

### 6.5 Kiểm tra bảo mật session
**Mục đích:** Xác minh bảo mật session và timeout

**Các bước thực hiện:**
1. Đăng nhập thành công
2. Để yên trang trong 30 phút (hoặc thời gian timeout)
3. Thử thực hiện thao tác (refresh trang, click link)
4. Kiểm tra có bị đăng xuất tự động không

**Kết quả mong đợi:**
- Session timeout hoạt động
- Tự động đăng xuất khi hết hạn
- Chuyển hướng về trang đăng nhập

### 6.6 Kiểm tra CSRF token cho thao tác xóa user
**Mục đích:** Xác minh CSRF token hoạt động đúng cho thao tác xóa user

**Các bước thực hiện:**
1. Truy cập trang quản lý user
2. Chuyển sang màn hình khác (Dashboard, Quản lý danh mục)
3. Quay lại trang quản lý user
4. Thử xóa một user
5. Kiểm tra có lỗi CSRF không

**Kết quả mong đợi:**
- Không có lỗi CSRF khi xóa user
- Thông báo lỗi rõ ràng nếu CSRF token không hợp lệ
- Xóa thành công nếu CSRF token hợp lệ

---

## 7. KIỂM TRA RESPONSIVE DESIGN

### 7.1 Kiểm tra trên desktop
**Mục đích:** Xác minh giao diện hiển thị tốt trên desktop

**Các bước thực hiện:**
1. Mở trình duyệt với kích thước desktop (1920x1080)
2. Truy cập các trang admin
3. Kiểm tra layout và spacing
4. Kiểm tra các bảng dữ liệu hiển thị đầy đủ

**Kết quả mong đợi:**
- Layout đẹp và cân đối
- Bảng hiển thị đầy đủ cột
- Không có horizontal scroll

### 7.2 Kiểm tra trên tablet
**Mục đích:** Xác minh giao diện responsive trên tablet

**Các bước thực hiện:**
1. Thay đổi kích thước trình duyệt thành tablet (768x1024)
2. Truy cập các trang admin
3. Kiểm tra layout responsive
4. Kiểm tra các bảng có scroll ngang không

**Kết quả mong đợi:**
- Layout responsive tốt
- Bảng có scroll ngang nếu cần
- Các nút và form vẫn dễ sử dụng

### 7.3 Kiểm tra trên mobile
**Mục đích:** Xác minh giao diện responsive trên mobile

**Các bước thực hiện:**
1. Thay đổi kích thước trình duyệt thành mobile (375x667)
2. Truy cập các trang admin
3. Kiểm tra layout mobile
4. Kiểm tra các form và bảng

**Kết quả mong đợi:**
- Layout mobile-friendly
- Các nút đủ lớn để click
- Form dễ nhập liệu
- Bảng có scroll ngang

---

## 8. KIỂM TRA PERFORMANCE

### 8.1 Kiểm tra tốc độ tải trang
**Mục đích:** Xác minh trang tải nhanh

**Các bước thực hiện:**
1. Mở Developer Tools (F12)
2. Truy cập các trang admin
3. Kiểm tra thời gian tải trang
4. Kiểm tra số lượng request

**Kết quả mong đợi:**
- Thời gian tải < 3 giây
- Số lượng request hợp lý
- Không có resource lỗi

### 8.2 Kiểm tra với dữ liệu lớn
**Mục đích:** Xác minh hiệu suất với dữ liệu nhiều

**Các bước thực hiện:**
1. Tạo nhiều dữ liệu test (100+ danh mục, truyện, chương)
2. Truy cập các trang danh sách
3. Kiểm tra thời gian tải
4. Kiểm tra phân trang hoạt động

**Kết quả mong đợi:**
- Trang vẫn tải nhanh
- Phân trang hoạt động tốt
- Không bị timeout

---

## 9. KIỂM TRA BẢO MẬT

### 9.1 Kiểm tra CSRF protection
**Mục đích:** Xác minh bảo vệ CSRF

**Các bước thực hiện:**
1. Đăng nhập admin
2. Mở Developer Tools
3. Tìm các form có CSRF token
4. Kiểm tra token được generate

**Kết quả mong đợi:**
- Tất cả form có CSRF token
- Token được generate ngẫu nhiên
- Form không submit được nếu thiếu token

### 9.2 Kiểm tra XSS protection
**Mục đích:** Xác minh bảo vệ XSS

**Các bước thực hiện:**
1. Thử nhập script vào các trường input
2. Kiểm tra script có được escape không
3. Thử với các ký tự đặc biệt

**Kết quả mong đợi:**
- Script được escape
- Không thực thi JavaScript
- Hiển thị text thuần

### 9.3 Kiểm tra SQL injection
**Mục đích:** Xác minh bảo vệ SQL injection

**Các bước thực hiện:**
1. Thử nhập SQL injection vào tìm kiếm
2. Kiểm tra có lỗi database không
3. Thử với các ký tự đặc biệt SQL

**Kết quả mong đợi:**
- Không có lỗi database
- Input được sanitize
- Không thực thi SQL

---

## 10. KIỂM TRA TÍNH NĂNG NÂNG CAO

### 10.1 Kiểm tra upload file
**Mục đích:** Xác minh chức năng upload ảnh

**Các bước thực hiện:**
1. Truy cập form thêm truyện/tác giả
2. Upload file ảnh
3. Kiểm tra file được lưu đúng
4. Thử upload file không phải ảnh

**Kết quả mong đợi:**
- Upload ảnh thành công
- File được lưu đúng thư mục
- Từ chối file không phải ảnh
- Hiển thị preview ảnh

### 10.2 Kiểm tra pagination
**Mục đích:** Xác minh phân trang hoạt động

**Các bước thực hiện:**
1. Tạo nhiều dữ liệu test
2. Truy cập trang danh sách
3. Kiểm tra hiển thị phân trang
4. Click các trang khác nhau

**Kết quả mong đợi:**
- Hiển thị phân trang khi cần
- Click trang hoạt động
- URL cập nhật với số trang
- Dữ liệu thay đổi theo trang

### 10.3 Kiểm tra bulk operations
**Mục đích:** Xác minh thao tác hàng loạt

**Các bước thực hiện:**
1. Chọn nhiều item trong danh sách
2. Thực hiện thao tác hàng loạt (xóa, thay đổi trạng thái)
3. Kiểm tra tất cả item được xử lý

**Kết quả mong đợi:**
- Checkbox hoạt động
- Thao tác hàng loạt thành công
- Tất cả item được cập nhật
- Thông báo kết quả

---

## GHI CHÚ QUAN TRỌNG

### Cách sử dụng test cases:
1. **Chuẩn bị dữ liệu test:** Tạo tài khoản admin và một số dữ liệu mẫu
2. **Thực hiện tuần tự:** Làm theo đúng thứ tự các bước
3. **Ghi lại kết quả:** Đánh dấu Pass/Fail cho mỗi test case
4. **Báo cáo lỗi:** Ghi lại chi tiết lỗi nếu có
5. **Test trên nhiều trình duyệt:** Chrome, Firefox, Safari, Edge

### Môi trường test:
- **URL gốc:** `http://localhost/project-story/public/`
- **Tài khoản admin:** admin/password123 (hoặc theo cấu hình)
- **Trình duyệt:** Chrome, Firefox, Safari, Edge
- **Kích thước màn hình:** Desktop, Tablet, Mobile

### Tiêu chí đánh giá:
- **Pass:** Chức năng hoạt động đúng như mong đợi
- **Fail:** Chức năng không hoạt động hoặc có lỗi
- **Partial:** Chức năng hoạt động một phần

### Báo cáo test:
- Ghi lại tất cả test case đã thực hiện
- Đánh dấu Pass/Fail/Partial
- Ghi lại chi tiết lỗi nếu có
- Đề xuất cải thiện nếu cần
