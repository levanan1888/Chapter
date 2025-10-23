<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-book me-2"></i>Quản lý Truyện
	</h2>
	<div>
		<a href="<?php echo Uri::base(); ?>admin/stories/trash" class="btn btn-outline-warning me-2">
			<i class="fas fa-trash me-2"></i>Sọt rác
		</a>
		<a href="<?php echo Uri::base(); ?>admin/stories/add" class="btn btn-primary">
			<i class="fas fa-plus me-2"></i>Thêm truyện mới
		</a>
	</div>
</div>

<!-- Search and Filter Bar -->
<div class="card mb-4">
	<div class="card-body">
		<form method="GET" action="<?php echo Uri::base(); ?>admin/stories" class="row g-3">
			<div class="col-md-4">
				<label for="search" class="form-label">Tìm kiếm</label>
				<input type="text" class="form-control" id="search" name="search" 
					   placeholder="Tìm theo tên truyện hoặc tác giả..." 
					   value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
			</div>
			<div class="col-md-2">
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
			<div class="col-md-2">
				<label for="status" class="form-label">Trạng thái</label>
				<select class="form-select" id="status" name="status">
					<option value="">Tất cả trạng thái</option>
					<option value="ongoing" <?php echo (isset($selected_status) && $selected_status == 'ongoing') ? 'selected' : ''; ?>>Đang cập nhật</option>
					<option value="completed" <?php echo (isset($selected_status) && $selected_status == 'completed') ? 'selected' : ''; ?>>Hoàn thành</option>
					<option value="paused" <?php echo (isset($selected_status) && $selected_status == 'paused') ? 'selected' : ''; ?>>Tạm dừng</option>
				</select>
			</div>
			<div class="col-md-2">
				<label for="sort" class="form-label">Sắp xếp</label>
				<select class="form-select" id="sort" name="sort">
					<option value="created_at_desc" <?php echo (isset($sort) && $sort === 'created_at_desc') ? 'selected' : ''; ?>>Mới nhất</option>
					<option value="created_at_asc" <?php echo (isset($sort) && $sort === 'created_at_asc') ? 'selected' : ''; ?>>Cũ nhất</option>
					<option value="title_asc" <?php echo (isset($sort) && $sort === 'title_asc') ? 'selected' : ''; ?>>Tên A-Z</option>
					<option value="title_desc" <?php echo (isset($sort) && $sort === 'title_desc') ? 'selected' : ''; ?>>Tên Z-A</option>
					<option value="views_desc" <?php echo (isset($sort) && $sort === 'views_desc') ? 'selected' : ''; ?>>Lượt xem nhiều nhất</option>
				</select>
			</div>
			<div class="col-md-2 d-flex align-items-end">
				<button type="submit" class="btn btn-primary me-2">
					<i class="fas fa-search me-1"></i>Tìm kiếm
				</button>
				<a href="<?php echo Uri::base(); ?>admin/stories" class="btn btn-outline-secondary">
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
							<th>Ảnh bìa</th>
							<th>Tên truyện</th>
							<th>Tác giả</th>
							<th>Danh mục</th>
							<th>Trạng thái</th>
							<th>Hiển thị</th>
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
								<?php if ($story->cover_image && file_exists(DOCROOT . $story->cover_image)): ?>
									<img src="<?php echo Uri::base() . $story->cover_image; ?>" 
										 class="img-thumbnail" 
										 style="width: 60px; height: 80px; object-fit: cover;" 
										 alt="<?php echo htmlspecialchars($story->title); ?>">
								<?php else: ?>
									<div class="bg-light d-flex align-items-center justify-content-center" 
										 style="width: 60px; height: 80px; border: 1px solid #ddd;">
										<i class="fas fa-book text-muted"></i>
									</div>
								<?php endif; ?>
							</td>
							<td>
								<div>
									<h6 class="mb-0"><?php echo $story->title; ?></h6>
									<small class="text-muted"><?php echo $story->slug; ?></small>
								</div>
							</td>
							<td>
								<?php if (isset($story->author_name) && $story->author_name): ?>
									<span><?php echo $story->author_name; ?></span>
								<?php else: ?>
									<span class="text-muted">Chưa xác định</span>
								<?php endif; ?>
							</td>
							<td>
								<?php if (isset($story->categories) && !empty($story->categories)): ?>
									<?php foreach ($story->categories as $category): ?>
										<span class="badge bg-secondary me-1"><?php echo $category; ?></span>
									<?php endforeach; ?>
								<?php else: ?>
									<span class="text-muted">Chưa phân loại</span>
								<?php endif; ?>
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
							<td>
								<div class="form-check form-switch d-flex justify-content-center">
									<input class="form-check-input visibility-toggle" 
										   type="checkbox" 
										   role="switch"
										   id="visibility_<?php echo $story->id; ?>"
										   data-story-id="<?php echo $story->id; ?>"
										   data-current-visibility="<?php echo $story->is_visible; ?>"
										   <?php echo $story->is_visible ? 'checked' : ''; ?>
										   style="transform: scale(1.2);">
									<label class="form-check-label ms-2" for="visibility_<?php echo $story->id; ?>">
										<span class="visibility-text"><?php echo $story->is_visible ? 'Hiển thị' : 'Ẩn'; ?></span>
									</label>
								</div>
							</td>
							<td><?php echo number_format(isset($story->views) ? $story->views : 0); ?></td>
							<td><?php echo date('d/m/Y', strtotime($story->created_at)); ?></td>
							<td>
								<div class="btn-group" role="group">
									<a href="<?php echo Uri::base(); ?>admin/stories/view/<?php echo $story->id; ?>" 
									   class="btn btn-sm btn-outline-info" title="Xem chi tiết">
										<i class="fas fa-eye"></i>
									</a>
									<a href="<?php echo Uri::base(); ?>admin/stories/edit/<?php echo $story->id; ?>" 
									   class="btn btn-sm btn-outline-primary" title="Sửa">
										<i class="fas fa-edit"></i>
									</a>
									<a href="<?php echo Uri::base(); ?>admin/chapters/<?php echo $story->id; ?>" 
									   class="btn btn-sm btn-outline-secondary" title="Quản lý chương">
										<i class="fas fa-file-alt"></i>
									</a>
									<?php if ($story->deleted_at): ?>
										<span class="btn btn-sm btn-outline-secondary" title="Đã xóa">
											<i class="fas fa-trash-alt"></i>
										</span>
									<?php else: ?>
										<form method="POST" action="<?php echo Uri::base(); ?>admin/stories/delete/<?php echo $story->id; ?>" 
											  style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa truyện này?')">
											<input type="hidden" name="fuel_csrf_token" value="<?php echo \Security::fetch_token(); ?>">
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
						if (isset($selected_category) && !empty($selected_category)) $query_params['category'] = $selected_category;
						if (isset($selected_status) && !empty($selected_status)) $query_params['status'] = $selected_status;
						if (isset($sort) && !empty($sort)) $query_params['sort'] = $sort;
						$query_string = !empty($query_params) ? '&' . http_build_query($query_params) : '';
						?>
						
						<?php if (isset($current_page) && $current_page > 1): ?>
							<li class="page-item">
								<a class="page-link" href="<?php echo Uri::base(); ?>admin/stories?page=<?php echo $current_page - 1; ?><?php echo $query_string; ?>">
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
									<a class="page-link" href="<?php echo Uri::base(); ?>admin/stories?page=<?php echo $i; ?><?php echo $query_string; ?>"><?php echo $i; ?></a>
								</li>
							<?php endif; ?>
						<?php endfor; ?>

						<?php if (isset($current_page) && $current_page < $total_pages): ?>
							<li class="page-item">
								<a class="page-link" href="<?php echo Uri::base(); ?>admin/stories?page=<?php echo $current_page + 1; ?><?php echo $query_string; ?>">
									<i class="fas fa-chevron-right"></i>
								</a>
							</li>
						<?php endif; ?>
					</ul>
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

<!-- Bulk Delete Form -->
<form id="bulk-delete-form" method="POST" action="<?php echo Uri::base(); ?>admin/stories/bulk-delete" style="display: none;">
	<input type="hidden" name="fuel_csrf_token" value="<?php echo \Security::fetch_token(); ?>">
	<div id="bulk-delete-ids"></div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
		fetch('<?php echo Uri::base(); ?>admin/stories', {
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

	const selectAllCheckbox = document.getElementById('select-all');
	const selectAllHeaderCheckbox = document.getElementById('select-all-header');
	const storyCheckboxes = document.querySelectorAll('.story-checkbox');
	const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
	const selectedCountSpan = document.getElementById('selected-count');
	const bulkDeleteForm = document.getElementById('bulk-delete-form');
	const bulkDeleteIds = document.getElementById('bulk-delete-ids');

	// Function to update selected count
	function updateSelectedCount() {
		const checkedBoxes = document.querySelectorAll('.story-checkbox:checked');
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

	// Bulk delete functionality
	bulkDeleteBtn.addEventListener('click', function() {
		const checkedBoxes = document.querySelectorAll('.story-checkbox:checked');
		if (checkedBoxes.length === 0) {
			alert('Vui lòng chọn ít nhất một truyện để xóa.');
			return;
		}

		if (confirm(`Bạn có chắc chắn muốn xóa ${checkedBoxes.length} truyện đã chọn?`)) {
			// Clear previous IDs
			bulkDeleteIds.innerHTML = '';
			
			// Add selected IDs as hidden inputs
			checkedBoxes.forEach(checkbox => {
				const hiddenInput = document.createElement('input');
				hiddenInput.type = 'hidden';
				hiddenInput.name = 'story_ids[]';
				hiddenInput.value = checkbox.value;
				bulkDeleteIds.appendChild(hiddenInput);
			});
			
			// Submit form
			bulkDeleteForm.submit();
		}
	});

	// Visibility toggle functionality
	const visibilityToggles = document.querySelectorAll('.visibility-toggle');
	visibilityToggles.forEach(toggle => {
		toggle.addEventListener('change', function() {
			const storyId = this.getAttribute('data-story-id');
			const currentVisibility = this.getAttribute('data-current-visibility');
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
			formData.append('story_id', storyId);
			formData.append('fuel_csrf_token', csrfToken);
			
			// Send AJAX request
			fetch('<?php echo Uri::base(); ?>admin/stories/toggle_visibility', {
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
					const newVisibility = data.data.is_visible;
					const newText = newVisibility ? 'Hiển thị' : 'Ẩn';
					
					switchElement.checked = newVisibility;
					switchElement.setAttribute('data-current-visibility', newVisibility);
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
					text.textContent = switchElement.checked ? 'Hiển thị' : 'Ẩn';
					
					// Show error message
					showAlert('danger', data.message || 'Có lỗi xảy ra khi cập nhật trạng thái');
				}
			})
			.catch(error => {
				console.error('Error:', error);
				// Revert switch state on error
				switchElement.checked = !switchElement.checked;
				text.textContent = switchElement.checked ? 'Hiển thị' : 'Ẩn';
				showAlert('danger', 'Có lỗi xảy ra khi cập nhật trạng thái');
			})
			.finally(() => {
				// Re-enable switch
				switchElement.disabled = false;
			});
		});
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
	.visibility-toggle {
		width: 3rem !important;
		height: 1.5rem !important;
		background-color: #6c757d !important;
		border: none !important;
		border-radius: 1rem !important;
		position: relative !important;
		transition: all 0.3s ease !important;
		cursor: pointer !important;
	}
	
	.visibility-toggle:checked {
		background-color: #198754 !important;
	}
	
	.visibility-toggle:focus {
		box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25) !important;
	}
	
	.visibility-toggle:disabled {
		opacity: 0.6 !important;
		cursor: not-allowed !important;
	}
	
	.visibility-toggle::before {
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
	
	.visibility-toggle:checked::before {
		transform: translateX(1.5rem);
	}
`;
document.head.appendChild(style);
</script>
