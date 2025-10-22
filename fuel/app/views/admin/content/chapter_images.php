<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-images me-2"></i>Quản lý ảnh chương
		<?php if (isset($chapter)): ?>
			<small class="text-muted">- <?php echo $chapter->title; ?></small>
		<?php endif; ?>
	</h2>
	<?php if (isset($story)): ?>
		<a href="<?php echo Uri::base(); ?>admin/chapters/<?php echo $story->id; ?>" class="btn btn-outline-secondary">
			<i class="fas fa-arrow-left me-2"></i>Quay lại
		</a>
	<?php endif; ?>
</div>

<!-- Upload New Images -->
<div class="card mb-4">
	<div class="card-header">
		<h5 class="mb-0">Thêm ảnh mới</h5>
	</div>
	<div class="card-body">
		<form method="POST" action="<?php echo Uri::base(); ?>../../admin/chapters/upload/<?php echo isset($chapter) ? $chapter->id : ''; ?>" enctype="multipart/form-data">
			<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
			<div class="row">
				<div class="col-md-8">
					<div class="mb-3">
						<label for="images" class="form-label">Chọn ảnh</label>
						<input type="file" class="form-control" id="images" name="images[]" 
							   accept="image/*" multiple required>
						<div class="form-text">Chọn nhiều ảnh (JPG, PNG, GIF - tối đa 2MB mỗi ảnh)</div>
					</div>
				</div>
				<div class="col-md-4">
					<label class="form-label">&nbsp;</label>
					<div class="d-grid">
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-upload me-2"></i>Tải lên
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- Images Grid -->
<div class="card">
	<div class="card-header">
		<h5 class="mb-0">Ảnh hiện tại</h5>
	</div>
	<div class="card-body">
		<?php if (isset($images) && !empty($images)): ?>
			<div class="row">
				<?php foreach ($images as $index => $image): ?>
				<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
					<div class="card">
						<img src="<?php echo Uri::base() . $image; ?>" class="card-img-top" 
							 style="height: 200px; object-fit: cover;" alt="Page <?php echo $index + 1; ?>">
						<div class="card-body">
							<h6 class="card-title">Trang <?php echo $index + 1; ?></h6>
							<div class="btn-group w-100" role="group">
								<a href="<?php echo Uri::base() . $image; ?>" target="_blank" 
								   class="btn btn-sm btn-outline-primary">
									<i class="fas fa-eye"></i>
								</a>
								<a href="<?php echo Uri::base(); ?>admin/chapters/delete-image/<?php echo isset($chapter) ? $chapter->id : ''; ?>/<?php echo $index; ?>" 
								   class="btn btn-sm btn-outline-danger" 
								   onclick="return confirm('Bạn có chắc chắn muốn xóa ảnh này?')">
									<i class="fas fa-trash"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		<?php else: ?>
			<div class="text-center py-5">
				<i class="fas fa-images fa-3x text-muted mb-3"></i>
				<h5 class="text-muted">Chưa có ảnh nào</h5>
				<p class="text-muted">Hãy tải lên ảnh đầu tiên cho chương này</p>
			</div>
		<?php endif; ?>
	</div>
</div>
