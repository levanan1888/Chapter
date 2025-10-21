<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-trash me-2"></i>Sọt rác - Tác giả đã xóa
	</h2>
	<div>
		<a href="<?php echo Uri::base(); ?>admin/authors" class="btn btn-outline-primary me-2">
			<i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
		</a>
		<a href="<?php echo Uri::base(); ?>admin/authors/add" class="btn btn-primary">
			<i class="fas fa-plus me-2"></i>Thêm tác giả mới
		</a>
	</div>
</div>

<!-- Search and Filter Bar -->
<div class="card mb-4">
	<div class="card-body">
		<form method="GET" action="<?php echo Uri::base(); ?>admin/authors/trash" class="row g-3">
			<div class="col-md-6">
				<label for="search" class="form-label">Tìm kiếm</label>
				<input type="text" class="form-control" id="search" name="search" 
					   placeholder="Tìm theo tên tác giả..." 
					   value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
			</div>
			<div class="col-md-4">
				<label for="sort" class="form-label">Sắp xếp</label>
				<select class="form-select" id="sort" name="sort">
					<option value="deleted_at_desc" <?php echo (isset($sort) && $sort === 'deleted_at_desc') ? 'selected' : ''; ?>>Xóa gần nhất</option>
					<option value="deleted_at_asc" <?php echo (isset($sort) && $sort === 'deleted_at_asc') ? 'selected' : ''; ?>>Xóa xa nhất</option>
					<option value="name_asc" <?php echo (isset($sort) && $sort === 'name_asc') ? 'selected' : ''; ?>>Tên A-Z</option>
					<option value="name_desc" <?php echo (isset($sort) && $sort === 'name_desc') ? 'selected' : ''; ?>>Tên Z-A</option>
					<option value="created_at_desc" <?php echo (isset($sort) && $sort === 'created_at_desc') ? 'selected' : ''; ?>>Tạo mới nhất</option>
				</select>
			</div>
			<div class="col-md-2 d-flex align-items-end">
				<button type="submit" class="btn btn-primary me-2">
					<i class="fas fa-search me-1"></i>Tìm kiếm
				</button>
				<a href="<?php echo Uri::base(); ?>admin/authors/trash" class="btn btn-outline-secondary">
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

		<?php if (isset($authors) && !empty($authors)): ?>
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Tên tác giả</th>
							<th>Mô tả</th>
							<th>Số truyện</th>
							<th>Ngày tạo</th>
							<th>Ngày xóa</th>
							<th>Thao tác</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($authors as $author): ?>
						<tr class="table-danger">
							<td>
								<div class="d-flex align-items-center">
									<?php if ($author->avatar && file_exists(DOCROOT . $author->avatar)): ?>
										<img src="<?php echo Uri::base() . $author->avatar; ?>" 
											 class="rounded-circle me-3" 
											 style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #e3e6f0; opacity: 0.6;" 
											 alt="<?php echo htmlspecialchars($author->name); ?>">
									<?php else: ?>
										<div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; opacity: 0.6;">
											<i class="fas fa-user"></i>
										</div>
									<?php endif; ?>
									<div>
										<h6 class="mb-0 text-muted"><?php echo $author->name; ?></h6>
										<small class="text-muted"><?php echo $author->slug; ?></small>
									</div>
								</div>
							</td>
							<td>
								<?php if ($author->description): ?>
									<p class="mb-0 text-truncate text-muted" style="max-width: 200px;" title="<?php echo $author->description; ?>">
										<?php echo $author->description; ?>
									</p>
								<?php else: ?>
									<span class="text-muted">Chưa có mô tả</span>
								<?php endif; ?>
							</td>
							<td>
								<span class="badge bg-secondary"><?php echo $author->story_count ?? 0; ?> truyện</span>
							</td>
							<td class="text-muted"><?php echo date('d/m/Y', strtotime($author->created_at)); ?></td>
							<td class="text-muted">
								<?php if ($author->deleted_at): ?>
									<?php echo date('d/m/Y H:i', strtotime($author->deleted_at)); ?>
								<?php else: ?>
									<span class="text-muted">Chưa xác định</span>
								<?php endif; ?>
							</td>
							<td>
								<div class="btn-group" role="group">
									<form method="POST" action="<?php echo Uri::base(); ?>admin/authors/restore/<?php echo $author->id; ?>" 
										  style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn khôi phục tác giả này?')">
										<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
										<button type="submit" class="btn btn-sm btn-outline-success" title="Khôi phục">
											<i class="fas fa-undo"></i>
										</button>
									</form>
									<form method="POST" action="<?php echo Uri::base(); ?>admin/authors/force_delete/<?php echo $author->id; ?>" 
										  style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn XÓA VĨNH VIỄN tác giả này? Hành động này không thể hoàn tác!')">
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
								<a class="page-link" href="<?php echo Uri::base(); ?>admin/authors/trash?page=<?php echo $current_page - 1; ?><?php echo $query_string; ?>">
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
									<a class="page-link" href="<?php echo Uri::base(); ?>admin/authors/trash?page=<?php echo $i; ?><?php echo $query_string; ?>"><?php echo $i; ?></a>
								</li>
							<?php endif; ?>
						<?php endfor; ?>

						<?php if (isset($current_page) && $current_page < $total_pages): ?>
							<li class="page-item">
								<a class="page-link" href="<?php echo Uri::base(); ?>admin/authors/trash?page=<?php echo $current_page + 1; ?><?php echo $query_string; ?>">
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
				<p class="text-muted">Không có tác giả nào đã bị xóa</p>
				<a href="<?php echo Uri::base(); ?>admin/authors" class="btn btn-primary">
					<i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>
