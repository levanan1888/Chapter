<div class="container">
	<!-- Breadcrumb -->
	<nav aria-label="breadcrumb" class="mb-4">
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="<?php echo Uri::base(); ?>client" class="text-decoration-none">
					<i class="fas fa-home me-1"></i>Trang chủ
				</a>
			</li>
			<li class="breadcrumb-item">
				<a href="<?php echo Uri::base(); ?>client/stories" class="text-decoration-none">Truyện</a>
			</li>
			<li class="breadcrumb-item active" aria-current="page">
				<?php echo Security::htmlentities($story->title); ?>
			</li>
		</ol>
	</nav>

	<!-- Story Detail Section -->
	<div class="row">
		<!-- Story Info -->
		<div class="col-lg-8">
			<div class="card mb-4">
				<div class="card-body">
					<div class="row">
						<!-- Cover Image -->
						<div class="col-md-4 mb-3">
							<img src="<?php echo $story->cover_image ? Uri::base() . $story->cover_image : Uri::base() . 'assets/img/default-story-cover.svg'; ?>" 
								 class="img-fluid rounded shadow story-detail-cover" 
								 alt="<?php echo Security::htmlentities($story->title); ?>">
						</div>
						
						<!-- Story Information -->
						<div class="col-md-8">
							<h1 class="h2 mb-3"><?php echo Security::htmlentities($story->title); ?></h1>
							
							<!-- Author -->
							<?php if (isset($author) && $author && $author->is_active == 1 && empty($author->deleted_at)): ?>
							<p class="mb-2">
								<strong><i class="fas fa-pen-fancy me-2 text-primary"></i>Tác giả:</strong>
								<a href="<?php echo Uri::base(); ?>client/author/<?php echo $author->slug; ?>" 
								   class="text-decoration-none ms-2">
									<?php echo Security::htmlentities($author->name); ?>
								</a>
							</p>
							<?php endif; ?>
							
							<!-- Status -->
							<p class="mb-2">
								<strong><i class="fas fa-flag me-2 text-info"></i>Trạng thái:</strong>
								<span class="status-badge status-<?php echo $story->status; ?> ms-2">
									<?php 
									switch($story->status) {
										case 'ongoing': echo 'Đang cập nhật'; break;
										case 'completed': echo 'Hoàn thành'; break;
										case 'paused': echo 'Tạm dừng'; break;
										default: echo $story->status;
									}
									?>
								</span>
							</p>
							
							<!-- View Count -->
							<p class="mb-2">
								<strong><i class="fas fa-chart-line me-2 text-success"></i>Lượt xem:</strong>
								<span class="ms-2"><?php echo number_format($story->views); ?></span>
							</p>
							
							
							<!-- Categories -->
							<?php if (isset($categories) && !empty($categories)): ?>
							<p class="mb-3">
								<strong style="color: #fff;"><i class="fas fa-layer-group me-2 text-warning"></i>Thể loại:</strong>
								<?php foreach ($categories as $category): ?>
									<a href="<?php echo Uri::base(); ?>client/category/<?php echo $category->slug; ?>" 
									   class="category-badge me-1 text-decoration-none">
										<?php echo Security::htmlentities($category->name); ?>
									</a>
								<?php endforeach; ?>
							</p>
							<?php endif; ?>
							
							<!-- Description -->
							<?php if (!empty($story->description)): ?>
							<div class="mb-3">
								<strong><i class="fas fa-file-alt me-2 text-secondary"></i>Mô tả:</strong>
								<div class="mt-2 p-3 bg-light rounded">
									<?php echo nl2br(Security::htmlentities($story->description)); ?>
								</div>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Chapters List -->
			<?php if (isset($chapters) && !empty($chapters)): ?>
			<div class="card mb-4">
				<div class="card-header">
					<h3 class="h4 mb-0">
						<i class="fas fa-list me-2"></i>Danh sách chương
						<span class="badge bg-primary ms-2"><?php echo count($chapters); ?> chương</span>
					</h3>
				</div>
				<div class="card-body p-0">
					<div class="chapter-list-container">
						<?php foreach ($chapters as $index => $chapter): ?>
						<div class="chapter-item">
							<div class="chapter-number">
								<span class="chapter-badge"><?php echo $chapter->chapter_number; ?></span>
							</div>
							<div class="chapter-content">
								<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $chapter->chapter_number; ?>" 
								   class="chapter-title">
									<?php echo Security::htmlentities($chapter->title); ?>
								</a>
								<div class="chapter-meta">
									<span class="chapter-date">
										<i class="fas fa-calendar-alt me-1"></i>
										<?php echo date('d/m/Y', strtotime($chapter->created_at)); ?>
									</span>
									<span class="chapter-views">
										<i class="fas fa-eye me-1"></i>
										<?php echo number_format($chapter->views); ?>
									</span>
								</div>
							</div>
							<div class="chapter-action">
								<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $chapter->chapter_number; ?>" 
								   class="btn-read">
									<i class="fas fa-play"></i>
								</a>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<?php else: ?>
			<div class="card mb-4">
				<div class="card-body text-center py-5">
					<i class="fas fa-book-open fa-3x text-muted mb-3"></i>
					<h4 class="text-muted">Chưa có chương nào</h4>
					<p class="text-muted">Truyện này chưa có chương nào được đăng.</p>
				</div>
			</div>
			<?php endif; ?>
		</div>
		
		<!-- Sidebar -->
		<div class="col-lg-4">
			<!-- Read First Chapter Button -->
			<?php if (isset($chapters) && !empty($chapters)): ?>
			<?php 
			$first_chapter = reset($chapters); // Get first chapter
			?>
			<div class="card mb-4">
				<div class="card-body text-center">
					<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $first_chapter->chapter_number; ?>" 
					   class="btn btn-primary btn-lg w-100 mb-3">
						<i class="fas fa-play me-2"></i>Đọc ngay
					</a>
					<small class="text-muted">Bắt đầu từ chương <?php echo $first_chapter->chapter_number; ?></small>
				</div>
			</div>
			<?php else: ?>
			<div class="card mb-4">
				<div class="card-body text-center">
					<p class="text-muted mb-0">Chưa có chương nào để đọc</p>
				</div>
			</div>
			<?php endif; ?>
			
			<!-- Related Stories -->
			<?php if (isset($related_stories) && !empty($related_stories)): ?>
			<div class="card mb-4">
				<div class="card-header">
					<h4 class="h5 mb-0">
						<i class="fas fa-bookmark me-2"></i>Truyện liên quan
					</h4>
				</div>
				<div class="card-body">
					<?php foreach ($related_stories as $related_story): ?>
					<div class="d-flex mb-3">
						<div class="flex-shrink-0 me-3">
							<a href="<?php echo Uri::base(); ?>client/story/<?php echo $related_story->slug; ?>">
								<img src="<?php echo $related_story->cover_image ? Uri::base() . $related_story->cover_image : Uri::base() . 'assets/img/default-story-cover.svg'; ?>" 
									 class="rounded" 
									 width="60" 
									 height="80" 
									 alt="<?php echo Security::htmlentities($related_story->title); ?>">
							</a>
						</div>
						<div class="flex-grow-1">
							<h6 class="mb-1">
								<a href="<?php echo Uri::base(); ?>client/story/<?php echo $related_story->slug; ?>" 
								   class="text-decoration-none">
									<?php echo Security::htmlentities($related_story->title); ?>
								</a>
							</h6>
							<small class="text-muted d-block">
								<?php echo Security::htmlentities($related_story->author_name ?? ''); ?>
							</small>
							<small class="text-muted">
								<i class="fas fa-eye me-1"></i>
								<?php echo number_format($related_story->views); ?>
							</small>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>
			
		</div>
	</div>
</div>

<style>
/* Import unified design system */
.container {
	color: var(--text-secondary, #e2e8f0);
	font-family: var(--font-family, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif);
}

h1, h2, h3, h4, h5 {
	color: var(--text-primary, #ffffff);
	font-family: var(--font-family, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif);
	font-weight: var(--font-weight-bold, 700);
}

.story-detail-cover {
	max-width: 300px;
	width: 100%;
	height: auto;
	border: 1px solid #444;
	border-radius: 8px;
}

.card {
	background: var(--bg-surface, rgba(30, 41, 59, 0.6)) !important;
	backdrop-filter: blur(10px);
	-webkit-backdrop-filter: blur(10px);
	border: 1px solid var(--border-primary, rgba(51, 65, 85, 0.5)) !important;
	border-radius: 16px !important;
	box-shadow: var(--shadow-lg, 0 10px 15px -3px rgba(0, 0, 0, 0.1)) !important;
}

.card-header {
	background: var(--bg-card, rgba(30, 41, 59, 0.8)) !important;
	border-bottom: 1px solid var(--border-primary, rgba(51, 65, 85, 0.5)) !important;
	color: var(--text-primary, #ffffff) !important;
	border-radius: 16px 16px 0 0 !important;
	font-family: var(--font-family, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif);
}

.card-body {
	background: transparent;
	color: var(--text-secondary, #e2e8f0);
	font-family: var(--font-family, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif);
}

.breadcrumb {
	background: transparent;
}

.breadcrumb-item a {
	color: var(--text-primary, #ffffff) !important;
	text-decoration: none;
	transition: all 0.2s ease;
	font-family: var(--font-family, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif);
	font-size: var(--font-size-sm, 0.875rem);
}

.breadcrumb-item a:hover {
	color: var(--primary-color, #8b7ef8) !important;
}

.breadcrumb-item.active {
	color: var(--text-primary, #ffffff) !important;
	font-family: var(--font-family, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif);
	font-size: var(--font-size-sm, 0.875rem);
}

/* Breadcrumb separators */
.breadcrumb-item + .breadcrumb-item::before {
	color: var(--text-primary, #ffffff) !important;
	content: "/";
}

p strong {
	color: var(--text-primary, #ffffff);
	font-family: var(--font-family, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif);
	font-weight: var(--font-weight-semibold, 600);
}

/* Input descriptions and form elements */
.form-text,
.form-label,
small,
.text-muted {
	color: var(--text-primary, #ffffff) !important;
	font-family: var(--font-family, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif);
	font-size: var(--font-size-sm, 0.875rem);
}

.form-control::placeholder {
	color: var(--text-muted, #94a3b8) !important;
	font-family: var(--font-family, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif);
}

.form-control {
	background-color: var(--bg-card, rgba(15, 23, 42, 0.8)) !important;
	border: 1px solid var(--border-primary, rgba(51, 65, 85, 0.5)) !important;
	color: var(--text-secondary, #e2e8f0) !important;
	font-family: var(--font-family, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif);
	font-size: var(--font-size-base, 1rem);
}

.form-control:focus {
	background-color: var(--bg-hover, rgba(15, 23, 42, 0.9)) !important;
	border-color: var(--primary-color, #8b7ef8) !important;
	color: var(--text-secondary, #e2e8f0) !important;
	box-shadow: 0 0 0 0.2rem rgba(139, 126, 248, 0.25) !important;
}

/* Breadcrumb improvements */
.breadcrumb-item a i {
	color: #fff !important;
}

.breadcrumb-item.active i {
	color: #fff !important;
}



/* Modern Chapter List Styles */
.chapter-list-container {
	background: transparent;
	padding: 0;
}

.chapter-item {
	display: flex;
	align-items: center;
	padding: 1rem 1.5rem;
	border-bottom: 1px solid var(--border-light, rgba(51, 65, 85, 0.3));
	background: var(--bg-card, rgba(15, 23, 42, 0.6));
	transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	position: relative;
	overflow: hidden;
	font-family: var(--font-family, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif);
}

.chapter-item:last-child {
	border-bottom: none;
}

.chapter-item::before {
	content: '';
	position: absolute;
	left: 0;
	top: 0;
	height: 100%;
	width: 3px;
	background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
	opacity: 0;
	transition: opacity 0.3s ease;
}

.chapter-item:hover {
	background: var(--bg-hover, rgba(30, 41, 59, 0.8));
	transform: translateX(4px);
}

.chapter-item:hover::before {
	opacity: 1;
}

.chapter-number {
	flex-shrink: 0;
	margin-right: 1rem;
}

.chapter-badge {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 40px;
	height: 40px;
	background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
	color: #fff;
	border-radius: 50%;
	font-weight: 700;
	font-size: 0.875rem;
	box-shadow: 0 4px 12px rgba(139, 126, 248, 0.3);
	transition: all 0.3s ease;
}

.chapter-item:hover .chapter-badge {
	transform: scale(1.1);
	box-shadow: 0 6px 16px rgba(139, 126, 248, 0.4);
}

.chapter-content {
	flex: 1;
	min-width: 0;
}

.chapter-title {
	color: var(--text-secondary, #e2e8f0);
	text-decoration: none;
	font-weight: var(--font-weight-semibold, 600);
	font-size: var(--font-size-base, 1rem);
	line-height: var(--line-height-tight, 1.25);
	display: block;
	margin-bottom: 0.5rem;
	transition: all 0.2s ease;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	font-family: var(--font-family, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif);
}

.chapter-title:hover {
	color: var(--primary-color);
	text-decoration: none;
}

.chapter-meta {
	display: flex;
	gap: 1rem;
	align-items: center;
}

.chapter-date,
.chapter-views {
	color: var(--text-muted, #94a3b8);
	font-size: var(--font-size-sm, 0.875rem);
	font-weight: var(--font-weight-medium, 500);
	display: flex;
	align-items: center;
	font-family: var(--font-family, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif);
}

.chapter-date i,
.chapter-views i {
	color: var(--primary-color);
	font-size: 0.75rem;
}

.chapter-action {
	flex-shrink: 0;
	margin-left: 1rem;
}

.btn-read {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 40px;
	height: 40px;
	background: rgba(139, 126, 248, 0.1);
	border: 2px solid rgba(139, 126, 248, 0.3);
	color: var(--primary-color);
	border-radius: 50%;
	text-decoration: none;
	transition: all 0.3s ease;
	opacity: 0;
	transform: scale(0.8);
}

.chapter-item:hover .btn-read {
	opacity: 1;
	transform: scale(1);
}

.btn-read:hover {
	background: var(--primary-color);
	color: #fff;
	border-color: var(--primary-color);
	transform: scale(1.1);
}

/* Responsive Design */
@media (max-width: 768px) {
	.chapter-item {
		padding: 0.875rem 1rem;
	}
	
	.chapter-badge {
		width: 35px;
		height: 35px;
		font-size: 0.8rem;
	}
	
	.chapter-title {
		font-size: 0.9rem;
	}
	
	.chapter-meta {
		flex-direction: column;
		gap: 0.25rem;
		align-items: flex-start;
	}
	
	.chapter-date,
	.chapter-views {
		font-size: 0.8rem;
	}
	
	.btn-read {
		width: 35px;
		height: 35px;
		opacity: 1;
		transform: scale(1);
	}
}

@media (max-width: 576px) {
	.chapter-item {
		padding: 0.75rem;
	}
	
	.chapter-number {
		margin-right: 0.75rem;
	}
	
	.chapter-badge {
		width: 32px;
		height: 32px;
		font-size: 0.75rem;
	}
	
	.chapter-title {
		font-size: 0.85rem;
		margin-bottom: 0.25rem;
	}
}

.btn-primary {
	background: #6c5ce7;
	border-color: #6c5ce7;
}

.btn-primary:hover {
	background: #5a4fc7;
	border-color: #5a4fc7;
}

.bg-light {
	background: #2d2d2d !important;
	color: #e0e0e0;
	border: 1px solid #444;
}

.story-cover {
	height: 120px;
	width: 80px;
	object-fit: cover;
	border-radius: 4px;
}
</style>
