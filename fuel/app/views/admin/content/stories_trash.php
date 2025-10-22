<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-trash me-2"></i>Sọt rác - Truyện đã xóa
	</h2>
	<div>
		<a href="<?php echo Uri::base(); ?>admin/stories" class="btn btn-outline-primary me-2">
			<i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
		</a>
		<a href="<?php echo Uri::base(); ?>admin/stories/add" class="btn btn-primary">
			<i class="fas fa-plus me-2"></i>Thêm truyện mới
		</a>
	</div>
</div>

<!-- Search and Filter Bar -->
<div class="card mb-4">
	<div class="card-body">
		<form method="GET" action="<?php echo Uri::base(); ?>admin/stories/trash" class="row g-3">
			<div class="col-md-6">
				<label for="search" class="form-label">Tìm kiếm</label>
				<input type="text" class="form-control" id="search" name="search" 
					   placeholder="Tìm theo tên truyện hoặc tác giả..." 
					   value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
			</div>
			<div class="col-md-4">
				<label for="sort" class="form-label">Sắp xếp</label>
				<select class="form-select" id="sort" name="sort">
					<option value="deleted_at_desc" <?php echo (isset($sort) && $sort === 'deleted_at_desc') ? 'selected' : ''; ?>>Xóa gần nhất</option>
					<option value="deleted_at_asc" <?php echo (isset($sort) && $sort === 'deleted_at_asc') ? 'selected' : ''; ?>>Xóa xa nhất</option>
					<option value="title_asc" <?php echo (isset($sort) && $sort === 'title_asc') ? 'selected' : ''; ?>>Tên A-Z</option>
					<option value="title_desc" <?php echo (isset($sort) && $sort === 'title_desc') ? 'selected' : ''; ?>>Tên Z-A</option>
					<option value="created_at_desc" <?php echo (isset($sort) && $sort === 'created_at_desc') ? 'selected' : ''; ?>>Tạo mới nhất</option>
				</select>
			</div>
			<div class="col-md-2 d-flex align-items-end">
				<button type="submit" class="btn btn-primary me-2">
					<i class="fas fa-search me-1"></i>Tìm kiếm
				</button>
				<a href="<?php echo Uri::base(); ?>admin/stories/trash" class="btn btn-outline-secondary">
					<i class="fas fa-times"></i>
				</a>
			</div>
		</form>
	</div>
</div>

<!-- Stories Table -->
<div class="card">
	<div class="card-body">
		<!-- Success/Error Messages -->
		<?php if (Session::get_flash('success')): ?>
			<div class="alert alert-success">
				<i class="fas fa-check-circle me-2"></i>
				<?php echo Session::get_flash('success'); ?>
			</div>
		<?php endif; ?>

		<?php if (Session::get_flash('error')): ?>
			<div class="alert alert-danger">
				<i class="fas fa-exclamation-triangle me-2"></i>
				<?php echo Session::get_flash('error'); ?>
			</div>
		<?php endif; ?>

		<?php if (isset($stories) && !empty($stories)): ?>
			<!-- Bulk Actions -->
			<div class="row mb-3">
				<div class="col-md-6">
					<div class="d-flex align-items-center">
						<input type="checkbox" id="select-all" class="form-check-input me-2">
						<label for="select-all" class="form-check-label me-3">Chọn tất cả</label>
						<button type="button" class="btn btn-outline-success btn-sm me-2" id="bulk-restore-btn" disabled>
							<i class="fas fa-undo me-1"></i>Khôi phục đã chọn
						</button>
						<button type="button" class="btn btn-outline-danger btn-sm" id="bulk-force-delete-btn" disabled>
							<i class="fas fa-trash-alt me-1"></i>Xóa vĩnh viễn đã chọn
						</button>
					</div>
				</div>
				<div class="col-md-6 text-end">
					<small class="text-muted">
						<span id="selected-count">0</span> mục đã chọn
					</small>
				</div>
			</div>

			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th width="50">
								<input type="checkbox" id="select-all-header" class="form-check-input">
							</th>
							<th>Ảnh bìa</th>
							<th>Tên truyện</th>
							<th>Tác giả</th>
							<th>Trạng thái</th>
							<th>Lượt xem</th>
							<th>Ngày tạo</th>
							<th>Ngày xóa</th>
							<th>Thao tác</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($stories as $story): ?>
						<tr class="table-danger">
							<td>
								<input type="checkbox" class="form-check-input story-checkbox" value="<?php echo $story->id; ?>">
							</td>
							<td>
								<?php if ($story->cover_image && file_exists(DOCROOT . $story->cover_image)): ?>
									<img src="<?php echo Uri::base() . $story->cover_image; ?>" 
										 class="img-thumbnail" 
										 style="width: 60px; height: 80px; object-fit: cover; opacity: 0.6;" 
										 alt="<?php echo htmlspecialchars($story->title); ?>">
								<?php else: ?>
									<div class="bg-light d-flex align-items-center justify-content-center" 
										 style="width: 60px; height: 80px; border: 1px solid #ddd; opacity: 0.6;">
										<i class="fas fa-book text-muted"></i>
									</div>
								<?php endif; ?>
							</td>
							<td>
								<div>
									<h6 class="mb-0 text-muted"><?php echo $story->title; ?></h6>
									<small class="text-muted"><?php echo $story->slug; ?></small>
								</div>
							</td>
							<td>
								<?php if (isset($story->author_name) && $story->author_name): ?>
									<span class="text-muted"><?php echo $story->author_name; ?></span>
								<?php else: ?>
									<span class="text-muted">Chưa xác định</span>
								<?php endif; ?>
							</td>
							<td>
								<span class="badge bg-secondary">
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
							<td class="text-muted"><?php echo number_format(isset($story->views) ? $story->views : 0); ?></td>
							<td class="text-muted"><?php echo date('d/m/Y', strtotime($story->created_at)); ?></td>
							<td class="text-muted">
								<?php if ($story->deleted_at): ?>
									<?php echo date('d/m/Y H:i', strtotime($story->deleted_at)); ?>
								<?php else: ?>
									<span class="text-muted">Chưa xác định</span>
								<?php endif; ?>
							</td>
							<td>
								<div class="btn-group" role="group">
									<form method="POST" action="<?php echo Uri::base(); ?>admin/stories/restore/<?php echo $story->id; ?>" 
										  style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn khôi phục truyện này?')">
										<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
										<button type="submit" class="btn btn-sm btn-outline-success" title="Khôi phục">
											<i class="fas fa-undo"></i>
										</button>
									</form>
									<form method="POST" action="<?php echo Uri::base(); ?>admin/stories/force_delete/<?php echo $story->id; ?>" 
										  style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn XÓA VĨNH VIỄN truyện này? Hành động này không thể hoàn tác!')">
										<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
										<button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa vĩnh viễn">
											<i class="fas fa-trash-alt"></i>
										</button>
									</form>
								</div>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<!-- Pagination -->
			<?php if (isset($total_pages) && $total_pages > 1): ?>
				<nav aria-label="Page navigation" class="mt-4">
					<ul class="pagination justify-content-center">
						<?php 
						// Build query string for pagination
						$query_params = array();
						if (isset($search) && !empty($search)) $query_params['search'] = $search;
						if (isset($sort) && !empty($sort)) $query_params['sort'] = $sort;
						$query_string = !empty($query_params) ? '&' . http_build_query($query_params) : '';
						?>
						
						<?php if (isset($current_page) && $current_page > 1): ?>
							<li class="page-item">
								<a class="page-link" href="<?php echo Uri::base(); ?>admin/stories/trash?page=<?php echo $current_page - 1; ?><?php echo $query_string; ?>">
									<i class="fas fa-chevron-left"></i>
								</a>
							</li>
						<?php endif; ?>

						<?php for ($i = 1; $i <= $total_pages; $i++): ?>
							<?php if ($i == $current_page): ?>
								<li class="page-item active">
									<span class="page-link"><?php echo $i; ?></span>
							</li>
							<?php else: ?>
								<li class="page-item">
									<a class="page-link" href="<?php echo Uri::base(); ?>admin/stories/trash?page=<?php echo $i; ?><?php echo $query_string; ?>"><?php echo $i; ?></a>
								</li>
							<?php endif; ?>
						<?php endfor; ?>

						<?php if (isset($current_page) && $current_page < $total_pages): ?>
							<li class="page-item">
								<a class="page-link" href="<?php echo Uri::base(); ?>admin/stories/trash?page=<?php echo $current_page + 1; ?><?php echo $query_string; ?>">
									<i class="fas fa-chevron-right"></i>
								</a>
							</li>
						<?php endif; ?>
					</ul>
				</nav>
			<?php endif; ?>

		<?php else: ?>
			<div class="text-center py-5">
				<i class="fas fa-trash fa-3x text-muted mb-3"></i>
				<h5 class="text-muted">Sọt rác trống</h5>
				<p class="text-muted">Không có truyện nào đã bị xóa</p>
				<a href="<?php echo Uri::base(); ?>admin/stories" class="btn btn-primary">
					<i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- Bulk Actions Forms -->
<form id="bulk-restore-form" method="POST" action="<?php echo Uri::base(); ?>admin/stories/bulk-restore" style="display: none;">
	<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
	<div id="bulk-restore-ids"></div>
</form>

<form id="bulk-force-delete-form" method="POST" action="<?php echo Uri::base(); ?>admin/stories/bulk-force-delete" style="display: none;">
	<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
	<div id="bulk-force-delete-ids"></div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const selectAllCheckbox = document.getElementById('select-all');
	const selectAllHeaderCheckbox = document.getElementById('select-all-header');
	const storyCheckboxes = document.querySelectorAll('.story-checkbox');
	const bulkRestoreBtn = document.getElementById('bulk-restore-btn');
	const bulkForceDeleteBtn = document.getElementById('bulk-force-delete-btn');
	const selectedCountSpan = document.getElementById('selected-count');
	const bulkRestoreForm = document.getElementById('bulk-restore-form');
	const bulkForceDeleteForm = document.getElementById('bulk-force-delete-form');
	const bulkRestoreIds = document.getElementById('bulk-restore-ids');
	const bulkForceDeleteIds = document.getElementById('bulk-force-delete-ids');

	// Function to update selected count
	function updateSelectedCount() {
		const checkedBoxes = document.querySelectorAll('.story-checkbox:checked');
		const count = checkedBoxes.length;
		selectedCountSpan.textContent = count;
		
		// Enable/disable bulk action buttons
		bulkRestoreBtn.disabled = count === 0;
		bulkForceDeleteBtn.disabled = count === 0;
		
		// Update select all checkboxes
		if (count === 0) {
			selectAllCheckbox.indeterminate = false;
			selectAllCheckbox.checked = false;
			selectAllHeaderCheckbox.indeterminate = false;
			selectAllHeaderCheckbox.checked = false;
		} else if (count === storyCheckboxes.length) {
			selectAllCheckbox.indeterminate = false;
			selectAllCheckbox.checked = true;
			selectAllHeaderCheckbox.indeterminate = false;
			selectAllHeaderCheckbox.checked = true;
		} else {
			selectAllCheckbox.indeterminate = true;
			selectAllHeaderCheckbox.indeterminate = true;
		}
	}

	// Select all functionality
	function selectAll(checked) {
		storyCheckboxes.forEach(checkbox => {
			checkbox.checked = checked;
		});
		updateSelectedCount();
	}

	// Event listeners
	selectAllCheckbox.addEventListener('change', function() {
		selectAll(this.checked);
	});

	selectAllHeaderCheckbox.addEventListener('change', function() {
		selectAll(this.checked);
	});

	storyCheckboxes.forEach(checkbox => {
		checkbox.addEventListener('change', updateSelectedCount);
	});

	// Bulk restore functionality
	bulkRestoreBtn.addEventListener('click', function() {
		const checkedBoxes = document.querySelectorAll('.story-checkbox:checked');
		if (checkedBoxes.length === 0) {
			alert('Vui lòng chọn ít nhất một truyện để khôi phục.');
			return;
		}

		if (confirm(`Bạn có chắc chắn muốn khôi phục ${checkedBoxes.length} truyện đã chọn?`)) {
			// Clear previous IDs
			bulkRestoreIds.innerHTML = '';
			
			// Add selected IDs as hidden inputs
			checkedBoxes.forEach(checkbox => {
				const hiddenInput = document.createElement('input');
				hiddenInput.type = 'hidden';
				hiddenInput.name = 'story_ids[]';
				hiddenInput.value = checkbox.value;
				bulkRestoreIds.appendChild(hiddenInput);
			});
			
			// Submit form
			bulkRestoreForm.submit();
		}
	});

	// Bulk force delete functionality
	bulkForceDeleteBtn.addEventListener('click', function() {
		const checkedBoxes = document.querySelectorAll('.story-checkbox:checked');
		if (checkedBoxes.length === 0) {
			alert('Vui lòng chọn ít nhất một truyện để xóa vĩnh viễn.');
			return;
		}

		if (confirm(`Bạn có chắc chắn muốn XÓA VĨNH VIỄN ${checkedBoxes.length} truyện đã chọn?\n\nHành động này không thể hoàn tác!`)) {
			// Clear previous IDs
			bulkForceDeleteIds.innerHTML = '';
			
			// Add selected IDs as hidden inputs
			checkedBoxes.forEach(checkbox => {
				const hiddenInput = document.createElement('input');
				hiddenInput.type = 'hidden';
				hiddenInput.name = 'story_ids[]';
				hiddenInput.value = checkbox.value;
				bulkForceDeleteIds.appendChild(hiddenInput);
			});
			
			// Submit form
			bulkForceDeleteForm.submit();
		}
	});
});
</script>
