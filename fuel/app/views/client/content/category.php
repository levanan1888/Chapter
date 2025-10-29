<div class="container">
	<!-- Breadcrumb -->
	<nav aria-label="breadcrumb" class="mb-4">
		<ol class="breadcrumb" style="background: transparent;">
			<li class="breadcrumb-item">
				<a href="<?php echo Uri::base(); ?>client" class="text-decoration-none" style="color: var(--primary-color);">
					<i class="fas fa-home me-1"></i>Trang chủ
				</a>
			</li>
			<li class="breadcrumb-item active" aria-current="page" style="color: #94a3b8;">
				Thể loại: <?php echo Security::htmlentities($category->name); ?>
			</li>
		</ol>
	</nav>

	<!-- Header -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center flex-wrap">
				<div>
					<h1 class="h2 mb-2">
						<i class="fas fa-tags me-2" style="color: var(--primary-color);"></i>
						<?php echo Security::htmlentities($category->name); ?>
					</h1>
					<p class="mb-0" style="color: #94a3b8;">
						Tổng cộng <?php echo number_format($total_stories); ?> truyện trong thể loại này
					</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Stories Grid -->
	<?php if (!empty($stories)): ?>
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
						<?php if (!empty($story->author_name)): ?>
						<p class="card-text text-muted small">
							<?php echo Security::htmlentities($story->author_name); ?>
						</p>
						<?php endif; ?>
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
			<nav aria-label="Category pagination">
				<ul class="pagination justify-content-center">
					<!-- Previous Page -->
					<?php if ($current_page > 1): ?>
						<li class="page-item">
							<a class="page-link" href="<?php echo Uri::base(); ?>client/category/<?php echo $category->slug; ?>?page=<?php echo $current_page - 1; ?>">
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
							<a class="page-link" href="<?php echo Uri::base(); ?>client/category/<?php echo $category->slug; ?>?page=1">1</a>
						</li>
						<?php if ($start_page > 2): ?>
							<li class="page-item disabled">
								<span class="page-link">...</span>
							</li>
						<?php endif; ?>
					<?php endif; ?>

					<?php for ($i = $start_page; $i <= $end_page; $i++): ?>
						<li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
							<a class="page-link" href="<?php echo Uri::base(); ?>client/category/<?php echo $category->slug; ?>?page=<?php echo $i; ?>">
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
							<a class="page-link" href="<?php echo Uri::base(); ?>client/category/<?php echo $category->slug; ?>?page=<?php echo $total_pages; ?>">
								<?php echo $total_pages; ?>
							</a>
						</li>
					<?php endif; ?>

					<!-- Next Page -->
					<?php if ($current_page < $total_pages): ?>
						<li class="page-item">
							<a class="page-link" href="<?php echo Uri::base(); ?>client/category/<?php echo $category->slug; ?>?page=<?php echo $current_page + 1; ?>">
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
	<!-- No Stories -->
	<div class="card">
		<div class="card-body text-center py-5">
			<i class="fas fa-book-open fa-3x text-muted mb-3"></i>
			<h4 class="text-white mb-3">Chưa có truyện nào</h4>
			<p class="text-muted mb-4" style="color: #94a3b8;">
				Thể loại này chưa có truyện nào được đăng.
			</p>
			<a href="<?php echo Uri::base(); ?>client" class="btn btn-primary">
				<i class="fas fa-home me-2"></i>Về trang chủ
			</a>
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

.story-card {
	background: rgba(30, 41, 59, 0.6);
	border: none;
}

.card-title a {
	color: #fff;
}

.text-muted {
	color: #94a3b8 !important;
}
</style>

