<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-edit me-2"></i>Sửa tác giả
	</h2>
	<a href="<?php echo Uri::base(); ?>admin/authors" class="btn btn-outline-secondary">
		<i class="fas fa-arrow-left me-2"></i>Quay lại
	</a>
</div>

<div class="card">
	<div class="card-body">
		<?php if (isset($success_message) && !empty($success_message)): ?>
			<div class="alert alert-success">
				<i class="fas fa-check-circle me-2"></i>
				<?php echo $success_message; ?>
			</div>
		<?php endif; ?>

		<?php if (isset($error_message) && !empty($error_message)): ?>
			<div class="alert alert-danger">
				<i class="fas fa-exclamation-triangle me-2"></i>
				<?php echo $error_message; ?>
			</div>
		<?php endif; ?>

		<form method="POST" action="<?php echo Uri::base(); ?>admin/authors/edit/<?php echo isset($author) ? $author->id : ''; ?>" enctype="multipart/form-data">
			<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
			
			<div class="row">
				<div class="col-md-8">
					<div class="mb-3">
						<label for="name" class="form-label">
							Tên tác giả <span class="text-danger">*</span>
						</label>
						<input type="text" class="form-control" id="name" name="name" 
							   value="<?php echo isset($author) ? htmlspecialchars($author->name) : ''; ?>" required>
						<div class="form-text">Tên tác giả là bắt buộc và phải có ít nhất 2 ký tự</div>
					</div>

					<div class="mb-3">
						<label for="slug" class="form-label">Slug (URL)</label>
						<input type="text" class="form-control" id="slug" name="slug" 
							   value="<?php echo isset($author) ? htmlspecialchars($author->slug) : ''; ?>"
							   placeholder="Tự động tạo từ tên tác giả" readonly>
						<div class="form-text">Slug sẽ được tự động tạo từ tên tác giả khi cập nhật</div>
					</div>

					<div class="mb-3">
						<label for="description" class="form-label">Mô tả</label>
						<textarea class="form-control" id="description" name="description" rows="5"><?php echo isset($author) ? htmlspecialchars($author->description) : ''; ?></textarea>
						<div class="form-text">Mô tả chỉ được chứa chữ cái và khoảng trắng</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="mb-3">
						<label for="avatar" class="form-label">Ảnh đại diện</label>
						<input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
						<div class="form-text">Hỗ trợ: JPG, PNG, GIF (tối đa 2MB)</div>
						<?php if (isset($author) && !empty($author->avatar)): ?>
							<div class="mt-2">
								<img src="<?php echo Uri::base() . $author->avatar; ?>" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
								<div class="mt-1">
									<small class="text-muted">Ảnh hiện tại</small>
								</div>
							</div>
						<?php endif; ?>
					</div>

					<div class="mb-3">
						<label class="form-label">Trạng thái</label>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="is_active" value="1" 
								   id="is_active" <?php echo (isset($author) && $author->is_active) ? 'checked' : ''; ?>>
							<label class="form-check-label" for="is_active">
								Hoạt động
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="d-flex justify-content-end gap-2">
				<a href="<?php echo Uri::base(); ?>admin/authors" class="btn btn-secondary">
					<i class="fas fa-times me-2"></i>Hủy
				</a>
				<button type="submit" class="btn btn-primary">
					<i class="fas fa-save me-2"></i>Cập nhật tác giả
				</button>
			</div>
		</form>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const nameInput = document.getElementById('name');
	const slugInput = document.getElementById('slug');
	
	if (nameInput && slugInput) {
		nameInput.addEventListener('input', function() {
			// Tạo slug từ tên
			let slug = this.value.toLowerCase()
				.replace(/[àáạảãâầấậẩẫăằắặẳẵ]/g, 'a')
				.replace(/[èéẹẻẽêềếệểễ]/g, 'e')
				.replace(/[ìíịỉĩ]/g, 'i')
				.replace(/[òóọỏõôồốộổỗơờớợởỡ]/g, 'o')
				.replace(/[ùúụủũưừứựửữ]/g, 'u')
				.replace(/[ỳýỵỷỹ]/g, 'y')
				.replace(/đ/g, 'd')
				.replace(/[^a-z0-9\s-]/g, '')
				.replace(/\s+/g, '-')
				.replace(/-+/g, '-')
				.replace(/^-|-$/g, '');
			
			slugInput.value = slug;
		});
	}
});
</script>
