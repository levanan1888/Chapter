<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-tags me-2"></i>Quản lý Danh mục
	</h2>
	<div>
		<a href="<?php echo Uri::base(); ?>admin/categories/trash" class="btn btn-outline-warning me-2">
			<i class="fas fa-trash me-2"></i>Sọt rác
		</a>
		<a href="<?php echo Uri::base(); ?>admin/categories/add" class="btn btn-primary">
			<i class="fas fa-plus me-2"></i>Thêm danh mục mới
		</a>
	</div>
</div>

<!-- Hidden form for CSRF token -->
<form id="csrf-form" style="display: none;">
	<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
</form>

<!-- Search and Filter Bar -->
<div class="card mb-4">
	<div class="card-body">
		<form method="GET" action="<?php echo Uri::base(); ?>admin/categories" class="row g-3">
			<div class="col-md-4">
				<label for="search" class="form-label">Tìm kiếm</label>
				<input type="text" class="form-control" id="search" name="q" 
					   placeholder="Tìm theo tên danh mục..." 
					   value="<?php echo isset($q) ? htmlspecialchars($q) : ''; ?>">
			</div>
			<div class="col-md-3">
				<label for="status" class="form-label">Trạng thái</label>
				<select class="form-select" id="status" name="status">
					<option value="all" <?php echo (isset($status) && $status === 'all') ? 'selected' : ''; ?>>Tất cả trạng thái</option>
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
				<a href="<?php echo Uri::base(); ?>admin/categories" class="btn btn-outline-secondary">
					<i class="fas fa-times"></i>
				</a>
			</div>
		</form>
	</div>
</div>

<!-- Categories Table -->
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

		<?php if (isset($categories) && !empty($categories)): ?>
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
							<th>Tên danh mục</th>
							<th>Mô tả</th>
							<th>Trạng thái</th>
							<th>Số truyện</th>
							<th>Ngày tạo</th>
							<th>Thao tác</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($categories as $category): ?>
						<tr>
							<td>
								<input type="checkbox" class="form-check-input category-checkbox" value="<?php echo $category->id; ?>">
							</td>
							<td>
								<div class="d-flex align-items-center">
									<div class="category-icon me-3" style="width: 40px; height: 40px; background-color: <?php echo $category->color; ?>; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
										<i class="fas fa-tag text-white"></i>
									</div>
									<div>
										<h6 class="mb-0"><?php echo $category->name; ?></h6>
										<small class="text-muted"><?php echo $category->slug; ?></small>
									</div>
								</div>
							</td>
							<td>
								<?php if ($category->description): ?>
									<p class="mb-0 text-truncate" style="max-width: 200px;" title="<?php echo $category->description; ?>">
										<?php echo $category->description; ?>
									</p>
								<?php else: ?>
									<span class="text-muted">Chưa có mô tả</span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ($category->deleted_at): ?>
									<span class="badge bg-danger">
										<i class="fas fa-trash me-1"></i>Đã xóa
									</span>
								<?php else: ?>
									<div class="form-check form-switch d-flex justify-content-center">
										<input class="form-check-input category-toggle" 
											   type="checkbox" 
											   role="switch"
											   id="category_<?php echo $category->id; ?>"
											   data-category-id="<?php echo $category->id; ?>"
											   data-current-active="<?php echo $category->is_active; ?>"
											   <?php echo $category->is_active ? 'checked' : ''; ?>>
										<label class="form-check-label visibility-text ms-2" for="category_<?php echo $category->id; ?>">
											<?php echo $category->is_active ? 'Hoạt động' : 'Không hoạt động'; ?>
										</label>
									</div>
								<?php endif; ?>
							</td>
							<td>
								<?php 
								$story_count = $category->story_count ?? 0;
								if ($story_count > 0): ?>
									<span class="badge bg-warning" title="Có <?php echo $story_count; ?> truyện thuộc danh mục này. Xóa danh mục sẽ ẩn tất cả truyện này.">
										<i class="fas fa-exclamation-triangle me-1"></i><?php echo $story_count; ?> truyện
									</span>
								<?php else: ?>
									<span class="badge bg-secondary">0 truyện</span>
								<?php endif; ?>
							</td>
							<td><?php echo date('d/m/Y', strtotime($category->created_at)); ?></td>
							<td>
								<div class="btn-group" role="group">
									<!-- <a href="<?php echo Uri::base(); ?>admin/categories/view/<?php echo $category->id; ?>" 
									   class="btn btn-sm btn-outline-info" title="Xem chi tiết">
										<i class="fas fa-eye"></i>
									</a> -->
									<a href="<?php echo Uri::base(); ?>admin/categories/edit/<?php echo $category->id; ?>" 
									   class="btn btn-sm btn-outline-primary" title="Sửa">
										<i class="fas fa-edit"></i>
									</a>
									<?php if ($category->deleted_at): ?>
										<span class="btn btn-sm btn-outline-secondary" title="Đã xóa">
											<i class="fas fa-trash-alt"></i>
										</span>
									<?php elseif ($category->is_active == 0): ?>
										<button type="button" class="btn btn-sm btn-outline-warning" title="Danh mục không hoạt động - không thể xóa">
											<i class="fas fa-ban"></i>
										</button>
									<?php else: ?>
										<form method="POST" action="<?php echo Uri::base(); ?>admin/categories/delete/<?php echo $category->id; ?>" 
											  style="display: inline;" onsubmit="return confirmDeleteCategory(<?php echo $story_count; ?>, '<?php echo addslashes($category->name); ?>')">
											<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
											<button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa danh mục">
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
						if (isset($q) && !empty($q)) $query_params['q'] = $q;
						if (isset($status) && !empty($status)) $query_params['status'] = $status;
						if (isset($sort) && !empty($sort)) $query_params['sort'] = $sort;
						$query_string = !empty($query_params) ? '&' . http_build_query($query_params) : '';
						?>
						
						<?php $current_page = isset($page) ? $page : 1; ?>
						<?php if ($current_page > 1): ?>
							<li class="page-item">
								<a class="page-link" href="<?php echo Uri::base(); ?>admin/categories?page=<?php echo $current_page - 1; ?><?php echo $query_string; ?>">
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
									<a class="page-link" href="<?php echo Uri::base(); ?>admin/categories?page=<?php echo $i; ?><?php echo $query_string; ?>"><?php echo $i; ?></a>
								</li>
							<?php endif; ?>
						<?php endfor; ?>

						<?php if ($current_page < $total_pages): ?>
							<li class="page-item">
								<a class="page-link" href="<?php echo Uri::base(); ?>admin/categories?page=<?php echo $current_page + 1; ?><?php echo $query_string; ?>">
									<i class="fas fa-chevron-right"></i>
								</a>
							</li>
						<?php endif; ?>
					</ul>
				</nav>
			<?php endif; ?>

		<?php else: ?>
			<div class="text-center py-5">
				<i class="fas fa-tags fa-3x text-muted mb-3"></i>
				<h5 class="text-muted">Chưa có danh mục nào</h5>
				<p class="text-muted">Hãy thêm danh mục đầu tiên của bạn</p>
				<a href="<?php echo Uri::base(); ?>admin/categories/add" class="btn btn-primary">
					<i class="fas fa-plus me-2"></i>Thêm danh mục mới
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- Bulk Delete Form -->
<form id="bulk-delete-form" method="POST" action="<?php echo Uri::base(); ?>admin/categories/bulk-delete" style="display: none;">
	<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
	<div id="bulk-delete-ids"></div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const selectAllCheckbox = document.getElementById('select-all');
	const selectAllHeaderCheckbox = document.getElementById('select-all-header');
	const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
	const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
	const selectedCountSpan = document.getElementById('selected-count');
	const bulkDeleteForm = document.getElementById('bulk-delete-form');
	const bulkDeleteIds = document.getElementById('bulk-delete-ids');

	// Function to update selected count
	function updateSelectedCount() {
		const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
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
		} else if (count === categoryCheckboxes.length) {
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
		categoryCheckboxes.forEach(checkbox => {
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

	categoryCheckboxes.forEach(checkbox => {
		checkbox.addEventListener('change', updateSelectedCount);
	});

	// Bulk delete functionality
	bulkDeleteBtn.addEventListener('click', function() {
		const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
		if (checkedBoxes.length === 0) {
			alert('Vui lòng chọn ít nhất một danh mục để xóa.');
			return;
		}

		if (confirm(`Bạn có chắc chắn muốn xóa ${checkedBoxes.length} danh mục đã chọn?`)) {
			// Clear previous IDs
			bulkDeleteIds.innerHTML = '';
			
			// Add selected IDs as hidden inputs
			checkedBoxes.forEach(checkbox => {
				const hiddenInput = document.createElement('input');
				hiddenInput.type = 'hidden';
				hiddenInput.name = 'category_ids[]';
				hiddenInput.value = checkbox.value;
				bulkDeleteIds.appendChild(hiddenInput);
			});
			
			// Submit form
			bulkDeleteForm.submit();
		}
	});

	// Function to confirm category deletion with story count warning
	window.confirmDeleteCategory = function(storyCount, categoryName) {
		if (storyCount > 0) {
			return confirm(`⚠️ CẢNH BÁO!\n\nDanh mục "${categoryName}" có ${storyCount} truyện.\n\nKhi xóa danh mục này:\n• Tất cả ${storyCount} truyện sẽ bị ẨN\n• Các truyện sẽ không hiển thị trên website\n• Bạn có thể khôi phục bằng cách restore danh mục\n\nBạn có chắc chắn muốn tiếp tục?`);
		} else {
			return confirm(`Bạn có chắc chắn muốn xóa danh mục "${categoryName}"?\n\nDanh mục này không có truyện nào nên sẽ không ảnh hưởng đến nội dung.`);
		}
	};

	// Category toggle functionality
	const categoryToggles = document.querySelectorAll('.category-toggle');
	categoryToggles.forEach(toggle => {
		toggle.addEventListener('change', function() {
			const categoryId = this.getAttribute('data-category-id');
			const currentActive = this.getAttribute('data-current-active');
			const switchElement = this;
			const text = this.parentElement.querySelector('.visibility-text');
			
			// Disable switch during request
			switchElement.disabled = true;
			text.textContent = 'Đang xử lý...';
			
			// Prepare form data with current CSRF token
			// Use stored token if available, otherwise use initial token
			const csrfToken = window.currentCsrfToken || '<?php echo \Security::fetch_token(); ?>';
			console.log('Sending CSRF token:', csrfToken);
			
			const formData = new URLSearchParams();
			formData.append('is_active', this.checked ? 1 : 0);
			formData.append('fuel_csrf_token', csrfToken);
			
			// Send AJAX request
			fetch('<?php echo Uri::base(); ?>admin/categories/toggle_status/' + categoryId, {
				method: 'POST',
				body: formData,
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'Content-Type': 'application/x-www-form-urlencoded'
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					// Update switch state
					const newActive = data.data.is_active;
					const newText = newActive ? 'Hoạt động' : 'Không hoạt động';
					
					switchElement.checked = newActive;
					switchElement.setAttribute('data-current-active', newActive);
					text.textContent = newText;
					
					// Store new CSRF token for next request
					if (data.data.csrf_token) {
						window.currentCsrfToken = data.data.csrf_token;
						console.log('New CSRF token received:', data.data.csrf_token);
					}
					
					// Show success message
					showAlert('success', data.message);
				} else {
					// Revert switch state on error
					switchElement.checked = !switchElement.checked;
					text.textContent = switchElement.checked ? 'Hoạt động' : 'Không hoạt động';
					
					// Show error message
					showAlert('danger', data.message || 'Có lỗi xảy ra khi cập nhật trạng thái');
				}
			})
			.catch(error => {
				console.error('Error:', error);
				// Revert switch state on error
				switchElement.checked = !switchElement.checked;
				text.textContent = switchElement.checked ? 'Hoạt động' : 'Không hoạt động';
				showAlert('danger', 'Có lỗi xảy ra khi cập nhật trạng thái');
			})
			.finally(() => {
				// Re-enable switch
				switchElement.disabled = false;
			});
		});
	});

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
		fetch('<?php echo Uri::base(); ?>admin/categories', {
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
		
		// Insert at the top of the card body
		const cardBody = document.querySelector('.card-body');
		cardBody.insertBefore(alertDiv, cardBody.firstChild);
		
		// Auto dismiss after 5 seconds
		setTimeout(() => {
			if (alertDiv.parentNode) {
				alertDiv.remove();
			}
		}, 5000);
	}
});

// Custom CSS for toggle switch
const style = document.createElement('style');
style.textContent = `
	.category-toggle {
		width: 3rem !important;
		height: 1.5rem !important;
		background-color: #6c757d !important;
		border: none !important;
		border-radius: 1rem !important;
		position: relative !important;
		transition: background-color 0.3s ease !important;
		cursor: pointer !important;
	}
	
	.category-toggle:checked {
		background-color: #198754 !important;
	}
	
	.category-toggle:focus {
		box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25) !important;
	}
	
	.category-toggle:disabled {
		opacity: 0.6 !important;
		cursor: not-allowed !important;
	}
	
	.category-toggle::before {
		content: '';
		position: absolute;
		top: 0.125rem;
		left: 0.125rem;
		width: 1.25rem;
		height: 1.25rem;
		background-color: white;
		border-radius: 50%;
		transition: transform 0.3s ease;
	}
	
	.category-toggle:checked::before {
		transform: translateX(1.5rem);
	}
`;
document.head.appendChild(style);
</script>