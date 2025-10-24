<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-book me-2"></i>Chi tiết truyện
	</h2>
	<a href="<?php echo Uri::base(); ?>admin/stories" class="btn btn-outline-secondary">
		<i class="fas fa-arrow-left me-2"></i>Quay lại
	</a>
</div>

<?php if (isset($story)): ?>
<div class="row">
	<div class="col-md-4">
		<div class="card">
			<div class="card-body text-center">
				<?php if ($story->cover_image && file_exists(DOCROOT . $story->cover_image)): ?>
					<img src="<?php echo Uri::base() . $story->cover_image; ?>" 
						 class="img-thumbnail mb-3" 
						 style="width: 200px; height: 280px; object-fit: cover;" 
						 alt="<?php echo htmlspecialchars($story->title); ?>">
				<?php else: ?>
					<div class="bg-light d-flex align-items-center justify-content-center mb-3 mx-auto" 
						 style="width: 200px; height: 280px; border: 1px solid #ddd;">
						<i class="fas fa-book fa-3x text-muted"></i>
					</div>
				<?php endif; ?>
				<h4><?php echo $story->title; ?></h4>
				<p class="text-muted"><?php echo $story->slug; ?></p>
				<div class="d-flex justify-content-center gap-2">
					<a href="<?php echo Uri::base(); ?>admin/stories/edit/<?php echo $story->id; ?>" class="btn btn-primary">
						<i class="fas fa-edit me-1"></i>Sửa
					</a>
					<form method="POST" action="<?php echo Uri::base(); ?>admin/stories/delete/<?php echo $story->id; ?>" 
						  style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa truyện này?')">
						<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
						<button type="submit" class="btn btn-danger">
							<i class="fas fa-trash me-1"></i>Xóa
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-md-8">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0">Thông tin chi tiết</h5>
			</div>
			<div class="card-body">
				<?php if ($story->description): ?>
					<div class="mb-3">
						<label class="form-label fw-bold">Mô tả:</label>
						<p><?php echo $story->description; ?></p>
					</div>
				<?php endif; ?>
				
				<div class="row">
					<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label fw-bold">Tác giả:</label>
							<p><?php echo isset($story->author_name) ? $story->author_name : 'Chưa xác định'; ?></p>
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label fw-bold">Trạng thái:</label>
							<span class="badge bg-<?php 
								switch($story->status) {
									case 'ongoing': echo 'success'; break;
									case 'completed': echo 'info'; break;
									case 'paused': echo 'warning'; break;
									default: echo 'secondary';
								}
							?>">
								<?php 
								switch($story->status) {
									case 'ongoing': echo 'Đang cập nhật'; break;
									case 'completed': echo 'Hoàn thành'; break;
									case 'paused': echo 'Tạm dừng'; break;
									default: echo $story->status;
								}
								?>
							</span>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label fw-bold">Lượt xem:</label>
							<p><?php echo number_format(isset($story->views) ? $story->views : 0); ?></p>
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label fw-bold">Số chương:</label>
							<p><?php echo isset($chapter_count) ? $chapter_count : 0; ?></p>
						</div>
					</div>
				</div>

				<div class="mb-3">
					<label class="form-label fw-bold">Danh mục:</label>
					<?php if (isset($story->categories) && !empty($story->categories)): ?>
						<div class="d-flex flex-wrap gap-2">
							<?php foreach ($story->categories as $category): ?>
								<span class="badge bg-secondary"><?php echo $category; ?></span>
							<?php endforeach; ?>
						</div>
					<?php else: ?>
						<p class="text-muted">Chưa phân loại</p>
					<?php endif; ?>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label fw-bold">Ngày tạo:</label>
							<p><?php echo date('d/m/Y H:i', strtotime($story->created_at)); ?></p>
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label fw-bold">Cập nhật cuối:</label>
							<p><?php echo date('d/m/Y H:i', strtotime($story->updated_at)); ?></p>
						</div>
					</div>
				</div>

				<div class="mb-3">
					<label class="form-label fw-bold">Tùy chọn:</label>
					<div class="d-flex gap-3">
						<?php if ($story->is_featured): ?>
							<span class="badge bg-warning">
								<i class="fas fa-star me-1"></i>Truyện nổi bật
							</span>
						<?php endif; ?>
						<?php if ($story->is_hot): ?>
							<span class="badge bg-danger">
								<i class="fas fa-fire me-1"></i>Truyện hot
							</span>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if (isset($chapters) && !empty($chapters)): ?>
<div class="row mt-4">
	<div class="col-12">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h5 class="mb-0">Danh sách chương</h5>
				<a href="<?php echo Uri::base(); ?>admin/chapters/<?php echo $story->id; ?>" class="btn btn-primary btn-sm">
					<i class="fas fa-plus me-1"></i>Thêm chương
				</a>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Chương</th>
								<th>Tên chương</th>
								<th>Số ảnh</th>
								<th>Ngày tạo</th>
								<th>Thao tác</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($chapters as $chapter): ?>
							<tr>
								<td><?php echo $chapter->chapter_number; ?></td>
								<td><?php echo $chapter->title; ?></td>
								<td><?php echo isset($chapter->image_count) ? $chapter->image_count : 0; ?></td>
								<td><?php echo date('d/m/Y', strtotime($chapter->created_at)); ?></td>
								<td>
									<div class="btn-group" role="group">
										<a href="<?php echo Uri::base(); ?>admin/chapters/edit/<?php echo $chapter->id; ?>" 
										   class="btn btn-sm btn-outline-primary" title="Sửa">
											<i class="fas fa-edit"></i>
										</a>
									</div>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<?php else: ?>
<div class="alert alert-danger">
	<i class="fas fa-exclamation-triangle me-2"></i>
	Không tìm thấy truyện.
</div>
<?php endif; ?>
