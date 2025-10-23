<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-trash me-2"></i>Sọt rác - Chương đã xóa
		<?php if (isset($story)): ?>
			<small class="text-muted">- <?php echo $story->title; ?></small>
		<?php endif; ?>
	</h2>
    <div class="d-flex gap-2">
        <?php if (isset($story)): ?>
            <a href="<?php echo Uri::base(); ?>admin/chapters/<?php echo $story->id; ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
	<div class="card-body">
		<form method="GET" action="<?php echo Uri::base(); ?>admin/chapters/trash/<?php echo isset($story) ? $story->id : ''; ?>" class="row g-3">
			<div class="col-md-4">
				<label for="search" class="form-label">Tìm kiếm</label>
				<input type="text" class="form-control" id="search" name="search" 
					   value="<?php echo isset($search) ? $search : ''; ?>" 
					   placeholder="Tìm theo tên chương...">
			</div>
			<div class="col-md-3">
				<label for="sort" class="form-label">Sắp xếp</label>
				<select class="form-select" id="sort" name="sort">
					<option value="deleted_at_desc" <?php echo (isset($sort) && $sort === 'deleted_at_desc') ? 'selected' : ''; ?>>Mới xóa nhất</option>
					<option value="deleted_at_asc" <?php echo (isset($sort) && $sort === 'deleted_at_asc') ? 'selected' : ''; ?>>Cũ xóa nhất</option>
					<option value="chapter_number_asc" <?php echo (isset($sort) && $sort === 'chapter_number_asc') ? 'selected' : ''; ?>>Số chương tăng dần</option>
					<option value="chapter_number_desc" <?php echo (isset($sort) && $sort === 'chapter_number_desc') ? 'selected' : ''; ?>>Số chương giảm dần</option>
					<option value="title_asc" <?php echo (isset($sort) && $sort === 'title_asc') ? 'selected' : ''; ?>>Tên A-Z</option>
					<option value="title_desc" <?php echo (isset($sort) && $sort === 'title_desc') ? 'selected' : ''; ?>>Tên Z-A</option>
				</select>
			</div>
			<div class="col-md-3 d-flex align-items-end">
				<button type="submit" class="btn btn-primary me-2">
					<i class="fas fa-search me-2"></i>Tìm kiếm
				</button>
				<a href="<?php echo Uri::base(); ?>admin/chapters/trash/<?php echo isset($story) ? $story->id : ''; ?>" class="btn btn-outline-secondary">
					<i class="fas fa-times me-2"></i>Xóa bộ lọc
				</a>
			</div>
		</form>
	</div>
</div>

<!-- Chapters List -->
<div class="card">
	<div class="card-header">
		<div class="d-flex justify-content-between align-items-center">
			<h5 class="mb-0">
				<i class="fas fa-list me-2"></i>Danh sách chương đã xóa
				<span class="badge bg-danger ms-2"><?php echo isset($total_chapters) ? $total_chapters : 0; ?></span>
			</h5>
			<div class="text-muted">
				<small>
					<i class="fas fa-info-circle me-1"></i>
					Các chương trong sọt rác sẽ bị xóa vĩnh viễn sau 30 ngày
				</small>
			</div>
		</div>
	</div>
	<div class="card-body p-0">
		<?php if (empty($chapters)): ?>
			<div class="text-center py-5">
				<i class="fas fa-trash fa-3x text-muted mb-3"></i>
				<h5 class="text-muted">Sọt rác trống</h5>
				<p class="text-muted">Không có chương nào đã bị xóa.</p>
			</div>
		<?php else: ?>
			<div class="table-responsive">
				<table class="table table-hover mb-0">
					<thead class="table-light">
						<tr>
							<th width="5%">
								<input type="checkbox" id="select-all" class="form-check-input">
							</th>
							<th width="10%">Số chương</th>
							<th width="35%">Tên chương</th>
							<th width="15%">Số ảnh</th>
							<th width="15%">Ngày xóa</th>
							<th width="20%" class="text-center">Thao tác</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($chapters as $chapter): ?>
							<tr data-chapter-id="<?php echo $chapter->id; ?>">
								<td>
									<input type="checkbox" class="form-check-input chapter-checkbox" value="<?php echo $chapter->id; ?>">
								</td>
								<td>
									<span class="badge bg-secondary"><?php echo $chapter->chapter_number; ?></span>
								</td>
								<td>
									<div class="d-flex align-items-center">
										<div class="me-2">
											<?php 
											$images = $chapter->get_images();
											if (!empty($images)): 
											?>
												<img src="<?php echo Uri::base() . $images[0]; ?>" 
													 class="rounded" 
													 style="width: 40px; height: 40px; object-fit: cover;" 
													 alt="Preview">
											<?php else: ?>
												<div class="bg-light rounded d-flex align-items-center justify-content-center" 
													 style="width: 40px; height: 40px;">
													<i class="fas fa-image text-muted"></i>
												</div>
											<?php endif; ?>
										</div>
										<div>
											<div class="fw-medium"><?php echo Security::htmlentities($chapter->title); ?></div>
											<small class="text-muted">
												<i class="fas fa-eye me-1"></i><?php echo number_format($chapter->views); ?> lượt xem
											</small>
										</div>
									</div>
								</td>
								<td>
									<span class="badge bg-info"><?php echo count($images); ?> ảnh</span>
								</td>
								<td>
									<div>
										<div class="text-muted small">
											<i class="fas fa-calendar me-1"></i>
											<?php echo date('d/m/Y', strtotime($chapter->deleted_at)); ?>
										</div>
										<div class="text-muted small">
											<i class="fas fa-clock me-1"></i>
											<?php echo date('H:i', strtotime($chapter->deleted_at)); ?>
										</div>
									</div>
								</td>
								<td class="text-center">
									<div class="btn-group" role="group">
										<button type="button" class="btn btn-sm btn-outline-success restore-btn" 
												data-chapter-id="<?php echo $chapter->id; ?>" 
												title="Khôi phục">
											<i class="fas fa-undo"></i>
										</button>
										<button type="button" class="btn btn-sm btn-outline-danger force-delete-btn" 
												data-chapter-id="<?php echo $chapter->id; ?>" 
												title="Xóa vĩnh viễn">
											<i class="fas fa-trash-alt"></i>
										</button>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
	
	<?php if (!empty($chapters) && isset($total_pages) && $total_pages > 1): ?>
		<div class="card-footer">
			<div class="d-flex justify-content-between align-items-center">
				<div class="text-muted">
					<small>
						Hiển thị <?php echo count($chapters); ?> trong tổng số <?php echo $total_chapters; ?> chương
					</small>
				</div>
				<nav>
					<ul class="pagination pagination-sm mb-0">
						<?php if ($current_page > 1): ?>
							<li class="page-item">
								<a class="page-link" href="<?php echo Uri::base(); ?>admin/chapters/trash/<?php echo $story->id; ?>?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>">
									<i class="fas fa-chevron-left"></i>
								</a>
							</li>
						<?php endif; ?>
						
						<?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
							<li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
								<a class="page-link" href="<?php echo Uri::base(); ?>admin/chapters/trash/<?php echo $story->id; ?>?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>">
									<?php echo $i; ?>
								</a>
							</li>
						<?php endfor; ?>
						
						<?php if ($current_page < $total_pages): ?>
							<li class="page-item">
								<a class="page-link" href="<?php echo Uri::base(); ?>admin/chapters/trash/<?php echo $story->id; ?>?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>">
									<i class="fas fa-chevron-right"></i>
								</a>
							</li>
						<?php endif; ?>
					</ul>
				</nav>
			</div>
		</div>
	<?php endif; ?>
</div>

<!-- Bulk Actions -->
<div class="card mt-3" id="bulk-actions" style="display: none;">
	<div class="card-body">
		<div class="d-flex justify-content-between align-items-center">
			<div>
				<strong id="selected-count">0</strong> chương được chọn
			</div>
			<div class="btn-group">
				<button type="button" class="btn btn-success" id="bulk-restore-btn">
					<i class="fas fa-undo me-2"></i>Khôi phục tất cả
				</button>
				<button type="button" class="btn btn-danger" id="bulk-force-delete-btn">
					<i class="fas fa-trash-alt me-2"></i>Xóa vĩnh viễn tất cả
				</button>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const chapterCheckboxes = document.querySelectorAll('.chapter-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    const bulkRestoreBtn = document.getElementById('bulk-restore-btn');
    const bulkForceDeleteBtn = document.getElementById('bulk-force-delete-btn');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        chapterCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    // Individual checkbox change
    chapterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.chapter-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (count > 0) {
            bulkActions.style.display = 'block';
            selectedCount.textContent = count;
        } else {
            bulkActions.style.display = 'none';
        }
        
        // Update select all checkbox state
        if (count === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (count === chapterCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Restore individual chapter
    document.querySelectorAll('.restore-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const chapterId = this.dataset.chapterId;
            if (confirm('Bạn có chắc chắn muốn khôi phục chương này?')) {
                restoreChapter(chapterId);
            }
        });
    });

    // Force delete individual chapter
    document.querySelectorAll('.force-delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const chapterId = this.dataset.chapterId;
            if (confirm('Bạn có chắc chắn muốn xóa vĩnh viễn chương này? Hành động này không thể hoàn tác!')) {
                forceDeleteChapter(chapterId);
            }
        });
    });

    // Bulk restore
    bulkRestoreBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.chapter-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (confirm(`Bạn có chắc chắn muốn khôi phục ${count} chương đã chọn?`)) {
            const chapterIds = Array.from(checkedBoxes).map(cb => cb.value);
            bulkRestore(chapterIds);
        }
    });

    // Bulk force delete
    bulkForceDeleteBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.chapter-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (confirm(`Bạn có chắc chắn muốn xóa vĩnh viễn ${count} chương đã chọn? Hành động này không thể hoàn tác!`)) {
            const chapterIds = Array.from(checkedBoxes).map(cb => cb.value);
            bulkForceDelete(chapterIds);
        }
    });


    function restoreChapter(chapterId) {
        const formData = new FormData();
        formData.append('<?php echo \Config::get('security.csrf_token_key'); ?>', '<?php echo \Security::fetch_token(); ?>');

        fetch(`<?php echo Uri::base(); ?>admin/chapters/restore/${chapterId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                // Remove the row
                const row = document.querySelector(`tr[data-chapter-id="${chapterId}"]`);
                if (row) row.remove();
                updateBulkActions();
            } else {
                showAlert('error', data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(error => {
            showAlert('error', 'Có lỗi xảy ra khi khôi phục chương');
        });
    }

    function forceDeleteChapter(chapterId) {
        const formData = new FormData();
        formData.append('<?php echo \Config::get('security.csrf_token_key'); ?>', '<?php echo \Security::fetch_token(); ?>');

        fetch(`<?php echo Uri::base(); ?>admin/chapters/force-delete/${chapterId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                // Remove the row
                const row = document.querySelector(`tr[data-chapter-id="${chapterId}"]`);
                if (row) row.remove();
                updateBulkActions();
            } else {
                showAlert('error', data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(error => {
            showAlert('error', 'Có lỗi xảy ra khi xóa chương');
        });
    }

    function bulkRestore(chapterIds) {
        const promises = chapterIds.map(chapterId => {
            const formData = new FormData();
            formData.append('<?php echo \Config::get('security.csrf_token_key'); ?>', '<?php echo \Security::fetch_token(); ?>');
            
            return fetch(`<?php echo Uri::base(); ?>admin/chapters/restore/${chapterId}`, {
                method: 'POST',
                body: formData
            });
        });

        Promise.all(promises)
        .then(responses => Promise.all(responses.map(r => r.json())))
        .then(results => {
            const successCount = results.filter(r => r.success).length;
            showAlert('success', `Đã khôi phục ${successCount}/${chapterIds.length} chương`);
            
            // Remove successful rows
            results.forEach((result, index) => {
                if (result.success) {
                    const row = document.querySelector(`tr[data-chapter-id="${chapterIds[index]}"]`);
                    if (row) row.remove();
                }
            });
            
            updateBulkActions();
        })
        .catch(error => {
            showAlert('error', 'Có lỗi xảy ra khi khôi phục chương');
        });
    }

    function bulkForceDelete(chapterIds) {
        const promises = chapterIds.map(chapterId => {
            const formData = new FormData();
            formData.append('<?php echo \Config::get('security.csrf_token_key'); ?>', '<?php echo \Security::fetch_token(); ?>');
            
            return fetch(`<?php echo Uri::base(); ?>admin/chapters/force-delete/${chapterId}`, {
                method: 'POST',
                body: formData
            });
        });

        Promise.all(promises)
        .then(responses => Promise.all(responses.map(r => r.json())))
        .then(results => {
            const successCount = results.filter(r => r.success).length;
            showAlert('success', `Đã xóa vĩnh viễn ${successCount}/${chapterIds.length} chương`);
            
            // Remove successful rows
            results.forEach((result, index) => {
                if (result.success) {
                    const row = document.querySelector(`tr[data-chapter-id="${chapterIds[index]}"]`);
                    if (row) row.remove();
                }
            });
            
            updateBulkActions();
        })
        .catch(error => {
            showAlert('error', 'Có lỗi xảy ra khi xóa chương');
        });
    }

    function showAlert(type, message) {
        // Remove existing alerts in card body
        const cardBody = document.querySelector('.card-body');
        const existing = cardBody ? cardBody.querySelectorAll('.alert') : [];
        existing.forEach(el => el.remove());

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        if (cardBody) {
            cardBody.insertBefore(alertDiv, cardBody.firstChild);
        } else {
            document.body.appendChild(alertDiv);
        }

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>

<style>
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    border-radius: 0.375rem;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.25rem;
}

.badge {
    font-size: 0.75em;
}

#bulk-actions {
    border-left: 4px solid #007bff;
}

.position-fixed {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
