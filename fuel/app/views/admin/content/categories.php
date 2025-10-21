<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-tags me-2"></i>Quản lý Danh mục
	</h2>
	<div>
		<a href="<?php echo Uri::base(); ?>admin/categories/trash" class="btn btn-outline-warning me-2">
			<i class="fas fa-trash me-2"></i>Thùng rác
		</a>
		<a href="<?php echo Uri::base(); ?>admin/categories/add" class="btn btn-primary">
			<i class="fas fa-plus me-2"></i>Thêm danh mục mới
		</a>
	</div>
</div>

<!-- Search & Filter -->
<form method="get" class="row g-3 mb-3">
	<div class="col-md-6">
		<input type="text" class="form-control" name="q" placeholder="Tìm kiếm theo tên hoặc slug" value="<?php echo isset($q) ? htmlspecialchars($q) : ''; ?>">
	</div>
	<div class="col-md-3">
		<select class="form-select" name="status">
			<?php $curStatus = isset($status) ? $status : 'active'; ?>
			<option value="all" <?php echo $curStatus === 'all' ? 'selected' : ''; ?>>Tất cả</option>
			<option value="active" <?php echo $curStatus === 'active' ? 'selected' : ''; ?>>Đang hoạt động</option>
			<option value="inactive" <?php echo $curStatus === 'inactive' ? 'selected' : ''; ?>>Đã ẩn</option>
		</select>
	</div>
	<div class="col-md-3 d-grid">
		<button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-2"></i>Tìm kiếm</button>
	</div>
</form>

<!-- Categories Grid -->
<div class="row">
	<?php if (isset($categories) && !empty($categories)): ?>
		<?php foreach ($categories as $category): ?>
		<div class="col-lg-4 col-md-6 mb-4">
			<div class="card h-100">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-start mb-3">
						<div class="d-flex align-items-center">
							<div class="category-icon me-3" style="width: 40px; height: 40px; background-color: <?php echo $category->color; ?>; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
								<i class="fas fa-tag text-white"></i>
							</div>
							<div>
								<h5 class="mb-0"><?php echo $category->name; ?></h5>
								<small class="text-muted"><?php echo $category->slug; ?></small>
							</div>
						</div>
						<div class="dropdown">
							<button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="<?php echo Uri::base(); ?>admin/categories/edit/<?php echo $category->id; ?>">
									<i class="fas fa-edit me-2"></i>Sửa
								</a></li>
								<li><a class="dropdown-item text-danger" href="#" 
									   onclick="deleteCategory(<?php echo $category->id; ?>, '<?php echo htmlspecialchars($category->name); ?>')">
									<i class="fas fa-trash me-2"></i>Xóa
								</a></li>
							</ul>
						</div>
					</div>
					
					<?php if ($category->description): ?>
						<p class="text-muted mb-3"><?php echo $category->description; ?></p>
					<?php endif; ?>
					
					<div class="d-flex justify-content-between align-items-center">
						<span class="badge bg-info"><?php echo $category->story_count ?? 0; ?> truyện</span>
						<small class="text-muted"><?php echo date('d/m/Y', strtotime($category->created_at)); ?></small>
					</div>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	<?php else: ?>
		<div class="col-12">
			<div class="text-center py-5">
				<i class="fas fa-tags fa-3x text-muted mb-3"></i>
				<h5 class="text-muted">Không có danh mục phù hợp</h5>
				<p class="text-muted">Thử thay đổi từ khóa hoặc bộ lọc.</p>
				<a href="<?php echo Uri::base(); ?>admin/categories/add" class="btn btn-primary">
					<i class="fas fa-plus me-2"></i>Thêm danh mục mới
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
        'status' => isset($status) ? $status : 'active',
    );
?>
<nav aria-label="Category pagination" class="mt-3">
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
function deleteCategory(id, name) {
	if (confirm('Bạn có chắc chắn muốn xóa danh mục "' + name + '"?')) {
		const body = new URLSearchParams();
		body.append('fuel_csrf_token', '<?php echo Security::fetch_token(); ?>');

		fetch('<?php echo Uri::base(); ?>admin/categories/delete/' + id, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'X-Requested-With': 'XMLHttpRequest'
			},
			body: body.toString()
		})
		.then(response => response.json())
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
			alert('Có lỗi xảy ra khi xóa danh mục');
		});
	}
}
</script>
