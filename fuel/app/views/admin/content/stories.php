<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-book me-2"></i>Quản lý Truyện
	</h2>
	<a href="<?php echo Uri::base(); ?>admin/stories/add" class="btn btn-primary">
		<i class="fas fa-plus me-2"></i>Thêm truyện mới
	</a>
</div>

<!-- Filter and Search -->
<div class="card mb-4">
	<div class="card-body">
		<form method="GET" action="<?php echo Uri::base(); ?>admin/stories" class="row g-3">
			<div class="col-md-4">
				<label for="search" class="form-label">Tìm kiếm</label>
				<input type="text" class="form-control" id="search" name="search" 
					   value="<?php echo isset($search) ? $search : ''; ?>" 
					   placeholder="Tìm theo tên truyện hoặc tác giả">
			</div>
			<div class="col-md-3">
				<label for="category" class="form-label">Danh mục</label>
				<select class="form-select" id="category" name="category">
					<option value="">Tất cả danh mục</option>
					<?php if (isset($categories) && !empty($categories)): ?>
						<?php foreach ($categories as $category): ?>
							<option value="<?php echo $category->id; ?>" 
									<?php echo (isset($selected_category) && $selected_category == $category->id) ? 'selected' : ''; ?>>
								<?php echo $category->name; ?>
							</option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
			<div class="col-md-3">
				<label for="status" class="form-label">Trạng thái</label>
				<select class="form-select" id="status" name="status">
					<option value="">Tất cả trạng thái</option>
					<option value="ongoing" <?php echo (isset($selected_status) && $selected_status == 'ongoing') ? 'selected' : ''; ?>>Đang cập nhật</option>
					<option value="completed" <?php echo (isset($selected_status) && $selected_status == 'completed') ? 'selected' : ''; ?>>Hoàn thành</option>
					<option value="paused" <?php echo (isset($selected_status) && $selected_status == 'paused') ? 'selected' : ''; ?>>Tạm dừng</option>
				</select>
			</div>
			<div class="col-md-2">
				<label class="form-label">&nbsp;</label>
				<div class="d-grid">
					<button type="submit" class="btn btn-outline-primary">
						<i class="fas fa-search me-1"></i>Tìm kiếm
					</button>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- Stories Table -->
<div class="card">
	<div class="card-body">
		<?php if (isset($stories) && !empty($stories)): ?>
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>
								<input type="checkbox" id="selectAll" class="form-check-input">
							</th>
							<th>Ảnh bìa</th>
							<th>Tên truyện</th>
							<th>Tác giả</th>
							<th>Danh mục</th>
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
								<input type="checkbox" class="form-check-input story-checkbox" value="<?php echo $story->id; ?>">
							</td>
							<td>
								<img src="<?php echo $story->cover_image ? Uri::base() . $story->cover_image : 'https://via.placeholder.com/60x80'; ?>" 
									 class="img-thumbnail" style="width: 60px; height: 80px; object-fit: cover;" 
									 alt="<?php echo $story->title; ?>">
							</td>
							<td>
								<div>
									<h6 class="mb-1"><?php echo $story->title; ?></h6>
									<small class="text-muted"><?php echo $story->slug; ?></small>
								</div>
							</td>
							<td><?php echo $story->author_name ?? 'Unknown'; ?></td>
							<td>
								<span class="text-muted">Chưa có</span>
							</td>
							<td>
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
							</td>
							<td><?php echo number_format(isset($story->views) ? $story->views : 0); ?></td>
							<td><?php echo date('d/m/Y', strtotime($story->created_at)); ?></td>
							<td>
								<div class="btn-group" role="group">
									<a href="<?php echo Uri::base(); ?>admin/stories/edit/<?php echo $story->id; ?>" 
									   class="btn btn-sm btn-outline-primary" title="Sửa">
										<i class="fas fa-edit"></i>
									</a>
									<a href="<?php echo Uri::base(); ?>admin/chapters/<?php echo $story->id; ?>" 
									   class="btn btn-sm btn-outline-info" title="Quản lý chương">
										<i class="fas fa-file-alt"></i>
									</a>
									<a href="<?php echo Uri::base(); ?>admin/stories/delete/<?php echo $story->id; ?>" 
									   class="btn btn-sm btn-outline-danger" title="Xóa" 
									   onclick="return confirm('Bạn có chắc chắn muốn xóa truyện này?')">
										<i class="fas fa-trash"></i>
									</a>
								</div>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<!-- Bulk Actions -->
			<div class="mt-3">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<button type="button" class="btn btn-outline-danger btn-sm" id="bulkDelete" disabled>
							<i class="fas fa-trash me-1"></i>Xóa đã chọn
						</button>
						<button type="button" class="btn btn-outline-warning btn-sm" id="bulkUpdateStatus" disabled>
							<i class="fas fa-edit me-1"></i>Cập nhật trạng thái
						</button>
					</div>
					<div>
						<small class="text-muted">
							Hiển thị <?php echo count($stories); ?> trong tổng số <?php echo isset($total_stories) ? $total_stories : 0; ?> truyện
						</small>
					</div>
				</div>
			</div>

			<!-- Pagination -->
			<?php if (isset($pagination) && $pagination): ?>
				<nav aria-label="Page navigation" class="mt-4">
					<?php echo $pagination; ?>
				</nav>
			<?php endif; ?>

		<?php else: ?>
			<div class="text-center py-5">
				<i class="fas fa-book fa-3x text-muted mb-3"></i>
				<h5 class="text-muted">Chưa có truyện nào</h5>
				<p class="text-muted">Hãy thêm truyện đầu tiên của bạn</p>
				<a href="<?php echo Uri::base(); ?>admin/stories/add" class="btn btn-primary">
					<i class="fas fa-plus me-2"></i>Thêm truyện mới
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>

<script>
// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
	const checkboxes = document.querySelectorAll('.story-checkbox');
	checkboxes.forEach(checkbox => {
		checkbox.checked = this.checked;
	});
	updateBulkButtons();
});

// Individual checkbox change
document.querySelectorAll('.story-checkbox').forEach(checkbox => {
	checkbox.addEventListener('change', updateBulkButtons);
});

function updateBulkButtons() {
	const checkedBoxes = document.querySelectorAll('.story-checkbox:checked');
	const bulkDelete = document.getElementById('bulkDelete');
	const bulkUpdateStatus = document.getElementById('bulkUpdateStatus');
	
	if (checkedBoxes.length > 0) {
		bulkDelete.disabled = false;
		bulkUpdateStatus.disabled = false;
	} else {
		bulkDelete.disabled = true;
		bulkUpdateStatus.disabled = true;
	}
}

// Bulk delete
document.getElementById('bulkDelete').addEventListener('click', function() {
	const checkedBoxes = document.querySelectorAll('.story-checkbox:checked');
	const ids = Array.from(checkedBoxes).map(cb => cb.value);
	
	if (ids.length === 0) return;
	
	if (confirm(`Bạn có chắc chắn muốn xóa ${ids.length} truyện đã chọn?`)) {
		// Implement bulk delete logic here
		console.log('Bulk delete IDs:', ids);
	}
});

// Bulk update status
document.getElementById('bulkUpdateStatus').addEventListener('click', function() {
	const checkedBoxes = document.querySelectorAll('.story-checkbox:checked');
	const ids = Array.from(checkedBoxes).map(cb => cb.value);
	
	if (ids.length === 0) return;
	
	const newStatus = prompt('Nhập trạng thái mới (ongoing/completed/paused):');
	if (newStatus && ['ongoing', 'completed', 'paused'].includes(newStatus)) {
		// Implement bulk update status logic here
		console.log('Bulk update status:', ids, 'to', newStatus);
	}
});
</script>
