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
							<?php if (isset($author) && $author): ?>
							<p class="mb-2">
								<strong><i class="fas fa-user me-2 text-primary"></i>Tác giả:</strong>
								<a href="<?php echo Uri::base(); ?>client/author/<?php echo $author->slug; ?>" 
								   class="text-decoration-none ms-2">
									<?php echo Security::htmlentities($author->name); ?>
								</a>
							</p>
							<?php endif; ?>
							
							<!-- Status -->
							<p class="mb-2">
								<strong><i class="fas fa-info-circle me-2 text-info"></i>Trạng thái:</strong>
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
								<strong><i class="fas fa-eye me-2 text-success"></i>Lượt xem:</strong>
								<span class="ms-2"><?php echo number_format($story->views); ?></span>
							</p>
							
							
							<!-- Categories -->
							<?php if (isset($categories) && !empty($categories)): ?>
							<p class="mb-3">
								<strong style="color: #fff;"><i class="fas fa-tags me-2 text-warning"></i>Thể loại:</strong>
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
								<strong><i class="fas fa-align-left me-2 text-secondary"></i>Mô tả:</strong>
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
					<div class="table-responsive">
						<table class="table table-hover mb-0">
							<thead>
								<tr>
									<th width="10%">Chương</th>
									<th>Tên chương</th>
									<th width="15%">Ngày cập nhật</th>
									<th width="10%">Lượt xem</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($chapters as $chapter): ?>
								<tr>
									<td>
										<span class="badge"><?php echo $chapter->chapter_number; ?></span>
									</td>
									<td>
										<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $chapter->chapter_number; ?>" 
										   class="text-decoration-none">
											<?php echo Security::htmlentities($chapter->title); ?>
										</a>
									</td>
									<td>
										<small>
											<?php echo date('d/m/Y', strtotime($chapter->created_at)); ?>
										</small>
									</td>
									<td>
										<small>
											<i class="fas fa-eye me-1"></i>
											<?php echo number_format($chapter->views); ?>
										</small>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
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
								<?php echo Security::htmlentities($related_story->author_name ?? 'Unknown'); ?>
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
.container {
	color: #e0e0e0;
}

h1, h2, h3, h4, h5 {
	color: #fff;
}

.story-detail-cover {
	max-width: 300px;
	width: 100%;
	height: auto;
	border: 1px solid #444;
	border-radius: 8px;
}

.card {
	background: rgba(30, 41, 59, 0.6) !important;
	backdrop-filter: blur(10px);
	-webkit-backdrop-filter: blur(10px);
	border: 1px solid rgba(51, 65, 85, 0.5) !important;
	border-radius: 16px !important;
	box-shadow: var(--shadow-lg) !important;
}

.card-header {
	background: rgba(30, 41, 59, 0.8) !important;
	border-bottom: 1px solid rgba(51, 65, 85, 0.5) !important;
	color: #fff !important;
	border-radius: 16px 16px 0 0 !important;
}

.card-body {
	background: transparent;
}

.breadcrumb {
	background: transparent;
}

.breadcrumb-item a {
	color: #6c5ce7;
}

.breadcrumb-item.active {
	color: #aaa;
}

p strong {
	color: #fff;
}



.table {
	color: #e2e8f0 !important;
	background: transparent !important;
}

.table thead th {
	background: rgba(15, 23, 42, 0.8) !important;
	color: #fff !important;
	border-bottom: 2px solid rgba(139, 126, 248, 0.3) !important;
	border-top: none !important;
	padding: 1rem !important;
	font-weight: 600 !important;
	text-transform: uppercase;
	font-size: 0.875rem;
	letter-spacing: 0.5px;
}

.table tbody tr {
	background: rgba(15, 23, 42, 1) !important;
	border-bottom: 1px solid rgba(51, 65, 85, 0.3) !important;
	transition: all 0.2s ease;
}

.table tbody tr:nth-child(even) {
	background: rgba(15, 23, 42, 0.95) !important;
}

.table tbody tr:last-child {
	border-bottom: none !important;
}

.table tbody td {
	border: none !important;
	color: #e2e8f0 !important;
	padding: 1rem !important;
	vertical-align: middle;
}

.table-hover tbody tr:hover {
	background: rgba(30, 41, 59, 1) !important;
}

.table tbody td a {
	color: var(--primary-color) !important;
	text-decoration: none;
	font-weight: 500;
	transition: all 0.2s ease;
}

.table tbody td a:hover {
	color: var(--primary-dark) !important;
	text-decoration: underline;
}

.table .badge {
	background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
	color: #fff !important;
	border: none !important;
	padding: 0.5rem 0.875rem !important;
	font-weight: 600 !important;
	border-radius: 12px !important;
	box-shadow: 0 2px 8px rgba(139, 126, 248, 0.3);
}

.table small {
	color: #94a3b8 !important;
	font-weight: 500;
}

.table tbody td i {
	color: var(--primary-color) !important;
	margin-right: 0.5rem;
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
