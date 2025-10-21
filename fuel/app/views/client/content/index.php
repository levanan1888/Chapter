<div class="container">
	<!-- Hero Section -->
	<div class="row mb-5">
		<div class="col-12">
			<div class="jumbotron bg-primary text-white rounded p-5 text-center">
				<h1 class="display-4 mb-3">
					<i class="fas fa-book-open me-3"></i>
					Chào mừng đến với ComicHub
				</h1>
				<p class="lead">Khám phá thế giới truyện tranh đa dạng và phong phú</p>
				<a href="<?php echo Uri::base(); ?>client/stories" class="btn btn-light btn-lg">
					<i class="fas fa-list me-2"></i>Xem tất cả truyện
				</a>
			</div>
		</div>
	</div>

	<!-- Latest Stories -->
	<?php if (isset($latest_stories) && !empty($latest_stories)): ?>
	<div class="row mb-5">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center mb-4">
				<h2 class="h3 mb-0">
					<i class="fas fa-clock me-2 text-primary"></i>
					Truyện mới nhất
				</h2>
				<a href="<?php echo Uri::base(); ?>client/stories?sort=latest" class="btn btn-outline-primary">
					Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
				</a>
			</div>
			
			<div class="row">
				<?php foreach ($latest_stories as $story): ?>
				<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
					<div class="card story-card h-100">
						<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>">
							<img src="<?php echo $story->cover_image ? Uri::base() . $story->cover_image : 'https://via.placeholder.com/200x300'; ?>" 
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
							<p class="card-text text-muted small">
								<?php echo $story->author_name ?? 'Unknown'; ?>
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
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Hot Stories -->
	<?php if (isset($hot_stories) && !empty($hot_stories)): ?>
	<div class="row mb-5">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center mb-4">
				<h2 class="h3 mb-0">
					<i class="fas fa-fire me-2 text-danger"></i>
					Truyện hot
				</h2>
				<a href="<?php echo Uri::base(); ?>client/stories?sort=popular" class="btn btn-outline-danger">
					Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
				</a>
			</div>
			
			<div class="row">
				<?php foreach ($hot_stories as $story): ?>
				<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
					<div class="card story-card h-100">
						<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>">
							<img src="<?php echo $story->cover_image ? Uri::base() . $story->cover_image : 'https://via.placeholder.com/200x300'; ?>" 
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
							<p class="card-text text-muted small">
								<?php echo $story->author_name ?? 'Unknown'; ?>
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
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Most Viewed Stories -->
	<?php if (isset($most_viewed_stories) && !empty($most_viewed_stories)): ?>
	<div class="row mb-5">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center mb-4">
				<h2 class="h3 mb-0">
					<i class="fas fa-chart-line me-2 text-success"></i>
					Được xem nhiều nhất
				</h2>
				<a href="<?php echo Uri::base(); ?>client/stories?sort=view" class="btn btn-outline-success">
					Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
				</a>
			</div>
			
			<div class="row">
				<?php foreach ($most_viewed_stories as $story): ?>
				<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
					<div class="card story-card h-100">
						<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>">
							<img src="<?php echo $story->cover_image ? Uri::base() . $story->cover_image : 'https://via.placeholder.com/200x300'; ?>" 
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
							<p class="card-text text-muted small">
								<?php echo $story->author_name ?? 'Unknown'; ?>
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
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Categories -->
	<?php if (isset($categories) && !empty($categories)): ?>
	<div class="row mb-5">
		<div class="col-12">
			<h2 class="h3 mb-4">
				<i class="fas fa-th-large me-2 text-info"></i>
				Danh mục truyện
			</h2>
			
			<div class="row">
				<?php foreach ($categories as $category): ?>
				<div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
					<a href="<?php echo Uri::base(); ?>client/category/<?php echo $category->slug; ?>" 
					   class="text-decoration-none">
						<div class="card text-center h-100">
							<div class="card-body">
								<div class="category-icon mb-2" style="font-size: 2rem; color: <?php echo $category->color; ?>">
									<i class="fas fa-tag"></i>
								</div>
								<h6 class="card-title mb-1"><?php echo $category->name; ?></h6>
								<small class="text-muted"><?php echo $category->story_count ?? 0; ?> truyện</small>
							</div>
						</div>
					</a>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>
