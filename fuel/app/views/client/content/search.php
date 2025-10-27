<div class="container">
	<!-- Header -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center flex-wrap">
				<div>
					<h1 class="h2 mb-2">
						<i class="fas fa-search me-2" style="color: var(--primary-color);"></i>
						Kết quả tìm kiếm
					</h1>
					<p class="mb-0" style="color: #94a3b8;">
						<?php if (!empty($keyword)): ?>
							Tìm thấy <?php echo number_format($total_stories); ?> kết quả cho "<strong class="text-white"><?php echo Security::htmlentities($keyword); ?></strong>"
						<?php else: ?>
							Nhập từ khóa để tìm kiếm
						<?php endif; ?>
					</p>
				</div>
				<a href="<?php echo Uri::base(); ?>client" class="btn btn-outline-primary mt-3 mt-md-0">
					<i class="fas fa-arrow-left me-2"></i>Về trang chủ
				</a>
			</div>
		</div>
	</div>

	<!-- Search Form -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<form method="GET" action="<?php echo Uri::base(); ?>client/search" class="row g-3">
						<div class="col-md-10">
							<input type="text" 
								   class="form-control" 
								   name="q" 
								   placeholder="Tìm kiếm theo tên truyện, tác giả..." 
								   value="<?php echo Security::htmlentities($keyword); ?>"
								   style="background: rgba(30, 41, 59, 0.6); border: 2px solid rgba(51, 65, 85, 0.5); color: #fff; border-radius: 12px;">
						</div>
						<div class="col-md-2">
							<button type="submit" class="btn btn-primary w-100">
								<i class="fas fa-search me-2"></i>Tìm kiếm
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Search Results -->
	<?php if (!empty($keyword)): ?>
		<?php if (isset($stories) && !empty($stories)): ?>
		<div class="row">
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
		</div>

		<!-- Pagination -->
		<?php if (isset($total_pages) && $total_pages > 1): ?>
		<div class="row mt-4">
			<div class="col-12">
				<nav aria-label="Search results pagination">
					<ul class="pagination justify-content-center">
						<!-- Previous Page -->
						<?php if ($current_page > 1): ?>
							<li class="page-item">
								<a class="page-link" href="<?php echo Uri::base(); ?>client/search?q=<?php echo urlencode($keyword); ?>&page=<?php echo $current_page - 1; ?>">
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
								<a class="page-link" href="<?php echo Uri::base(); ?>client/search?q=<?php echo urlencode($keyword); ?>&page=1">1</a>
							</li>
							<?php if ($start_page > 2): ?>
								<li class="page-item disabled">
									<span class="page-link">...</span>
								</li>
							<?php endif; ?>
						<?php endif; ?>

						<?php for ($i = $start_page; $i <= $end_page; $i++): ?>
							<li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
								<a class="page-link" href="<?php echo Uri::base(); ?>client/search?q=<?php echo urlencode($keyword); ?>&page=<?php echo $i; ?>">
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
								<a class="page-link" href="<?php echo Uri::base(); ?>client/search?q=<?php echo urlencode($keyword); ?>&page=<?php echo $total_pages; ?>">
									<?php echo $total_pages; ?>
								</a>
							</li>
						<?php endif; ?>

						<!-- Next Page -->
						<?php if ($current_page < $total_pages): ?>
							<li class="page-item">
								<a class="page-link" href="<?php echo Uri::base(); ?>client/search?q=<?php echo urlencode($keyword); ?>&page=<?php echo $current_page + 1; ?>">
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

		<?php else: ?>
		<!-- No Results -->
		<div class="card">
			<div class="card-body text-center py-5">
				<i class="fas fa-search fa-4x mb-3" style="color: var(--primary-color); opacity: 0.5;"></i>
				<h4 class="text-white mb-3">Không tìm thấy kết quả</h4>
				<p class="text-muted mb-4" style="color: #94a3b8;">
					Không tìm thấy truyện nào với từ khóa "<strong class="text-white"><?php echo Security::htmlentities($keyword); ?></strong>"
				</p>
				<div class="d-flex gap-2 justify-content-center flex-wrap">
					<a href="<?php echo Uri::base(); ?>client" class="btn btn-primary">
						<i class="fas fa-home me-2"></i>Về trang chủ
					</a>
					<a href="<?php echo Uri::base(); ?>client/stories" class="btn btn-outline-primary">
						<i class="fas fa-list me-2"></i>Xem tất cả truyện
					</a>
				</div>
			</div>
		</div>
		<?php endif; ?>
	<?php else: ?>
		<!-- Empty State -->
		<div class="card">
			<div class="card-body text-center py-5">
				<i class="fas fa-search fa-4x mb-3" style="color: var(--primary-color); opacity: 0.5;"></i>
				<h4 class="text-white mb-3">Tìm kiếm truyện của bạn</h4>
				<p class="text-muted mb-4" style="color: #94a3b8;">
					Nhập từ khóa vào ô tìm kiếm ở trên để bắt đầu tìm kiếm
				</p>
			</div>
		</div>
	<?php endif; ?>
</div>

<style>
.container {
	color: #e2e8f0;
}

h1, h2, h3, h4, h5, h6 {
	color: #fff;
}

.btn-outline-primary {
	border-color: var(--primary-color);
	color: var(--primary-color);
	background: transparent;
}

.btn-outline-primary:hover {
	background: var(--primary-color);
	border-color: var(--primary-color);
	color: white;
}

.btn-primary {
	background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
	border-color: var(--primary-color);
	color: white;
}

.btn-primary:hover {
	transform: translateY(-2px);
	box-shadow: 0 8px 20px 0 rgba(139, 126, 248, 0.5);
}

.story-card {
	background: rgba(30, 41, 59, 0.6);
	backdrop-filter: blur(10px);
	border: none;
}

.story-cover {
	height: 280px;
}

.card-title a {
	color: #fff;
}

.text-muted {
	color: #94a3b8 !important;
}
</style>

