<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-user-edit me-2"></i>Quản lý Tác giả
	</h2>
	<div>
		<a href="<?php echo Uri::base(); ?>admin/authors/trash" class="btn btn-outline-warning me-2">
			<i class="fas fa-trash me-2"></i>Sọt rác
		</a>
		<a href="<?php echo Uri::base(); ?>admin/authors/add" class="btn btn-primary">
			<i class="fas fa-plus me-2"></i>Thêm tác giả mới
		</a>
	</div>
</div>

<!-- Search and Filter Bar -->
<div class="card mb-4">
	<div class="card-body">
		<form method="GET" action="<?php echo Uri::base(); ?>admin/authors" class="row g-3">
			<div class="col-md-4">
				<label for="search" class="form-label">Tìm kiếm</label>
				<input type="text" class="form-control" id="search" name="search" 
					   placeholder="Tìm theo tên tác giả..." 
					   value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
			</div>
			<div class="col-md-3">
				<label for="status" class="form-label">Trạng thái</label>
				<select class="form-select" id="status" name="status">
					<option value="">Tất cả trạng thái</option>
					<option value="active" <?php echo (isset($status) && $status === 'active') ? 'selected' : ''; ?>>Hoạt động</option>
					<option value="deleted" <?php echo (isset($status) && $status === 'deleted') ? 'selected' : ''; ?>>Đã xóa</option>
				</select>
			</div>
			<div class="col-md-3">
				<label for="sort" class="form-label">Sắp xếp</label>
				<select class="form-select" id="sort" name="sort">
					<option value="created_at_desc" <?php echo (isset($sort) && $sort === 'created_at_desc') ? 'selected' : ''; ?>>Mới nhất</option>
					<option value="created_at_asc" <?php echo (isset($sort) && $sort === 'created_at_asc') ? 'selected' : ''; ?>>Cũ nhất</option>
					<option value="name_asc" <?php echo (isset($sort) && $sort === 'name_asc') ? 'selected' : ''; ?>>Tên A-Z</option>
					<option value="name_desc" <?php echo (isset($sort) && $sort === 'name_desc') ? 'selected' : ''; ?>>Tên Z-A</option>
				</select>
			</div>
			<div class="col-md-2 d-flex align-items-end">
				<button type="submit" class="btn btn-primary me-2">
					<i class="fas fa-search me-1"></i>Tìm kiếm
				</button>
				<a href="<?php echo Uri::base(); ?>admin/authors" class="btn btn-outline-secondary">
					<i class="fas fa-times"></i>
				</a>
			</div>
		</form>
	</div>
</div>

<!-- Authors Table -->
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

		<!-- Messages only; debug removed -->

		<?php if (isset($authors) && !empty($authors)): ?>
			<!-- Bulk Actions -->
			<div class="row mb-3">
				<div class="col-md-6">
					<div class="d-flex align-items-center">
						<input type="checkbox" id="select-all" class="form-check-input me-2">
						<label for="select-all" class="form-check-label me-3">Chọn tất cả</label>
						<button type="button" class="btn btn-outline-danger btn-sm" id="bulk-delete-btn" disabled>
							<i class="fas fa-trash me-1"></i>Xóa đã chọn
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
							<th>Tên tác giả</th>
							<th>Mô tả</th>
							<th>Số truyện</th>
							<th>Ngày tạo</th>
							<th>Thao tác</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($authors as $author): ?>
						<tr>
							<td>
								<input type="checkbox" class="form-check-input author-checkbox" value="<?php echo $author->id; ?>">
							</td>
							<td>
								<div class="d-flex align-items-center">
									<?php if ($author->avatar && file_exists(DOCROOT . $author->avatar)): ?>
										<img src="<?php echo Uri::base() . $author->avatar; ?>" 
											 class="rounded-circle me-3" 
											 style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #e3e6f0;" 
											 alt="<?php echo htmlspecialchars($author->name); ?>">
									<?php else: ?>
										<div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
											<i class="fas fa-user"></i>
										</div>
									<?php endif; ?>
									<div>
										<h6 class="mb-0"><?php echo $author->name; ?></h6>
										<small class="text-muted"><?php echo $author->slug; ?></small>
									</div>
								</div>
							</td>
							<td>
								<?php if ($author->description): ?>
									<p class="mb-0 text-truncate" style="max-width: 200px;" title="<?php echo $author->description; ?>">
										<?php echo $author->description; ?>
									</p>
								<?php else: ?>
									<span class="text-muted">Chưa có mô tả</span>
								<?php endif; ?>
							</td>
							<td>
								<span class="badge bg-info"><?php echo $author->story_count ?? 0; ?> truyện</span>
							</td>
							<td><?php echo date('d/m/Y', strtotime($author->created_at)); ?></td>
							<td>
								<div class="btn-group" role="group">
									<a href="<?php echo Uri::base(); ?>admin/authors/view/<?php echo $author->id; ?>" 
									   class="btn btn-sm btn-outline-info" title="Xem chi tiết">
										<i class="fas fa-eye"></i>
									</a>
									<a href="<?php echo Uri::base(); ?>admin/authors/edit/<?php echo $author->id; ?>" 
									   class="btn btn-sm btn-outline-primary" title="Sửa">
										<i class="fas fa-edit"></i>
									</a>
									<?php if ($author->deleted_at): ?>
										<span class="btn btn-sm btn-outline-secondary" title="Đã xóa">
											<i class="fas fa-trash-alt"></i>
										</span>
									<?php else: ?>
										<form method="POST" action="<?php echo Uri::base(); ?>admin/authors/delete/<?php echo $author->id; ?>" 
											  style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tác giả này?')">
											<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
											<button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
												<i class="fas fa-trash"></i>
											</button>
										</form>
									<?php endif; ?>
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
						if (isset($status) && !empty($status)) $query_params['status'] = $status;
						if (isset($sort) && !empty($sort)) $query_params['sort'] = $sort;
						$query_string = !empty($query_params) ? '&' . http_build_query($query_params) : '';
						?>
						
						<?php if (isset($current_page) && $current_page > 1): ?>
							<li class="page-item">
								<a class="page-link" href="<?php echo Uri::base(); ?>admin/authors?page=<?php echo $current_page - 1; ?><?php echo $query_string; ?>">
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
									<a class="page-link" href="<?php echo Uri::base(); ?>admin/authors?page=<?php echo $i; ?><?php echo $query_string; ?>"><?php echo $i; ?></a>
								</li>
							<?php endif; ?>
						<?php endfor; ?>

						<?php if (isset($current_page) && $current_page < $total_pages): ?>
							<li class="page-item">
								<a class="page-link" href="<?php echo Uri::base(); ?>admin/authors?page=<?php echo $current_page + 1; ?><?php echo $query_string; ?>">
									<i class="fas fa-chevron-right"></i>
								</a>
							</li>
						<?php endif; ?>
					</ul>
				</nav>
			<?php endif; ?>

		<?php else: ?>
			<div class="text-center py-5">
				<i class="fas fa-user-edit fa-3x text-muted mb-3"></i>
				<h5 class="text-muted">Chưa có tác giả nào</h5>
				<p class="text-muted">Hãy thêm tác giả đầu tiên của bạn</p>
				<a href="<?php echo Uri::base(); ?>admin/authors/add" class="btn btn-primary">
					<i class="fas fa-plus me-2"></i>Thêm tác giả mới
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- Bulk Delete Form -->
<form id="bulk-delete-form" method="POST" action="<?php echo Uri::base(); ?>admin/authors/bulk-delete" style="display: none;">
	<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
	<div id="bulk-delete-ids"></div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const selectAllCheckbox = document.getElementById('select-all');
	const selectAllHeaderCheckbox = document.getElementById('select-all-header');
	const authorCheckboxes = document.querySelectorAll('.author-checkbox');
	const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
	const selectedCountSpan = document.getElementById('selected-count');
	const bulkDeleteForm = document.getElementById('bulk-delete-form');
	const bulkDeleteIds = document.getElementById('bulk-delete-ids');

	// Function to update selected count
	function updateSelectedCount() {
		const checkedBoxes = document.querySelectorAll('.author-checkbox:checked');
		const count = checkedBoxes.length;
		selectedCountSpan.textContent = count;
		
		// Enable/disable bulk delete button
		bulkDeleteBtn.disabled = count === 0;
		
		// Update select all checkboxes
		if (count === 0) {
			selectAllCheckbox.indeterminate = false;
			selectAllCheckbox.checked = false;
			selectAllHeaderCheckbox.indeterminate = false;
			selectAllHeaderCheckbox.checked = false;
		} else if (count === authorCheckboxes.length) {
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
		authorCheckboxes.forEach(checkbox => {
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

	authorCheckboxes.forEach(checkbox => {
		checkbox.addEventListener('change', updateSelectedCount);
	});

	// Bulk delete functionality
	bulkDeleteBtn.addEventListener('click', function() {
		const checkedBoxes = document.querySelectorAll('.author-checkbox:checked');
		if (checkedBoxes.length === 0) {
			alert('Vui lòng chọn ít nhất một tác giả để xóa.');
			return;
		}

		if (confirm(`Bạn có chắc chắn muốn xóa ${checkedBoxes.length} tác giả đã chọn?`)) {
			// Clear previous IDs
			bulkDeleteIds.innerHTML = '';
			
			// Add selected IDs as hidden inputs
			checkedBoxes.forEach(checkbox => {
				const hiddenInput = document.createElement('input');
				hiddenInput.type = 'hidden';
				hiddenInput.name = 'author_ids[]';
				hiddenInput.value = checkbox.value;
				bulkDeleteIds.appendChild(hiddenInput);
			});
			
			// Submit form
			bulkDeleteForm.submit();
		}
	});
});
</script>
