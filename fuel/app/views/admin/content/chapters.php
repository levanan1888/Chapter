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

<?php if (isset($error_message) && !empty($error_message)): ?>
	<div class="alert alert-danger">
		<i class="fas fa-exclamation-triangle me-2"></i>
		<?php echo $error_message; ?>
	</div>
<?php endif; ?>

<?php if (isset($success_message) && !empty($success_message)): ?>
	<div class="alert alert-success">
		<i class="fas fa-check-circle me-2"></i>
		<?php echo $success_message; ?>
	</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h4 class="mb-0">
			<i class="fas fa-book me-2"></i>Quản lý Chương
		</h4>
		<?php if (isset($story)): ?>
			<p class="text-muted mb-0">Truyện: <strong><?php echo htmlspecialchars($story->title); ?></strong></p>
		<?php endif; ?>
	</div>
	<div>
		<a href="<?php echo Uri::base(); ?>admin/stories" class="btn btn-secondary me-2">
			<i class="fas fa-arrow-left me-2"></i>Quay lại
		</a>
		<?php if (isset($story)): ?>
			<a href="<?php echo Uri::base(); ?>admin/chapters/trash/<?php echo $story->id; ?>" class="btn btn-outline-warning me-2">
				<i class="fas fa-trash me-2"></i>Sọt rác
			</a>
			<a href="<?php echo Uri::base(); ?>admin/chapters/add/<?php echo $story->id; ?>" class="btn btn-primary">
				<i class="fas fa-plus me-2"></i>Thêm chương
			</a>
		<?php endif; ?>
	</div>
</div>

<!-- Search and Filter Bar -->
<?php if (isset($story)): ?>
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo Uri::base(); ?>admin/chapters/<?php echo $story->id; ?>" class="row g-3">
            <div class="col-md-6">
                <label for="search" class="form-label">Tìm kiếm</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Tìm theo tên chương..." 
                       value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="all" <?php echo (isset($status) && $status === 'all') ? 'selected' : ''; ?>>Tất cả</option>
                    <option value="active" <?php echo (isset($status) && $status === 'active') ? 'selected' : ''; ?>>Hoạt động</option>
                    <option value="deleted" <?php echo (isset($status) && $status === 'deleted') ? 'selected' : ''; ?>>Đã xóa</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="sort" class="form-label">Sắp xếp</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="created_at_desc" <?php echo (isset($sort) && $sort === 'created_at_desc') ? 'selected' : ''; ?>>Mới nhất</option>
                    <option value="created_at_asc" <?php echo (isset($sort) && $sort === 'created_at_asc') ? 'selected' : ''; ?>>Cũ nhất</option>
                    <option value="chapter_number_asc" <?php echo (isset($sort) && $sort === 'chapter_number_asc') ? 'selected' : ''; ?>>Số chương tăng dần</option>
                    <option value="chapter_number_desc" <?php echo (isset($sort) && $sort === 'chapter_number_desc') ? 'selected' : ''; ?>>Số chương giảm dần</option>
                    <option value="updated_at_desc" <?php echo (isset($sort) && $sort === 'updated_at_desc') ? 'selected' : ''; ?>>Cập nhật mới nhất</option>
                </select>
            </div>
            <div class="col-12 d-flex align-items-end justify-content-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i>Tìm kiếm
                </button>
                <a href="<?php echo Uri::base(); ?>admin/chapters/<?php echo $story->id; ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
    </div>
<?php endif; ?>

<?php if (isset($chapters) && !empty($chapters)): ?>
	<div class="card">
		<div class="card-body">
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
                            <th>Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Trạng thái</th>
                            <th>Lượt xem</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
						</tr>
					</thead>
					<tbody>
                        <?php foreach ($chapters as $chapter): ?>
                            <?php 
                                $images = method_exists($chapter, 'get_images') ? $chapter->get_images() : array();
                                $thumb = (is_array($images) && !empty($images)) ? $images[0] : '';
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input chapter-checkbox" value="<?php echo $chapter->id; ?>">
                                </td>
                                <td>
                                    <?php if (!empty($thumb) && file_exists(DOCROOT . $thumb)): ?>
                                        <img src="<?php echo Uri::base() . $thumb; ?>" 
                                             class="img-thumbnail" 
                                             style="width: 60px; height: 80px; object-fit: cover;" 
                                             alt="<?php echo htmlspecialchars($chapter->title); ?>">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 80px; border: 1px solid #ddd;">
                                            <i class="fas fa-file-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($chapter->title); ?></h6>
                                        <small class="text-muted">Chương <span class="badge bg-primary"><?php echo $chapter->chapter_number; ?></span></small>
                                    </div>
                                </td>
                                <td>
                                    <?php if (empty($chapter->deleted_at)): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Hoạt động
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-trash me-1"></i>Đã xóa
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo number_format($chapter->views); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($chapter->created_at)); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo Uri::base(); ?>admin/chapters/edit/<?php echo $chapter->id; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if (empty($chapter->deleted_at)): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Xóa"
                                                    onclick="softDeleteChapter(<?php echo $chapter->id; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-outline-success" title="Khôi phục"
                                                    onclick="restoreChapter(<?php echo $chapter->id; ?>)">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Xóa vĩnh viễn"
                                                    onclick="forceDeleteChapter(<?php echo $chapter->id; ?>)">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<?php if (isset($total_pages) && $total_pages > 1): ?>
				<nav aria-label="Page navigation">
					<ul class="pagination justify-content-center">
                        <?php 
                        $query_params = array();
                        if (isset($search) && $search !== '') $query_params['search'] = $search;
                        if (isset($status) && $status !== '') $query_params['status'] = $status;
                        if (isset($sort) && $sort !== '') $query_params['sort'] = $sort;
                        $query_string = !empty($query_params) ? '&' . http_build_query($query_params) : '';
                        ?>
                        <?php if ($current_page > 1): ?>
							<li class="page-item">
                                <a class="page-link" href="?page=<?php echo $current_page - 1; ?><?php echo $query_string; ?>">Trước</a>
							</li>
						<?php endif; ?>
						
						<?php for ($i = 1; $i <= $total_pages; $i++): ?>
							<li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $query_string; ?>"><?php echo $i; ?></a>
							</li>
						<?php endfor; ?>
						
						<?php if ($current_page < $total_pages): ?>
							<li class="page-item">
                                <a class="page-link" href="?page=<?php echo $current_page + 1; ?><?php echo $query_string; ?>">Sau</a>
							</li>
						<?php endif; ?>
					</ul>
				</nav>
			<?php endif; ?>
		</div>
	</div>
<?php else: ?>
	<div class="card">
		<div class="card-body text-center py-5">
			<i class="fas fa-book-open fa-3x text-muted mb-3"></i>
			<h5 class="text-muted">Chưa có chương nào</h5>
			<p class="text-muted">Hãy thêm chương đầu tiên cho truyện này</p>
			<?php if (isset($story)): ?>
				<a href="<?php echo Uri::base(); ?>admin/chapters/add/<?php echo $story->id; ?>" class="btn btn-primary">
					<i class="fas fa-plus me-2"></i>Thêm chương đầu tiên
				</a>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>

<!-- Bulk Delete Form -->
<form id="bulk-delete-form" method="POST" action="<?php echo Uri::base(); ?>admin/chapters/bulk-delete" style="display: none;">
	<input type="hidden" name="fuel_csrf_token" value="<?php echo \Security::fetch_token(); ?>">
	<div id="bulk-delete-ids"></div>
</form>

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
async function refreshCsrfToken() {
	try {
		const response = await fetch('<?php echo Uri::base(); ?>admin/chapters', {
			method: 'GET',
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		});
		
		if (!response.ok) {
			throw new Error(`HTTP error! status: ${response.status}`);
		}
		
		const html = await response.text();
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
			return newToken;
		} else {
			console.warn('No CSRF token found in response');
			return null;
		}
	} catch (err) {
		console.error('Failed to refresh CSRF token:', err);
		return null;
	}
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

// Bulk selection logic
const selectAllCheckbox = document.getElementById('select-all');
const selectAllHeaderCheckbox = document.getElementById('select-all-header');
const chapterCheckboxes = document.querySelectorAll('.chapter-checkbox');
const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
const selectedCountSpan = document.getElementById('selected-count');
const bulkDeleteForm = document.getElementById('bulk-delete-form');
const bulkDeleteIds = document.getElementById('bulk-delete-ids');

function updateSelectedCount() {
    const checked = document.querySelectorAll('.chapter-checkbox:checked');
    const count = checked.length;
    selectedCountSpan.textContent = count;
    bulkDeleteBtn.disabled = count === 0;

    if (count === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
        selectAllHeaderCheckbox.indeterminate = false;
        selectAllHeaderCheckbox.checked = false;
    } else if (count === chapterCheckboxes.length) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
        selectAllHeaderCheckbox.indeterminate = false;
        selectAllHeaderCheckbox.checked = true;
    } else {
        selectAllCheckbox.indeterminate = true;
        selectAllHeaderCheckbox.indeterminate = true;
    }
}

function setAllChecked(checked) {
    chapterCheckboxes.forEach(cb => { cb.checked = checked; });
    updateSelectedCount();
}

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() { setAllChecked(this.checked); });
}
if (selectAllHeaderCheckbox) {
    selectAllHeaderCheckbox.addEventListener('change', function() { setAllChecked(this.checked); });
}
chapterCheckboxes.forEach(cb => cb.addEventListener('change', updateSelectedCount));

if (bulkDeleteBtn) {
    bulkDeleteBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.chapter-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Vui lòng chọn ít nhất một chương để xóa.');
            return;
        }

        if (confirm(`Bạn có chắc chắn muốn xóa ${checkedBoxes.length} chương đã chọn?`)) {
            // Clear previous IDs
            bulkDeleteIds.innerHTML = '';
            
            // Add selected IDs as hidden inputs
            checkedBoxes.forEach(checkbox => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'chapter_ids[]';
                hiddenInput.value = checkbox.value;
                bulkDeleteIds.appendChild(hiddenInput);
            });
            
            // Submit form
            bulkDeleteForm.submit();
        }
    });
}

function softDeleteChapter(chapterId) {
	if (confirm('Bạn có chắc chắn muốn xóa chương này vào sọt rác? Chương có thể được khôi phục sau.')) {
		const formData = new FormData();
		formData.append('<?php echo \Config::get('security.csrf_token_key'); ?>', window.currentCsrfToken || '<?php echo \Security::fetch_token(); ?>');

        fetch(`<?php echo Uri::base(); ?>admin/chapters/delete/${chapterId}`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				showAlert('success', data.message);
                if (data.data && data.data.csrf_token) { window.currentCsrfToken = data.data.csrf_token; }
				// Reload page to update the list
				setTimeout(() => {
					window.location.reload();
				}, 1000);
			} else {
				showAlert('error', data.message || 'Có lỗi xảy ra');
			}
		})
		.catch(error => {
			showAlert('error', 'Có lỗi xảy ra khi xóa chương');
		});
	}
}

function restoreChapter(chapterId) {
	if (confirm('Bạn có chắc chắn muốn khôi phục chương này?')) {
		const formData = new FormData();
		formData.append('<?php echo \Config::get('security.csrf_token_key'); ?>', window.currentCsrfToken || '<?php echo \Security::fetch_token(); ?>');

        fetch(`<?php echo Uri::base(); ?>admin/chapters/restore/${chapterId}`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				showAlert('success', data.message);
                if (data.data && data.data.csrf_token) { window.currentCsrfToken = data.data.csrf_token; }
				// Reload page to update the list
				setTimeout(() => {
					window.location.reload();
				}, 1000);
			} else {
				showAlert('error', data.message || 'Có lỗi xảy ra');
			}
		})
		.catch(error => {
			showAlert('error', 'Có lỗi xảy ra khi khôi phục chương');
		});
	}
}

function forceDeleteChapter(chapterId) {
	if (confirm('Bạn có chắc chắn muốn xóa vĩnh viễn chương này? Hành động này không thể hoàn tác!')) {
		const formData = new FormData();
		formData.append('<?php echo \Config::get('security.csrf_token_key'); ?>', window.currentCsrfToken || '<?php echo \Security::fetch_token(); ?>');

        fetch(`<?php echo Uri::base(); ?>admin/chapters/force-delete/${chapterId}`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				showAlert('success', data.message);
                if (data.data && data.data.csrf_token) { window.currentCsrfToken = data.data.csrf_token; }
				// Reload page to update the list
				setTimeout(() => {
					window.location.reload();
				}, 1000);
			} else {
				showAlert('error', data.message || 'Có lỗi xảy ra');
			}
		})
		.catch(error => {
			showAlert('error', 'Có lỗi xảy ra khi xóa chương');
		});
	}
}

function showAlert(type, message) {
    // Remove existing alerts
    const existing = document.querySelectorAll('.card-body > .alert');
    existing.forEach(el => el.remove());

    const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${icon} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    const container = document.querySelector('.card-body');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
    } else {
        document.body.appendChild(alertDiv);
    }

    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
</script>