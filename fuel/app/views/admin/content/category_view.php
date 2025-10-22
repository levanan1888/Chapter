<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-tag me-2"></i>Chi tiết danh mục
	</h2>
	<a href="<?php echo Uri::base(); ?>admin/categories" class="btn btn-outline-secondary">
		<i class="fas fa-arrow-left me-2"></i>Quay lại
	</a>
</div>

<?php if (isset($category)): ?>
<div class="row">
	<div class="col-md-4">
		<div class="card">
			<div class="card-body text-center">
				<div class="category-icon mx-auto mb-3" style="width: 80px; height: 80px; background-color: <?php echo $category->color; ?>; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
					<i class="fas fa-tag fa-2x text-white"></i>
				</div>
				<h4><?php echo $category->name; ?></h4>
				<p class="text-muted"><?php echo $category->slug; ?></p>
				<div class="d-flex justify-content-center gap-2">
					<a href="<?php echo Uri::base(); ?>admin/categories/edit/<?php echo $category->id; ?>" class="btn btn-primary">
						<i class="fas fa-edit me-1"></i>Sửa
					</a>
					<form method="POST" action="<?php echo Uri::base(); ?>admin/categories/delete/<?php echo $category->id; ?>" 
						  style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
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
				<?php if ($category->description): ?>
					<div class="mb-3">
						<label class="form-label fw-bold">Mô tả:</label>
						<p><?php echo $category->description; ?></p>
					</div>
				<?php endif; ?>
				
				<div class="row">
					<div class="col-md-6">
						<label class="form-label fw-bold">Màu sắc:</label>
						<div class="d-flex align-items-center">
							<div class="color-preview me-2" style="width: 30px; height: 30px; background-color: <?php echo $category->color; ?>; border-radius: 6px; border: 2px solid #ddd;"></div>
							<span><?php echo $category->color; ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<label class="form-label fw-bold">Số truyện:</label>
						<p class="badge bg-info fs-6"><?php echo isset($story_count) ? $story_count : 0; ?> truyện</p>
					</div>
				</div>
				
				<div class="row mt-3">
					<div class="col-md-6">
						<label class="form-label fw-bold">Ngày tạo:</label>
						<p><?php echo date('d/m/Y H:i', strtotime($category->created_at)); ?></p>
					</div>
					<div class="col-md-6">
						<label class="form-label fw-bold">Cập nhật cuối:</label>
						<p><?php echo date('d/m/Y H:i', strtotime($category->updated_at)); ?></p>
					</div>
				</div>
			</div>
		</div>
		
		<?php if (isset($stories) && !empty($stories)): ?>
		<div class="card mt-4">
			<div class="card-header">
				<h5 class="mb-0">Truyện trong danh mục</h5>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Tên truyện</th>
								<th>Tác giả</th>
								<th>Trạng thái</th>
								<th>Lượt xem</th>
								<th>Ngày tạo</th>
								<th>Thao tác</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($stories as $story): ?>
							<tr>
								<td>
									<h6 class="mb-0"><?php echo $story['title']; ?></h6>
									<small class="text-muted"><?php echo $story['slug']; ?></small>
								</td>
								<td>
									<?php if (isset($story['author_name'])): ?>
										<span><?php echo $story['author_name']; ?></span>
									<?php else: ?>
										<span class="text-muted">Chưa xác định</span>
									<?php endif; ?>
								</td>
								<td>
									<span class="badge bg-<?php 
										switch($story['status']) {
											case 'ongoing': echo 'success'; break;
											case 'completed': echo 'info'; break;
											case 'paused': echo 'warning'; break;
											default: echo 'secondary';
										}
									?>">
										<?php 
										switch($story['status']) {
											case 'ongoing': echo 'Đang cập nhật'; break;
											case 'completed': echo 'Hoàn thành'; break;
											case 'paused': echo 'Tạm dừng'; break;
											default: echo $story['status'];
										}
										?>
									</span>
								</td>
								<td><?php echo number_format($story['views']); ?></td>
								<td><?php echo date('d/m/Y', strtotime($story['created_at'])); ?></td>
								<td>
									<a href="<?php echo Uri::base(); ?>admin/stories/edit/<?php echo $story['id']; ?>" 
									   class="btn btn-sm btn-outline-primary" title="Sửa truyện">
										<i class="fas fa-edit"></i>
									</a>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php else: ?>
		<div class="card mt-4">
			<div class="card-body text-center py-5">
				<i class="fas fa-book fa-3x text-muted mb-3"></i>
				<h5 class="text-muted">Chưa có truyện nào</h5>
				<p class="text-muted">Danh mục này chưa có truyện nào được phân loại</p>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php else: ?>
<div class="alert alert-danger">
	<i class="fas fa-exclamation-triangle me-2"></i>
	Không tìm thấy danh mục
</div>
<?php endif; ?>
