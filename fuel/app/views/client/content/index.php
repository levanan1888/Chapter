<div class="container" style="position: relative; z-index: 1;">
	<!-- Hero Section -->
	<div class="hero-section text-center">
		<div class="container position-relative" style="z-index: 2;">
			<div class="mb-4" style="font-size: 4rem; opacity: 0.9;">
				<i class="fas fa-book-open"></i>
			</div>
			<h1 class="display-4 mb-4 fw-bold" style="background: linear-gradient(135deg, #8b7ef8 0%, #ff9f66 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
				Chào mừng đến với An - NetTruyen
			</h1>
			<p class="lead mb-5" style="font-size: 1.25rem; color: #cbd5e1; max-width: 700px; margin: 0 auto;">
				Khám phá thế giới truyện tranh đa dạng và phong phú
			</p>
			<a href="<?php echo Uri::base(); ?>client/stories" class="btn btn-lg px-5 py-3 fs-5 rounded-pill shadow-lg" style="background: linear-gradient(135deg, #8b7ef8 0%, #6b5ddc 100%); border: none; color: white;">
				<i class="fas fa-list me-2"></i>Xem tất cả truyện
			</a>
		</div>
	</div>

	<!-- Latest Stories -->
	<?php if (isset($latest_stories) && !empty($latest_stories)): ?>
	<div class="row mb-5">
		<div class="col-12">
			<div class="section-header d-flex justify-content-between align-items-end flex-wrap">
				<div>
					<h2 class="mb-2">
						<i class="fas fa-clock me-2" style="color: var(--primary-color);"></i>
						Truyện mới cập nhật
					</h2>
					<p class="mb-0" style="color: #94a3b8;">Những bộ truyện mới nhất được đăng tải</p>
				</div>
				<a href="<?php echo Uri::base(); ?>client/stories?sort=latest" class="btn btn-outline-primary rounded-pill px-4 mt-3 mt-md-0">
					Xem tất cả <i class="fas fa-arrow-right ms-2"></i>
				</a>
			</div>
			
			<div class="row">
				<?php foreach ($latest_stories as $story): ?>
					<?php if (isset($story->is_visible) && $story->is_visible == 1): ?>
					<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
						<div class="card story-card h-100">
							<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>">
								<img src="<?php echo $story->cover_image ? Uri::base() . $story->cover_image : Uri::base() . 'assets/img/default-story-cover.svg'; ?>" 
									 class="card-img-top story-cover" 
									 alt="<?php echo $story->title; ?>">
							</a>
							<div class="card-body d-flex flex-column">
								<h5 class="card-title">
									<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>" 
									   class="text-decoration-none text-dark">
										<?php echo $story->title; ?>
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
									<?php if (!empty($story->categories)): ?>
									<div class="mb-2">
										<?php foreach (array_slice($story->categories, 0, 2) as $category): ?>
											<span class="category-badge me-1"><?php echo $category->name; ?></span>
										<?php endforeach; ?>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Hot Stories -->
	<?php if (isset($hot_stories) && !empty($hot_stories)): ?>
	<div class="row mb-5">
		<div class="col-12">
			<div class="section-header d-flex justify-content-between align-items-end flex-wrap">
				<div>
					<h2 class="mb-2">
						<i class="fas fa-fire me-2" style="color: #ff6b6b;"></i>
						Truyện hot
					</h2>
					<p class="mb-0" style="color: #94a3b8;">Truyện được mọi người yêu thích</p>
				</div>
				<a href="<?php echo Uri::base(); ?>client/stories?sort=popular" class="btn btn-outline-primary rounded-pill px-4 mt-3 mt-md-0" style="border-color: #ff6b6b; color: #ff6b6b;">
					Xem tất cả <i class="fas fa-arrow-right ms-2"></i>
				</a>
			</div>
			
			<div class="row">
				<?php foreach ($hot_stories as $story): ?>
					<?php if (isset($story->is_visible) && $story->is_visible == 1): ?>
					<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
						<div class="card story-card h-100 position-relative">
							<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>" class="position-relative d-block">
								<img src="<?php echo $story->cover_image ? Uri::base() . $story->cover_image : Uri::base() . 'assets/img/default-story-cover.svg'; ?>" 
									 class="card-img-top story-cover" 
									 alt="<?php echo Security::htmlentities($story->title); ?>">
								<span class="hot-badge">
									<i class="fas fa-fire me-1"></i>Hot
								</span>
							</a>
							<div class="card-body d-flex flex-column p-3">
								<h5 class="card-title mb-2">
									<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>" 
									   class="text-decoration-none">
										<?php echo Security::htmlentities($story->title); ?>
									</a>
								</h5>
								<p class="card-text text-muted small mb-2">
									<?php if (!empty($story->author_name)): ?>
									<i class="fas fa-user me-1"></i><?php echo Security::htmlentities($story->author_name); ?>
									<?php endif; ?>
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
										<span class="view-count">
											<i class="fas fa-eye"></i>
											<?php echo number_format($story->views); ?>
										</span>
									</div>
									<?php if (!empty($story->categories)): ?>
									<div class="mb-2">
										<?php foreach (array_slice($story->categories, 0, 2) as $category): ?>
											<span class="category-badge me-1"><?php echo Security::htmlentities($category->name); ?></span>
										<?php endforeach; ?>
										<?php if (count($story->categories) > 2): ?>
											<small class="text-muted">+<?php echo count($story->categories) - 2; ?></small>
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
		</div>
	</div>
	<?php endif; ?>

	<!-- Featured Stories -->
	<?php if (isset($featured_stories) && !empty($featured_stories)): ?>
	<div class="row mb-5">
		<div class="col-12">
			<div class="section-header d-flex justify-content-between align-items-end flex-wrap">
				<div>
					<h2 class="mb-2">
						<i class="fas fa-trophy me-2" style="color: #fbbf24;"></i>
						Truyện nổi bật
					</h2>
					<p class="mb-0" style="color: #94a3b8;">Những bộ truyện có lượt xem cao nhất</p>
				</div>
				<a href="<?php echo Uri::base(); ?>client/stories?sort=view" class="btn btn-outline-primary rounded-pill px-4 mt-3 mt-md-0" style="border-color: #fbbf24; color: #fbbf24;">
					Xem tất cả <i class="fas fa-arrow-right ms-2"></i>
				</a>
			</div>
			
			<div class="row">
				<?php foreach ($featured_stories as $story): ?>
					<?php if (isset($story->is_visible) && $story->is_visible == 1): ?>
					<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
						<div class="card story-card h-100 position-relative">
							<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>" class="position-relative d-block">
								<img src="<?php echo $story->cover_image ? Uri::base() . $story->cover_image : Uri::base() . 'assets/img/default-story-cover.svg'; ?>" 
									 class="card-img-top story-cover" 
									 alt="<?php echo Security::htmlentities($story->title); ?>">
								<span class="rating-badge">
									4.5
								</span>
							</a>
							<div class="card-body d-flex flex-column p-3">
								<h5 class="card-title mb-2">
									<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>" 
									   class="text-decoration-none">
										<?php echo Security::htmlentities($story->title); ?>
									</a>
								</h5>
								<p class="card-text text-muted small mb-2">
									<?php if (!empty($story->author_name)): ?>
									<i class="fas fa-user me-1"></i><?php echo Security::htmlentities($story->author_name); ?>
									<?php endif; ?>
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
										<span class="view-count">
											<i class="fas fa-eye"></i>
											<?php echo number_format($story->views); ?>
										</span>
									</div>
									<?php if (!empty($story->categories)): ?>
									<div class="mb-2">
										<?php foreach (array_slice($story->categories, 0, 2) as $category): ?>
											<span class="category-badge me-1"><?php echo Security::htmlentities($category->name); ?></span>
										<?php endforeach; ?>
										<?php if (count($story->categories) > 2): ?>
											<small class="text-muted">+<?php echo count($story->categories) - 2; ?></small>
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
		</div>
	</div>
	<?php endif; ?>

</div>

<style>
	/* Additional Homepage Styles */
	.card {
		overflow: hidden;
	}

	.card-img-top {
		position: relative;
		overflow: hidden;
	}

	.story-card:hover {
		transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
	}

	/* View count and category styling */
	.card-text.text-muted {
		color: #94a3b8;
		font-size: 0.875rem;
	}

	.text-muted {
		color: #94a3b8 !important;
	}

	/* Animation for cards */
	@keyframes fadeInUp {
		from {
			opacity: 0;
			transform: translateY(30px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.story-card {
		animation: fadeInUp 0.6s ease-out forwards;
		opacity: 0;
	}

	.story-card:nth-child(1) { animation-delay: 0.1s; }
	.story-card:nth-child(2) { animation-delay: 0.2s; }
	.story-card:nth-child(3) { animation-delay: 0.3s; }
	.story-card:nth-child(4) { animation-delay: 0.4s; }
	.story-card:nth-child(5) { animation-delay: 0.5s; }
	.story-card:nth-child(6) { animation-delay: 0.6s; }
	.story-card:nth-child(7) { animation-delay: 0.7s; }
	.story-card:nth-child(8) { animation-delay: 0.8s; }

	/* Responsive adjustments for homepage */
	@media (max-width: 992px) {
		.story-card:nth-child(n) {
			animation-delay: 0s;
		}
	}
</style>
