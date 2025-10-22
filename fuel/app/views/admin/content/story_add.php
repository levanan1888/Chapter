<?php if (isset($error_message) && !empty($error_message)): ?>
	<div class="alert alert-danger">
		<i class="fas fa-exclamation-triangle me-2"></i>
		<?php echo $error_message; ?>
	</div>
<?php endif; ?>

<?php if (isset($success_message) && !empty($success_message)): ?>
	<div class="alert alert-success">
		<i class="fas fa-check-circle me-2"></i>
		<?php echo $success_message; ?>
	</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
	<h4 class="mb-0">
		<i class="fas fa-plus-circle me-2"></i>Thêm Truyện Mới
	</h4>
	<a href="<?php echo Uri::base(); ?>admin/stories" class="btn btn-secondary">
		<i class="fas fa-arrow-left me-2"></i>Quay lại
	</a>
</div>

<div class="card">
	<div class="card-body">
		<form method="POST" action="<?php echo Uri::base(); ?>admin/stories/add" enctype="multipart/form-data">
			<input type="hidden" name="fuel_csrf_token" value="<?php echo \Security::fetch_token(); ?>">
			<div class="row">
				<div class="col-md-8">
					<div class="mb-3">
						<label for="title" class="form-label">Tên truyện *</label>
						<input type="text" class="form-control" id="title" name="title" 
							   value="<?php echo isset($form_data['title']) ? htmlspecialchars($form_data['title']) : ''; ?>" 
							   required>
					</div>

					<div class="mb-3">
						<label for="description" class="form-label">Mô tả</label>
						<textarea class="form-control" id="description" name="description" rows="5"><?php echo isset($form_data['description']) ? htmlspecialchars($form_data['description']) : ''; ?></textarea>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="author_id" class="form-label">Tác giả *</label>
								<select class="form-select" id="author_id" name="author_id" required>
									<option value="">Chọn tác giả</option>
									<?php if (isset($authors) && !empty($authors)): ?>
										<?php foreach ($authors as $author): ?>
											<option value="<?php echo $author->id; ?>" 
													<?php echo (isset($form_data['author_id']) && $form_data['author_id'] == $author->id) ? 'selected' : ''; ?>>
												<?php echo htmlspecialchars($author->name); ?>
											</option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label for="status" class="form-label">Trạng thái</label>
								<select class="form-select" id="status" name="status">
									<option value="ongoing" <?php echo (isset($form_data['status']) && $form_data['status'] == 'ongoing') ? 'selected' : ''; ?>>Đang cập nhật</option>
									<option value="completed" <?php echo (isset($form_data['status']) && $form_data['status'] == 'completed') ? 'selected' : ''; ?>>Hoàn thành</option>
									<option value="paused" <?php echo (isset($form_data['status']) && $form_data['status'] == 'paused') ? 'selected' : ''; ?>>Tạm dừng</option>
								</select>
							</div>
						</div>
					</div>

					<div class="mb-3">
						<label for="categories" class="form-label">Danh mục</label>
						<div class="row">
							<?php if (isset($categories) && !empty($categories)): ?>
								<?php foreach ($categories as $category): ?>
									<div class="col-md-4">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" name="category_ids[]" value="<?php echo $category->id; ?>" 
												   id="category_<?php echo $category->id; ?>"
												   <?php echo (isset($form_data['category_ids']) && in_array($category->id, $form_data['category_ids'])) ? 'checked' : ''; ?>>
											<label class="form-check-label" for="category_<?php echo $category->id; ?>">
												<span class="badge" style="background-color: <?php echo $category->color; ?>;">
													<?php echo $category->name; ?>
												</span>
											</label>
										</div>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="mb-3">
						<label for="cover_image" class="form-label">Ảnh bìa</label>
						<input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
						<div class="form-text">Hỗ trợ: JPG, PNG, GIF (tối đa 2MB)</div>
					</div>

					<div class="mb-3">
						<label class="form-label">Tùy chọn</label>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="is_featured" value="1" 
								   id="is_featured" <?php echo (isset($form_data['is_featured']) && $form_data['is_featured']) ? 'checked' : ''; ?>>
							<label class="form-check-label" for="is_featured">
								Truyện nổi bật
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="is_hot" value="1" 
								   id="is_hot" <?php echo (isset($form_data['is_hot']) && $form_data['is_hot']) ? 'checked' : ''; ?>>
							<label class="form-check-label" for="is_hot">
								Truyện hot
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="is_visible" value="1" 
								   id="is_visible" <?php echo (isset($form_data['is_visible']) && $form_data['is_visible']) ? 'checked' : ''; ?>>
							<label class="form-check-label" for="is_visible">
								Hiển thị truyện
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="d-flex justify-content-end gap-2">
				<a href="<?php echo Uri::base(); ?>admin/stories" class="btn btn-secondary">
					<i class="fas fa-times me-2"></i>Hủy
				</a>
				<button type="submit" class="btn btn-primary">
					<i class="fas fa-save me-2"></i>Lưu truyện
				</button>
			</div>
		</form>
	</div>
</div>

<!-- Đã bỏ toàn bộ styles/scripts quản lý ảnh chương đầu tiên -->