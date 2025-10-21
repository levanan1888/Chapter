<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-trash me-2"></i>Thùng rác - Danh mục
	</h2>
	<div>
		<a href="<?php echo Uri::base(); ?>admin/categories" class="btn btn-outline-primary me-2">
			<i class="fas fa-arrow-left me-2"></i>Quay lại
		</a>
		<button class="btn btn-danger" onclick="emptyTrash()">
			<i class="fas fa-trash-alt me-2"></i>Dọn sạch thùng rác
		</button>
	</div>
</div>

<!-- Search -->
<form method="get" class="row g-3 mb-3">
	<div class="col-md-8">
		<input type="text" class="form-control" name="q" placeholder="Tìm kiếm theo tên hoặc slug" value="<?php echo isset($q) ? htmlspecialchars($q) : ''; ?>">
	</div>
	<div class="col-md-4 d-grid">
		<button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-2"></i>Tìm kiếm</button>
	</div>
</form>

<!-- Categories Grid -->
<div class="row">
	<?php if (isset($categories) && !empty($categories)): ?>
		<?php foreach ($categories as $category): ?>
		<div class="col-lg-4 col-md-6 mb-4">
			<div class="card h-100 border-warning">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-start mb-3">
						<div class="d-flex align-items-center">
							<div class="category-icon me-3" style="width: 40px; height: 40px; background-color: <?php echo $category->color; ?>; border-radius: 8px; display: flex; align-items: center; justify-content: center; opacity: 0.6;">
								<i class="fas fa-tag text-white"></i>
							</div>
							<div>
								<h5 class="mb-0 text-muted"><?php echo $category->name; ?></h5>
								<small class="text-muted"><?php echo $category->slug; ?></small>
							</div>
						</div>
						<div class="dropdown">
							<button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item text-success" href="#" 
									   onclick="restoreCategory(<?php echo $category->id; ?>, '<?php echo htmlspecialchars($category->name); ?>')">
									<i class="fas fa-undo me-2"></i>Khôi phục
								</a></li>
								<li><a class="dropdown-item text-danger" href="#" 
									   onclick="forceDeleteCategory(<?php echo $category->id; ?>, '<?php echo htmlspecialchars($category->name); ?>')">
									<i class="fas fa-trash-alt me-2"></i>Xóa vĩnh viễn
								</a></li>
							</ul>
						</div>
					</div>
					
					<?php if ($category->description): ?>
						<p class="text-muted mb-3"><?php echo $category->description; ?></p>
					<?php endif; ?>
					
					<div class="d-flex justify-content-between align-items-center">
						<span class="badge bg-warning">Đã xóa</span>
						<small class="text-muted">
							Xóa: <?php echo date('d/m/Y H:i', strtotime($category->deleted_at)); ?>
						</small>
					</div>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	<?php else: ?>
		<div class="col-12">
			<div class="text-center py-5">
				<i class="fas fa-trash fa-3x text-muted mb-3"></i>
				<h5 class="text-muted">Thùng rác trống</h5>
				<p class="text-muted">Không có danh mục nào trong thùng rác</p>
				<a href="<?php echo Uri::base(); ?>admin/categories" class="btn btn-primary">
					<i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
				</a>
			</div>
		</div>
	<?php endif; ?>
</div>

<!-- Pagination -->
<?php if (!empty($total_pages) && $total_pages > 1): ?>
<?php
    $currentPage = isset($page) ? (int) $page : 1;
    $queryBase = array(
        'q' => isset($q) ? $q : '',
    );
?>
<nav aria-label="Trash pagination" class="mt-3">
	<ul class="pagination justify-content-center">
		<li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
			<a class="page-link" href="?<?php echo http_build_query($queryBase + array('page' => max(1, $currentPage - 1))); ?>" tabindex="-1">&laquo;</a>
		</li>
		<?php for ($i = 1; $i <= $total_pages; $i++): ?>
			<li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
				<a class="page-link" href="?<?php echo http_build_query($queryBase + array('page' => $i)); ?>"><?php echo $i; ?></a>
			</li>
		<?php endfor; ?>
		<li class="page-item <?php echo $currentPage >= $total_pages ? 'disabled' : ''; ?>">
			<a class="page-link" href="?<?php echo http_build_query($queryBase + array('page' => min($total_pages, $currentPage + 1))); ?>">&raquo;</a>
		</li>
	</ul>
</nav>
<?php endif; ?>

<script>
function restoreCategory(id, name) {
	if (confirm('Bạn có chắc chắn muốn khôi phục danh mục "' + name + '"?')) {
		// Lấy CSRF token từ meta tag hoặc form
		const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '<?php echo Security::fetch_token(); ?>';
		
		const body = new URLSearchParams();
		body.append('fuel_csrf_token', csrfToken);

		fetch('<?php echo Uri::base(); ?>admin/categories/restore/' + id, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'X-Requested-With': 'XMLHttpRequest'
			},
			body: body.toString()
		})
		.then(response => {
			if (!response.ok) {
				throw new Error('HTTP ' + response.status);
			}
			return response.json();
		})
		.then(data => {
			if (data.success) {
				alert(data.message);
				location.reload();
			} else {
				alert('Lỗi: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Error:', error);
			alert('Có lỗi xảy ra khi khôi phục danh mục. Vui lòng thử lại.');
		});
	}
}

function forceDeleteCategory(id, name) {
	if (confirm('Bạn có chắc chắn muốn XÓA VĨNH VIỄN danh mục "' + name + '"?\n\nHành động này không thể hoàn tác!')) {
		// Lấy CSRF token từ meta tag hoặc form
		const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '<?php echo Security::fetch_token(); ?>';
		
		const body = new URLSearchParams();
		body.append('fuel_csrf_token', csrfToken);

		fetch('<?php echo Uri::base(); ?>admin/categories/force_delete/' + id, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'X-Requested-With': 'XMLHttpRequest'
			},
			body: body.toString()
		})
		.then(response => {
			if (!response.ok) {
				throw new Error('HTTP ' + response.status);
			}
			return response.json();
		})
		.then(data => {
			if (data.success) {
				alert(data.message);
				location.reload();
			} else {
				alert('Lỗi: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Error:', error);
			alert('Có lỗi xảy ra khi xóa vĩnh viễn danh mục. Vui lòng thử lại.');
		});
	}
}

function emptyTrash() {
	if (confirm('Bạn có chắc chắn muốn dọn sạch thùng rác?\n\nTất cả danh mục trong thùng rác sẽ bị xóa vĩnh viễn!')) {
		// TODO: Implement bulk force delete
		alert('Chức năng dọn sạch thùng rác sẽ được thêm sau');
	}
}
</script>
