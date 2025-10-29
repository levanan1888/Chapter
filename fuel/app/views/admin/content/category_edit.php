<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-edit me-2"></i>Sửa danh mục
	</h2>
	<a href="<?php echo Uri::base(); ?>admin/categories" class="btn btn-outline-secondary">
		<i class="fas fa-arrow-left me-2"></i>Quay lại
	</a>
</div>

<div class="card">
	<div class="card-body">
		<?php if (isset($error_message) && !empty($error_message)): ?>
			<div class="alert alert-danger">
				<i class="fas fa-exclamation-triangle me-2"></i>
				<?php echo $error_message; ?>
			</div>
		<?php endif; ?>

		<form method="POST" action="<?php echo Uri::base(); ?>admin/categories/edit/<?php echo isset($category) ? $category->id : ''; ?>">
			<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
			<div class="row">
				<div class="col-md-8">
					<div class="mb-3">
						<label for="name" class="form-label">
							Tên danh mục <span class="text-danger">*</span>
						</label>
						<input type="text" class="form-control" id="name" name="name" 
							   value="<?php echo isset($category) ? $category->name : ''; ?>" required>
						<div class="form-text">Tên danh mục là bắt buộc và phải có ít nhất 2 ký tự</div>
					</div>

					<div class="mb-3">
						<label for="slug" class="form-label">Slug</label>
						<input type="text" class="form-control" id="slug" name="slug" 
							   value="<?php echo isset($category) ? $category->slug : ''; ?>" 
							   placeholder="Tự động tạo từ tên danh mục">
						<small class="form-text text-muted">Slug sẽ được tự động cập nhật khi bạn thay đổi tên danh mục</small>
					</div>

					<div class="mb-3">
						<label for="description" class="form-label">Mô tả</label>
						<textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($category) ? $category->description : ''; ?></textarea>
					</div>
				</div>

				<div class="col-md-4">
					<div class="mb-3">
						<label for="color" class="form-label">Màu sắc</label>
						<input type="color" class="form-control form-control-color" id="color" name="color" 
							   value="<?php echo isset($category) ? $category->color : '#007bff'; ?>">
					</div>

					<div class="mb-3">
						<label for="sort_order" class="form-label">Thứ tự sắp xếp</label>
						<input type="number" class="form-control" id="sort_order" name="sort_order" 
							   value="<?php echo isset($category) ? $category->sort_order : '0'; ?>">
					</div>

					<div class="mb-3">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
								   <?php echo (isset($category) && $category->is_active == 1) ? 'checked' : ''; ?>>
							<label class="form-check-label" for="is_active">
								Hoạt động
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="d-flex justify-content-end gap-2">
				<a href="<?php echo Uri::base(); ?>admin/categories" class="btn btn-secondary">
					<i class="fas fa-times me-2"></i>Hủy
				</a>
				<button type="submit" class="btn btn-primary">
					<i class="fas fa-save me-2"></i>Cập nhật danh mục
				</button>
			</div>
		</form>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    // Function to create slug from name
    function createSlug(text) {
        return text
            .toLowerCase()
            .replace(/á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/g, 'a')
            .replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/g, 'e')
            .replace(/í|ì|ỉ|ĩ|ị/g, 'i')
            .replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/g, 'o')
            .replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/g, 'u')
            .replace(/ý|ỳ|ỷ|ỹ|ỵ/g, 'y')
            .replace(/đ/g, 'd')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
    
    // Auto-update slug when name changes
    nameInput.addEventListener('input', function() {
        const slug = createSlug(this.value);
        slugInput.value = slug;
    });
    
    // Also update on page load if name exists but slug is empty
    if (nameInput.value && !slugInput.value) {
        slugInput.value = createSlug(nameInput.value);
    }
});
</script>
