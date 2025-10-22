<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-plus me-2"></i>Thêm chương mới
		<?php if (isset($story)): ?>
			<small class="text-muted">- <?php echo $story->title; ?></small>
		<?php endif; ?>
	</h2>
	<?php if (isset($story)): ?>
		<a href="<?php echo Uri::base(); ?>admin/chapters/<?php echo $story->id; ?>" class="btn btn-outline-secondary">
			<i class="fas fa-arrow-left me-2"></i>Quay lại
		</a>
	<?php endif; ?>
</div>

<div class="card">
	<div class="card-body">
		<?php if (isset($error_message) && !empty($error_message)): ?>
			<div class="alert alert-danger">
				<i class="fas fa-exclamation-triangle me-2"></i>
				<?php echo $error_message; ?>
			</div>
		<?php endif; ?>

		<form method="POST" action="<?php echo Uri::base(); ?>admin/chapters/add/<?php echo isset($story) ? $story->id : ''; ?>" enctype="multipart/form-data">
			<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
			<div class="row">
				<div class="col-md-6">
					<div class="mb-3">
						<label for="title" class="form-label">Tên chương *</label>
						<input type="text" class="form-control" id="title" name="title" 
							   value="<?php echo isset($form_data['title']) ? $form_data['title'] : ''; ?>" required>
					</div>

					<div class="mb-3">
						<label for="chapter_number" class="form-label">Số chương *</label>
						<input type="number" class="form-control" id="chapter_number" name="chapter_number" 
							   value="<?php echo isset($form_data['chapter_number']) ? $form_data['chapter_number'] : ''; ?>" required>
					</div>
				</div>

				<div class="col-md-6">
					<div class="mb-3">
						<label for="images" class="form-label">Ảnh chương *</label>
						<input type="file" class="form-control" id="images" name="images[]" 
							   accept="image/*" multiple required>
						<div class="form-text">Chọn nhiều ảnh (JPG, PNG, GIF - tối đa 2MB mỗi ảnh)</div>
					</div>
				</div>
			</div>

			<div class="d-flex justify-content-end gap-2">
				<?php if (isset($story)): ?>
					<a href="<?php echo Uri::base(); ?>admin/chapters/<?php echo $story->id; ?>" class="btn btn-secondary">
						<i class="fas fa-times me-2"></i>Hủy
					</a>
				<?php endif; ?>
				<button type="submit" class="btn btn-primary">
					<i class="fas fa-save me-2"></i>Lưu chương
				</button>
			</div>
		</form>
	</div>
</div>
