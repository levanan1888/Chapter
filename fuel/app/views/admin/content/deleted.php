<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-trash me-2"></i>Admin đã xóa
	</h2>
	<div>
		<a href="<?php echo Uri::base(); ?>admin/users" class="btn btn-outline-secondary">
			<i class="fas fa-arrow-left me-2"></i>Quay lại
		</a>
	</div>
</div>

<!-- Search and Filter Bar -->
<div class="card mb-4">
	<div class="card-body">
		<form method="GET" action="<?php echo Uri::base(); ?>admin/users/deleted" class="row g-3">
			<div class="col-md-4">
				<label for="search" class="form-label">Tìm kiếm</label>
				<input type="text" class="form-control" id="search" name="search" 
					   placeholder="Tìm theo username, email hoặc tên..." 
					   value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
			</div>
			<div class="col-md-3">
				<label for="sort" class="form-label">Sắp xếp</label>
				<select class="form-select" id="sort" name="sort">
					<option value="deleted_at_desc" <?php echo (isset($sort) && $sort === 'deleted_at_desc') ? 'selected' : ''; ?>>Xóa gần nhất</option>
					<option value="deleted_at_asc" <?php echo (isset($sort) && $sort === 'deleted_at_asc') ? 'selected' : ''; ?>>Xóa xa nhất</option>
					<option value="username_asc" <?php echo (isset($sort) && $sort === 'username_asc') ? 'selected' : ''; ?>>Username A-Z</option>
					<option value="username_desc" <?php echo (isset($sort) && $sort === 'username_desc') ? 'selected' : ''; ?>>Username Z-A</option>
				</select>
			</div>
			<div class="col-md-3 d-flex align-items-end">
				<button type="submit" class="btn btn-primary me-2">
					<i class="fas fa-search me-1"></i>Tìm kiếm
				</button>
				<a href="<?php echo Uri::base(); ?>admin/users/deleted" class="btn btn-outline-secondary">
					<i class="fas fa-times"></i>
				</a>
			</div>
		</form>
	</div>
</div>

<div class="card">
	<div class="card-header">
		<h5 class="mb-0">Admin đã xóa</h5>
	</div>
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

		<?php if (isset($admins) && !empty($admins)): ?>
			<!-- Bulk Actions -->
			<div class="row mb-3">
				<div class="col-md-6">
					<div class="d-flex align-items-center">
						<input type="checkbox" id="select-all" class="form-check-input me-2">
						<label for="select-all" class="form-check-label me-3">Chọn tất cả</label>
						<button type="button" class="btn btn-outline-success btn-sm me-2" id="bulk-restore-btn" disabled style="background-color: #d1e7dd; border-color: #badbcc; color: #0f5132;">
							<i class="fas fa-undo me-1"></i>Khôi phục đã chọn
						</button>
						<button type="button" class="btn btn-outline-danger btn-sm" id="bulk-delete-permanent-btn" disabled style="background-color: #f8d7da; border-color: #f5c6cb; color: #721c24;">
							<i class="fas fa-trash-alt me-1"></i>Xóa vĩnh viễn đã chọn
						</button>
					</div>
				</div>
				<div class="col-md-6 text-end">
					<small class="text-muted">
						<span id="selected-count">0</span> mục đã chọn / Tổng: <strong><?php echo isset($total_admins) ? number_format($total_admins) : 0; ?></strong> admin đã xóa
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
							<th>Tên đăng nhập</th>
							<th>Email</th>
							<th>Họ tên</th>
							<th>Ngày xóa</th>
							<th>Thao tác</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($admins as $user): ?>
						<tr>
							<td>
								<input type="checkbox" class="form-check-input admin-checkbox" value="<?php echo $user->id; ?>">
							</td>
							<td>
								<div class="d-flex align-items-center">
									<div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
										<i class="fas fa-user"></i>
									</div>
									<div>
										<h6 class="mb-0"><?php echo $user->username; ?></h6>
										<?php if ($user->google_id): ?>
											<small class="text-success">
												<i class="fab fa-google me-1"></i>Google
											</small>
										<?php endif; ?>
									</div>
								</div>
							</td>
							<td><?php echo $user->email; ?></td>
							<td><?php echo $user->full_name ?: 'Chưa có'; ?></td>
							<td><?php echo date('d/m/Y H:i', strtotime($user->deleted_at)); ?></td>
							<td>
								<div class="btn-group" role="group">
									<button class="btn btn-sm btn-outline-success" title="Khôi phục" 
											onclick="restoreAdmin(<?php echo $user->id; ?>)">
										<i class="fas fa-undo"></i>
									</button>
									<button class="btn btn-sm btn-outline-danger" title="Xóa vĩnh viễn" 
											onclick="deletePermanentAdmin(<?php echo $user->id; ?>)">
										<i class="fas fa-trash-alt"></i>
									</button>
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
					$search_param = isset($_GET['search']) && !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
					$sort_param = isset($_GET['sort']) && !empty($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '';
					$all_params = $search_param . $sort_param;
					?>
					<?php if ($current_page > 1): ?>
						<li class="page-item">
							<a class="page-link" href="<?php echo Uri::base(); ?>admin/users/deleted?page=<?php echo $current_page - 1; ?><?php echo $all_params; ?>">
								<i class="fas fa-chevron-left"></i> Trước
							</a>
						</li>
					<?php endif; ?>
					
					<?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
						<li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
							<a class="page-link" href="<?php echo Uri::base(); ?>admin/users/deleted?page=<?php echo $i; ?><?php echo $all_params; ?>">
								<?php echo $i; ?>
							</a>
						</li>
					<?php endfor; ?>
					
					<?php if ($current_page < $total_pages): ?>
						<li class="page-item">
							<a class="page-link" href="<?php echo Uri::base(); ?>admin/users/deleted?page=<?php echo $current_page + 1; ?><?php echo $all_params; ?>">
								Sau <i class="fas fa-chevron-right"></i>
							</a>
						</li>
					<?php endif; ?>
				</ul>
			</nav>
			<?php endif; ?>
		<?php else: ?>
			<div class="text-center py-5">
				<i class="fas fa-trash fa-3x text-muted mb-3"></i>
				<h5 class="text-muted">Không có admin nào bị xóa</h5>
				<p class="text-muted">Tất cả admin đều đang hoạt động</p>
			</div>
		<?php endif; ?>
	</div>
</div>

<script>
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

function restoreAdmin(id) {
    if (!confirm('Bạn có chắc chắn muốn khôi phục admin này?')) {
        return;
    }
    
    const button = event.target.closest('button');
    const row = button.closest('tr');
    
    // Use stored token if available, otherwise use initial token
    const csrfToken = window.currentCsrfToken || '<?php echo \Security::fetch_token(); ?>';
    console.log('Sending CSRF token:', csrfToken);
    
    const formData = new FormData();
    formData.append('<?php echo \Config::get("security.csrf_token_key"); ?>', csrfToken);
    
    fetch('<?php echo Uri::base(); ?>admin/users/restore/' + id, {
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
            
            showAlert('success', data.message);
            // Xóa row ngay lập tức
            if (row) {
                row.remove();
            }
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Có lỗi xảy ra khi khôi phục admin.');
    });
}

function deletePermanentAdmin(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa vĩnh viễn admin này? Hành động này không thể hoàn tác!')) {
        return;
    }
    
    const button = event.target.closest('button');
    const row = button.closest('tr');
    
    // Use stored token if available, otherwise use initial token
    const csrfToken = window.currentCsrfToken || '<?php echo \Security::fetch_token(); ?>';
    console.log('Sending CSRF token:', csrfToken);
    
    const formData = new FormData();
    formData.append('<?php echo \Config::get("security.csrf_token_key"); ?>', csrfToken);
    
    fetch('<?php echo Uri::base(); ?>admin/users/delete-permanent/' + id, {
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
            
            showAlert('success', data.message);
            // Xóa row ngay lập tức
            if (row) {
                row.remove();
            }
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Có lỗi xảy ra khi xóa vĩnh viễn admin.');
    });
}

// Bulk actions functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const selectAllHeaderCheckbox = document.getElementById('select-all-header');
    const adminCheckboxes = document.querySelectorAll('.admin-checkbox');
    const bulkRestoreBtn = document.getElementById('bulk-restore-btn');
    const bulkDeletePermanentBtn = document.getElementById('bulk-delete-permanent-btn');
    const selectedCountSpan = document.getElementById('selected-count');

    // Function to update selected count
    function updateSelectedCount() {
        const checkedBoxes = document.querySelectorAll('.admin-checkbox:checked');
        const count = checkedBoxes.length;
        selectedCountSpan.textContent = count;
        
        // Enable/disable bulk action buttons
        bulkRestoreBtn.disabled = count === 0;
        bulkDeletePermanentBtn.disabled = count === 0;
        
        // Update select all checkboxes
        if (count === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
            selectAllHeaderCheckbox.indeterminate = false;
            selectAllHeaderCheckbox.checked = false;
        } else if (count === adminCheckboxes.length) {
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
        adminCheckboxes.forEach(checkbox => {
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

    adminCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Bulk restore functionality
    bulkRestoreBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.admin-checkbox:checked');
        if (checkedBoxes.length === 0) {
            showAlert('warning', 'Vui lòng chọn ít nhất một admin để khôi phục.');
            return;
        }

        if (confirm(`Bạn có chắc chắn muốn khôi phục ${checkedBoxes.length} admin đã chọn?`)) {
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            
            // Use stored token if available, otherwise use initial token
            const csrfToken = window.currentCsrfToken || '<?php echo \Security::fetch_token(); ?>';
            console.log('Sending CSRF token:', csrfToken);
            
            const formData = new FormData();
            ids.forEach(id => {
                formData.append('ids[]', id);
            });
            formData.append('<?php echo \Config::get("security.csrf_token_key"); ?>', csrfToken);
            
            fetch('<?php echo Uri::base(); ?>admin/users/bulk-restore', {
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
                    
                    // Chỉ xóa các row nếu thực sự có admin được khôi phục
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
                showAlert('danger', 'Có lỗi xảy ra khi khôi phục admin.');
            });
        }
    });

    // Bulk delete permanent functionality
    bulkDeletePermanentBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.admin-checkbox:checked');
        if (checkedBoxes.length === 0) {
            showAlert('warning', 'Vui lòng chọn ít nhất một admin để xóa vĩnh viễn.');
            return;
        }

        if (confirm(`Bạn có chắc chắn muốn xóa vĩnh viễn ${checkedBoxes.length} admin đã chọn? Hành động này không thể hoàn tác!`)) {
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            
            // Use stored token if available, otherwise use initial token
            const csrfToken = window.currentCsrfToken || '<?php echo \Security::fetch_token(); ?>';
            console.log('Sending CSRF token:', csrfToken);
            
            const formData = new FormData();
            ids.forEach(id => {
                formData.append('ids[]', id);
            });
            formData.append('<?php echo \Config::get("security.csrf_token_key"); ?>', csrfToken);
            
            fetch('<?php echo Uri::base(); ?>admin/users/bulk-delete-permanent', {
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
                    
                    // Chỉ xóa các row nếu thực sự có admin được xóa
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
                showAlert('danger', 'Có lỗi xảy ra khi xóa vĩnh viễn admin.');
            });
        }
    });
});
</script>