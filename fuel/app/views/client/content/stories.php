<div class="container">
	<!-- Header -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h1 class="h2 mb-2">
						<i class="fas fa-list me-2 text-primary"></i>
						Danh sách Truyện
					</h1>
					<p class="text-muted mb-0">Tổng cộng <?php echo number_format($total_stories); ?> truyện</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Filters -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<form method="GET" action="<?php echo Uri::base(); ?>client/stories" class="row g-3">
						<!-- Category Filter -->
						<div class="col-md-3">
							<label for="category" class="form-label">Thể loại</label>
							<select class="form-select" id="category" name="category">
								<option value="">Tất cả thể loại</option>
								<?php if (isset($categories) && !empty($categories)): ?>
									<?php foreach ($categories as $category): ?>
										<option value="<?php echo $category->id; ?>" 
												<?php echo (isset($filter_params['category']) && $filter_params['category'] == $category->id) ? 'selected' : ''; ?>>
											<?php echo Security::htmlentities($category->name); ?>
										</option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</div>

						<!-- Author Filter -->
						<div class="col-md-3">
							<label for="author" class="form-label">Tác giả</label>
							<select class="form-select" id="author" name="author">
								<option value="">Tất cả tác giả</option>
								<?php if (isset($authors) && !empty($authors)): ?>
									<?php foreach ($authors as $author): ?>
										<option value="<?php echo $author->id; ?>" 
												<?php echo (isset($filter_params['author']) && $filter_params['author'] == $author->id) ? 'selected' : ''; ?>>
											<?php echo Security::htmlentities($author->name); ?>
										</option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</div>

						<!-- Status Filter -->
						<div class="col-md-2">
							<label for="status" class="form-label">Trạng thái</label>
							<select class="form-select" id="status" name="status">
								<option value="">Tất cả</option>
								<option value="ongoing" <?php echo (isset($filter_params['status']) && $filter_params['status'] == 'ongoing') ? 'selected' : ''; ?>>Đang cập nhật</option>
								<option value="completed" <?php echo (isset($filter_params['status']) && $filter_params['status'] == 'completed') ? 'selected' : ''; ?>>Hoàn thành</option>
								<option value="paused" <?php echo (isset($filter_params['status']) && $filter_params['status'] == 'paused') ? 'selected' : ''; ?>>Tạm dừng</option>
							</select>
						</div>

						<!-- Sort Filter -->
						<div class="col-md-2">
							<label for="sort" class="form-label">Sắp xếp</label>
							<select class="form-select" id="sort" name="sort">
								<option value="latest" <?php echo (isset($filter_params['sort']) && $filter_params['sort'] == 'latest') ? 'selected' : ''; ?>>Mới nhất</option>
								<option value="popular" <?php echo (isset($filter_params['sort']) && $filter_params['sort'] == 'popular') ? 'selected' : ''; ?>>Phổ biến</option>
								<option value="view" <?php echo (isset($filter_params['sort']) && $filter_params['sort'] == 'view') ? 'selected' : ''; ?>>Xem nhiều</option>
							</select>
						</div>

						<!-- Filter Buttons -->
						<div class="col-md-2 d-flex align-items-end">
							<div class="btn-group w-100" role="group">
								<button type="submit" class="btn btn-primary">
									<i class="fas fa-filter me-1"></i>Lọc
								</button>
								<a href="<?php echo Uri::base(); ?>client/stories" class="btn btn-outline-secondary">
									<i class="fas fa-times me-1"></i>Xóa
								</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Stories Grid -->
	<div class="row">
		<?php if (isset($stories) && !empty($stories)): ?>
			<?php foreach ($stories as $story): ?>
				<?php if (isset($story->is_visible) && $story->is_visible == 1): ?>
				<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
					<div class="card story-card h-100">
						<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>">
							<img src="<?php echo $story->cover_image ? Uri::base() . $story->cover_image : Uri::base() . 'assets/img/default-story-cover.svg'; ?>" 
								 class="card-img-top story-cover" 
								 alt="<?php echo Security::htmlentities($story->title); ?>">
						</a>
						<div class="card-body d-flex flex-column">
							<h5 class="card-title">
								<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>" 
								   class="text-decoration-none text-dark">
									<?php echo Security::htmlentities($story->title); ?>
								</a>
							</h5>
							<p class="card-text text-muted small">
								<?php echo Security::htmlentities($story->author_name ?? 'Unknown'); ?>
							</p>
							<div class="mt-auto">
								<div class="d-flex justify-content-between align-items-center mb-2">
									<span class="status-badge status-<?php echo $story->status; ?>">
										<?php 
										switch($story->status) {
											case 'ongoing': echo 'Đang cập nhật'; break;
											case 'completed': echo 'Hoàn thành'; break;
											case 'paused': echo 'Tạm dừng'; break;
											default: echo $story->status;
										}
										?>
									</span>
									<small class="text-muted">
										<i class="fas fa-eye me-1"></i>
										<?php echo number_format($story->views); ?>
									</small>
								</div>
								<?php 
								// Lấy categories của story
								$story_categories = $story->get_categories();
								if (!empty($story_categories)): 
								?>
								<div class="mb-2">
									<?php foreach (array_slice($story_categories, 0, 2) as $category): ?>
										<span class="category-badge me-1"><?php echo Security::htmlentities($category->name); ?></span>
									<?php endforeach; ?>
									<?php if (count($story_categories) > 2): ?>
										<small class="text-muted">+<?php echo count($story_categories) - 2; ?> khác</small>
									<?php endif; ?>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="col-12">
				<div class="text-center py-5">
					<i class="fas fa-search fa-3x text-muted mb-3"></i>
					<h4 class="text-muted">Không tìm thấy truyện nào</h4>
					<p class="text-muted">Hãy thử thay đổi bộ lọc hoặc tìm kiếm khác.</p>
					<a href="<?php echo Uri::base(); ?>client/stories" class="btn btn-primary">
						<i class="fas fa-refresh me-2"></i>Xem tất cả truyện
					</a>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<!-- Pagination -->
	<?php if (isset($total_pages) && $total_pages > 1): ?>
	<div class="row mt-4">
		<div class="col-12">
			<nav aria-label="Stories pagination">
				<ul class="pagination justify-content-center">
					<!-- Previous Page -->
					<?php if ($current_page > 1): ?>
						<li class="page-item">
							<a class="page-link" href="<?php echo Uri::base(); ?>client/stories?<?php echo http_build_query(array_merge($_GET, array('page' => $current_page - 1))); ?>">
								<i class="fas fa-chevron-left"></i>
							</a>
						</li>
					<?php else: ?>
						<li class="page-item disabled">
							<span class="page-link">
								<i class="fas fa-chevron-left"></i>
							</span>
						</li>
					<?php endif; ?>

					<!-- Page Numbers -->
					<?php
					$start_page = max(1, $current_page - 2);
					$end_page = min($total_pages, $current_page + 2);
					
					if ($start_page > 1): ?>
						<li class="page-item">
							<a class="page-link" href="<?php echo Uri::base(); ?>client/stories?<?php echo http_build_query(array_merge($_GET, array('page' => 1))); ?>">1</a>
						</li>
						<?php if ($start_page > 2): ?>
							<li class="page-item disabled">
								<span class="page-link">...</span>
							</li>
						<?php endif; ?>
					<?php endif; ?>

					<?php for ($i = $start_page; $i <= $end_page; $i++): ?>
						<li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
							<a class="page-link" href="<?php echo Uri::base(); ?>client/stories?<?php echo http_build_query(array_merge($_GET, array('page' => $i))); ?>">
								<?php echo $i; ?>
							</a>
						</li>
					<?php endfor; ?>

					<?php if ($end_page < $total_pages): ?>
						<?php if ($end_page < $total_pages - 1): ?>
							<li class="page-item disabled">
								<span class="page-link">...</span>
							</li>
						<?php endif; ?>
						<li class="page-item">
							<a class="page-link" href="<?php echo Uri::base(); ?>client/stories?<?php echo http_build_query(array_merge($_GET, array('page' => $total_pages))); ?>">
								<?php echo $total_pages; ?>
							</a>
						</li>
					<?php endif; ?>

					<!-- Next Page -->
					<?php if ($current_page < $total_pages): ?>
						<li class="page-item">
							<a class="page-link" href="<?php echo Uri::base(); ?>client/stories?<?php echo http_build_query(array_merge($_GET, array('page' => $current_page + 1))); ?>">
								<i class="fas fa-chevron-right"></i>
							</a>
						</li>
					<?php else: ?>
						<li class="page-item disabled">
							<span class="page-link">
								<i class="fas fa-chevron-right"></i>
							</span>
						</li>
					<?php endif; ?>
				</ul>
			</nav>
		</div>
	</div>
	<?php endif; ?>
</div>

<style>
.story-card {
	transition: transform 0.3s ease;
	border: none;
	box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.story-card:hover {
	transform: translateY(-5px);
	box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.story-cover {
	height: 200px;
	object-fit: cover;
	border-radius: 8px 8px 0 0;
}

.category-badge {
	background: linear-gradient(45deg, var(--primary-color), var(--info-color));
	color: white;
	padding: 4px 8px;
	border-radius: 12px;
	font-size: 0.8rem;
	text-decoration: none;
	display: inline-block;
}

.status-badge {
	padding: 4px 8px;
	border-radius: 12px;
	font-size: 0.8rem;
	font-weight: 500;
}

.status-ongoing {
	background-color: var(--success-color);
	color: white;
}

.status-completed {
	background-color: var(--info-color);
	color: white;
}

.status-paused {
	background-color: var(--warning-color);
	color: black;
}

.pagination .page-link {
	border-radius: 6px;
	margin: 0 2px;
	border: 1px solid #dee2e6;
}

.pagination .page-item.active .page-link {
	background-color: var(--primary-color);
	border-color: var(--primary-color);
}

.pagination .page-link:hover {
	background-color: #e9ecef;
	border-color: #dee2e6;
}

/* Dark mode support */
.dark-mode .story-card {
	background-color: #2d2d2d;
	color: #e0e0e0;
}

.dark-mode .card-body {
	background-color: #2d2d2d;
	color: #e0e0e0;
}

.dark-mode .text-dark {
	color: #e0e0e0 !important;
}
</style>
