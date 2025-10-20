# CODING RULES - BẮT BUỘC ĐỌC TRƯỚC KHI CODE

## ⚠️ QUY TRÌNH BẮT BUỘC:
1. **ĐỌC RULE NÀY TRƯỚC** khi làm bất kỳ task nào
2. **KIỂM TRA CODE HIỆN TẠI** theo rule
3. **CHỈ SỬA KHI ĐƯỢC YÊU CẦU CỤ THỂ**
4. **KHÔNG ĐƯA RA KHUYẾN NGHỊ** nếu không được hỏi

## 📋 CODING STANDARDS:

### File & Encoding:
- ★ Encoding: UTF-8 without BOM
- ■ Tên file viết bằng **chữ thường**

### Indentation:
- ■ Thụt lề dùng **tab** (tương đương 4 spaces)

### Naming Convention:
- ★ Biến boolean dùng prefix: `is_xxx`, `has_xxx`, `can_xxx`
- ★ Không đặt tên biến vô nghĩa: `a`, `b`, `c` hoặc `data1`, `data2`
- ★ Biến **snake_case**
- ★ Biến truyền từ Controller sang View phải gói trong `$data`

### Array:
- ★ Dùng `[]` thay vì `array()`

### Class & Method:
- ■ `{` ở dòng mới
- ★ Mỗi method cần PHPDoc
- ★ Phải khai báo type cho **parameter** và **return**

### Database & SQL:
- ★ Luôn dùng Raw SQL, không dùng Query Builder
- ★ Bắt buộc placeholder + bind parameter (`:id`, `:name`)
- ★ INSERT/UPDATE/DELETE phải trong **try block**

### HTML & Security:
- ■ Không dùng `<?= ?>`, chỉ dùng `<?php echo ?>`
- ★ BẮT BUỘC có CSRF protection trong forms
- ★ Escape output với `htmlentities()`

## 🚫 KHÔNG ĐƯỢC LÀM:
- Đưa ra khuyến nghị sửa code khi chưa được yêu cầu
- Sửa code mà không hỏi trước
- Bỏ qua bước kiểm tra rule

## ✅ QUY TRÌNH ĐÚNG:
1. Đọc rule này
2. Kiểm tra code hiện tại
3. Báo cáo kết quả
4. Chờ chỉ dẫn cụ thể
5. Thực hiện theo yêu cầu
