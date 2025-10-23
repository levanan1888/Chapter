<div class="container-fluid">
	<!-- Navigation Header -->
	<div class="row bg-white shadow-sm py-3 mb-4">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center">
				<!-- Back to Story -->
				<div>
					<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>" 
					   class="btn btn-outline-primary btn-sm">
						<i class="fas fa-arrow-left me-2"></i>Về trang truyện
					</a>
				</div>
				
				<!-- Chapter Info -->
				<div class="text-center">
					<h4 class="mb-1"><?php echo Security::htmlentities($story->title); ?></h4>
					<h5 class="text-muted mb-0">Chương <?php echo $chapter->chapter_number; ?>: <?php echo Security::htmlentities($chapter->title); ?></h5>
				</div>
				
				<!-- Chapter Selector -->
				<div class="dropdown">
					<button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
						<i class="fas fa-list me-2"></i>Danh sách chương
					</button>
					<div class="dropdown-menu dropdown-menu-end" style="max-height: 400px; overflow-y: auto; min-width: 250px;">
						<?php if (isset($all_chapters) && !empty($all_chapters)): ?>
							<?php foreach ($all_chapters as $ch): ?>
								<a class="dropdown-item <?php echo ($ch->chapter_number == $chapter->chapter_number) ? 'active' : ''; ?>" 
								   href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $ch->chapter_number; ?>">
									<div class="d-flex justify-content-between">
										<span>Chương <?php echo $ch->chapter_number; ?></span>
										<small class="text-muted"><?php echo Security::htmlentities($ch->title); ?></small>
									</div>
								</a>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Chapter Navigation Buttons -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="d-flex justify-content-between">
				<!-- Previous Chapter -->
				<?php if (isset($previous_chapter) && $previous_chapter): ?>
					<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $previous_chapter->chapter_number; ?>" 
					   class="btn btn-primary">
						<i class="fas fa-chevron-left me-2"></i>
						Chương <?php echo $previous_chapter->chapter_number; ?>
					</a>
				<?php else: ?>
					<div></div>
				<?php endif; ?>
				
				<!-- Next Chapter -->
				<?php if (isset($next_chapter) && $next_chapter): ?>
					<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $next_chapter->chapter_number; ?>" 
					   class="btn btn-primary">
						Chương <?php echo $next_chapter->chapter_number; ?>
						<i class="fas fa-chevron-right ms-2"></i>
					</a>
				<?php else: ?>
					<div></div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- Chapter Content -->
	<div class="row justify-content-center">
		<div class="col-lg-4 col-md-6 col-sm-8">
			<div class="reader-container">
				<?php 
				$images = $chapter->get_images();
				if (!empty($images)): 
				?>
					<?php foreach ($images as $index => $image): ?>
						<div class="chapter-image-container mb-3">
							<img src="<?php echo Uri::base() . $image; ?>" 
								 class="img-fluid chapter-image rounded shadow" 
								 alt="<?php echo Security::htmlentities($story->title); ?> - Trang <?php echo $index + 1; ?>"
								 loading="lazy">
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="text-center py-5">
						<i class="fas fa-image fa-3x text-muted mb-3"></i>
						<h4 class="text-muted">Chương này chưa có nội dung</h4>
						<p class="text-muted">Vui lòng quay lại sau.</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- Bottom Navigation -->
	<div class="row mt-4 mb-4">
		<div class="col-12">
			<div class="d-flex justify-content-between">
				<!-- Previous Chapter -->
				<?php if (isset($previous_chapter) && $previous_chapter): ?>
					<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $previous_chapter->chapter_number; ?>" 
					   class="btn btn-outline-primary btn-lg">
						<i class="fas fa-chevron-left me-2"></i>
						Chương <?php echo $previous_chapter->chapter_number; ?>
					</a>
				<?php else: ?>
					<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>" 
					   class="btn btn-outline-secondary btn-lg">
						<i class="fas fa-arrow-left me-2"></i>
						Về trang truyện
					</a>
				<?php endif; ?>
				
				<!-- Back to Story -->
				<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>" 
				   class="btn btn-outline-info btn-lg">
					<i class="fas fa-list me-2"></i>
					Danh sách chương
				</a>
				
				<!-- Next Chapter -->
				<?php if (isset($next_chapter) && $next_chapter): ?>
					<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $next_chapter->chapter_number; ?>" 
					   class="btn btn-primary btn-lg">
						Chương <?php echo $next_chapter->chapter_number; ?>
						<i class="fas fa-chevron-right ms-2"></i>
					</a>
				<?php else: ?>
					<div></div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- Floating Navigation (for mobile) -->
	<div class="navigation-buttons d-md-none">
		<?php if (isset($previous_chapter) && $previous_chapter): ?>
			<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $previous_chapter->chapter_number; ?>" 
			   class="btn btn-primary btn-floating">
				<i class="fas fa-chevron-left"></i>
			</a>
		<?php endif; ?>
		
		<?php if (isset($next_chapter) && $next_chapter): ?>
			<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $next_chapter->chapter_number; ?>" 
			   class="btn btn-primary btn-floating">
				<i class="fas fa-chevron-right"></i>
			</a>
		<?php endif; ?>
	</div>
</div>

<!-- Keyboard Navigation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
	// Keyboard navigation
	document.addEventListener('keydown', function(e) {
		// Left arrow key - previous chapter
		if (e.key === 'ArrowLeft') {
			<?php if (isset($previous_chapter) && $previous_chapter): ?>
				window.location.href = '<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $previous_chapter->chapter_number; ?>';
			<?php endif; ?>
		}
		
		// Right arrow key - next chapter
		if (e.key === 'ArrowRight') {
			<?php if (isset($next_chapter) && $next_chapter): ?>
				window.location.href = '<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $next_chapter->chapter_number; ?>';
			<?php endif; ?>
		}
		
		// Escape key - back to story
		if (e.key === 'Escape') {
			window.location.href = '<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>';
		}
	});

	// Auto-hide navigation on scroll (for better reading experience)
	let lastScrollTop = 0;
	let navBar = document.querySelector('.row.bg-white.shadow-sm');
	
	window.addEventListener('scroll', function() {
		let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
		
		if (scrollTop > lastScrollTop && scrollTop > 100) {
			// Scrolling down - hide nav
			navBar.style.transform = 'translateY(-100%)';
			navBar.style.transition = 'transform 0.3s ease';
		} else {
			// Scrolling up - show nav
			navBar.style.transform = 'translateY(0)';
		}
		
		lastScrollTop = scrollTop;
	});

	// Lazy loading for images
	if ('IntersectionObserver' in window) {
		const imageObserver = new IntersectionObserver((entries, observer) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					const img = entry.target;
					img.src = img.dataset.src || img.src;
					img.classList.remove('lazy');
					imageObserver.unobserve(img);
				}
			});
		});

		document.querySelectorAll('img[loading="lazy"]').forEach(img => {
			imageObserver.observe(img);
		});
	}

	// Double-click to fullscreen
	document.querySelectorAll('.chapter-image').forEach(img => {
		img.addEventListener('dblclick', function() {
			if (this.requestFullscreen) {
				this.requestFullscreen();
			} else if (this.webkitRequestFullscreen) {
				this.webkitRequestFullscreen();
			} else if (this.msRequestFullscreen) {
				this.msRequestFullscreen();
			}
		});
	});
});
</script>

<style>
.reader-container {
	background: white;
	border-radius: 8px;
	padding: 20px;
	box-shadow: 0 2px 10px rgba(0,0,0,0.1);
	max-width: 100%;
	margin: 0 auto;
}

.chapter-image {
	width: 100%;
	height: auto;
	margin-bottom: 10px;
	border-radius: 4px;
	transition: transform 0.2s ease;
	box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.chapter-image:hover {
	transform: scale(1.02);
	box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.chapter-image-container {
	text-align: center;
}

/* Responsive adjustments for centered content */
@media (max-width: 768px) {
	.reader-container {
		padding: 15px;
		margin: 0 10px;
	}
	
	.chapter-image {
		margin-bottom: 8px;
	}
}

.navigation-buttons {
	position: fixed;
	bottom: 20px;
	right: 20px;
	z-index: 1000;
}

.btn-floating {
	width: 50px;
	height: 50px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	margin: 5px;
	box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

/* Mobile optimizations */
@media (max-width: 768px) {
	.reader-container {
		padding: 10px;
	}
	
	.chapter-image {
		margin-bottom: 5px;
	}
	
	.navigation-buttons {
		bottom: 10px;
		right: 10px;
	}
}

/* Dark mode support */
.dark-mode .reader-container {
	background-color: #2d2d2d;
	color: #e0e0e0;
}

.dark-mode .row.bg-white.shadow-sm {
	background-color: #2d2d2d !important;
}

.dark-mode .btn-outline-primary,
.dark-mode .btn-outline-secondary,
.dark-mode .btn-outline-info {
	border-color: #007bff;
	color: #007bff;
}

.dark-mode .btn-outline-primary:hover,
.dark-mode .btn-outline-secondary:hover,
.dark-mode .btn-outline-info:hover {
	background-color: #007bff;
	border-color: #007bff;
	color: white;
}

/* Smooth scrolling */
html {
	scroll-behavior: smooth;
}

/* Loading animation for images */
.chapter-image {
	opacity: 0;
	animation: fadeIn 0.5s ease-in-out forwards;
}

@keyframes fadeIn {
	from { opacity: 0; }
	to { opacity: 1; }
}
</style>
