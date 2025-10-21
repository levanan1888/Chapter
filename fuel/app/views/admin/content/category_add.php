<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-plus me-2"></i>Thêm danh mục mới
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

		<form method="POST" action="<?php echo Uri::base(); ?>admin/categories/add">
			<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
			<div class="row">
				<div class="col-md-8">
					<div class="mb-3">
						<label for="name" class="form-label">Tên danh mục *</label>
						<input type="text" class="form-control" id="name" name="name" 
							   value="<?php echo isset($form_data['name']) ? $form_data['name'] : ''; ?>" required>
					</div>

					<div class="mb-3">
						<label for="description" class="form-label">Mô tả</label>
						<textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($form_data['description']) ? $form_data['description'] : ''; ?></textarea>
					</div>
				</div>

				<div class="col-md-4">
					<div class="mb-3">
						<label for="color" class="form-label">Màu sắc</label>
						<input type="color" class="form-control form-control-color" id="color" name="color" 
							   value="<?php echo isset($form_data['color']) ? $form_data['color'] : '#007bff'; ?>">
					</div>

					<div class="mb-3">
						<label for="sort_order" class="form-label">Thứ tự sắp xếp</label>
						<input type="number" class="form-control" id="sort_order" name="sort_order" 
							   value="<?php echo isset($form_data['sort_order']) ? $form_data['sort_order'] : '0'; ?>">
					</div>
				</div>
			</div>

			<div class="d-flex justify-content-end gap-2">
				<a href="<?php echo Uri::base(); ?>admin/categories" class="btn btn-secondary">
					<i class="fas fa-times me-2"></i>Hủy
				</a>
				<button type="submit" class="btn btn-primary">
					<i class="fas fa-save me-2"></i>Lưu danh mục
				</button>
			</div>
		</form>
	</div>
</div>
