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
					<option value="inactive" <?php echo (isset($status) && $status === 'inactive') ? 'selected' : ''; ?>>Không hoạt động</option>
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
						<span id="selected-count">0</span> mục đã chọn / Tổng: <strong><?php echo isset($total_authors) ? number_format($total_authors) : 0; ?></strong> tác giả
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
							<th>Trạng thái</th>
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
							<td>
								<?php if ($author->is_active): ?>
									<span class="badge bg-success">Hoạt động</span>
								<?php else: ?>
									<span class="badge bg-secondary">Không hoạt động</span>
								<?php endif; ?>
							</td>
							<td><?php echo date('d/m/Y', strtotime($author->created_at)); ?></td>
							<td>
								<div class="btn-group" role="group">
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


<script>
// Initialize CSRF token on page load
window.currentCsrfToken = '<?php echo \Security::fetch_token(); ?>';
console.log('Initial CSRF token:', window.currentCsrfToken);

// Update CSRF token in meta tag and hidden form when page loads
document.addEventListener('DOMContentLoaded', function() {
	const metaToken = document.querySelector('meta[name="csrf-token"]');
	const hiddenTokens = document.querySelectorAll('input[name="<?php echo \Config::get('security.csrf_token_key'); ?>"]');
	
	if (metaToken) {
		metaToken.setAttribute('content', window.currentCsrfToken);
	}
	hiddenTokens.forEach(token => {
		token.value = window.currentCsrfToken;
	});
});

// Function to refresh CSRF token
function refreshCsrfToken() {
	fetch('<?php echo Uri::base(); ?>admin/authors', {
		method: 'GET',
		headers: {
			'X-Requested-With': 'XMLHttpRequest'
		}
	})
	.then(response => response.text())
	.then(html => {
		const parser = new DOMParser();
		const doc = parser.parseFromString(html, 'text/html');
		const newToken = doc.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
						doc.querySelector('input[name="<?php echo \Config::get('security.csrf_token_key'); ?>"]')?.value;
		if (newToken) {
			window.currentCsrfToken = newToken;
			
			// Update meta tag and hidden forms with new token
			const metaToken = document.querySelector('meta[name="csrf-token"]');
			const hiddenTokens = document.querySelectorAll('input[name="<?php echo \Config::get('security.csrf_token_key'); ?>"]');
			
			if (metaToken) {
				metaToken.setAttribute('content', newToken);
			}
			hiddenTokens.forEach(token => {
				token.value = newToken;
			});
			
			console.log('CSRF token refreshed:', newToken);
		}
	})
	.catch(err => {
		console.error('Failed to refresh CSRF token:', err);
	});
}

// Refresh CSRF token when page becomes visible (user returns from another page)
document.addEventListener('visibilitychange', function() {
	if (!document.hidden) {
		console.log('Page became visible, refreshing CSRF token...');
		refreshCsrfToken();
	}
});

// Also refresh token when page gains focus
window.addEventListener('focus', function() {
	console.log('Window gained focus, refreshing CSRF token...');
	refreshCsrfToken();
});

// Function to show alert messages
function showAlert(type, message) {
	// Remove existing alerts
	const existingAlerts = document.querySelectorAll('.alert');
	existingAlerts.forEach(alert => alert.remove());
	
	// Create new alert
	const alertDiv = document.createElement('div');
	alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
	alertDiv.innerHTML = `
		<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
		${message}
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	`;
	
	// Insert at the top of card-body
	const cardBody = document.querySelector('.card-body');
	cardBody.insertBefore(alertDiv, cardBody.firstChild);
	
	// Auto remove after 5 seconds
	setTimeout(() => {
		if (alertDiv.parentNode) {
			alertDiv.remove();
		}
	}, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
	const selectAllCheckbox = document.getElementById('select-all');
	const selectAllHeaderCheckbox = document.getElementById('select-all-header');
	const authorCheckboxes = document.querySelectorAll('.author-checkbox');
	const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
	const selectedCountSpan = document.getElementById('selected-count');

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
			showAlert('warning', 'Vui lòng chọn ít nhất một tác giả để xóa.');
			return;
		}

		if (confirm(`Bạn có chắc chắn muốn xóa ${checkedBoxes.length} tác giả đã chọn?`)) {
			// Use stored token if available, otherwise use initial token
			const csrfToken = window.currentCsrfToken || '<?php echo \Security::fetch_token(); ?>';
			console.log('Sending CSRF token:', csrfToken);
			
			const formData = new FormData();
			checkedBoxes.forEach(checkbox => {
				formData.append('author_ids[]', checkbox.value);
			});
			formData.append('<?php echo \Config::get("security.csrf_token_key"); ?>', csrfToken);
			
			fetch('<?php echo Uri::base(); ?>admin/authors/bulk-delete', {
				method: 'POST',
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				},
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					// Store new CSRF token for next request
					if (data.data && data.data.csrf_token) {
						window.currentCsrfToken = data.data.csrf_token;
						console.log('New CSRF token received:', data.data.csrf_token);
					}
					
					// Chỉ xóa các row nếu thực sự có tác giả được xóa
					if (data.data && data.data.affected > 0) {
						// Xóa các row ngay lập tức
						checkedBoxes.forEach(checkbox => {
							const row = checkbox.closest('tr');
							if (row) {
								row.remove();
							}
						});
					}
					
					showAlert('success', data.message);
				} else {
					showAlert('danger', data.message);
				}
			})
			.catch(error => {
				console.error('Error:', error);
				showAlert('danger', 'Có lỗi xảy ra khi xóa tác giả.');
			});
		}
	});
});
</script>
