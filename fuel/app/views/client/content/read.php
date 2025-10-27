<div class="container-fluid">
	<!-- Navigation Header -->
	<div class="row py-4 mb-4" style="background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(51, 65, 85, 0.5); border-radius: 20px;">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center text-white flex-wrap gap-3">
				<!-- Back to Story -->
				<div>
					<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>" 
					   class="btn btn-outline-primary">
						<i class="fas fa-arrow-left me-2"></i>Về trang truyện
					</a>
				</div>
				
				<!-- Chapter Info -->
				<div class="text-center flex-grow-1">
					<h4 class="mb-1 text-white fw-bold"><?php echo Security::htmlentities($story->title); ?></h4>
					<h5 class="mb-0" style="color: #94a3b8; font-size: 0.9rem;">Chương <?php echo $chapter->chapter_number; ?>: <?php echo Security::htmlentities($chapter->title); ?></h5>
				</div>
				
				<!-- Chapter Selector -->
				<div class="dropdown">
					<button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
						<i class="fas fa-list me-2"></i>Danh sách chương
					</button>
					<div class="dropdown-menu dropdown-menu-end" style="max-height: 500px; overflow-y: auto; min-width: 280px; background: rgba(30, 41, 59, 0.98); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(51, 65, 85, 0.5);">
						<?php if (isset($all_chapters) && !empty($all_chapters)): ?>
							<?php foreach ($all_chapters as $ch): ?>
								<a class="dropdown-item <?php echo ($ch->chapter_number == $chapter->chapter_number) ? 'active' : ''; ?>" 
								   href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $ch->chapter_number; ?>"
								   style="color: #e2e8f0; padding: 0.75rem 1.25rem; border-radius: 8px; margin: 0.25rem;">
									<div class="d-flex justify-content-between align-items-center">
										<span class="fw-semibold">Chương <?php echo $ch->chapter_number; ?></span>
										<small class="text-muted" style="color: #64748b;"><?php echo Security::htmlentities($ch->title); ?></small>
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
		<div class="col-lg-6 col-md-8 col-sm-10">
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

<!-- Reading Controls -->
<div class="reading-controls" id="readingControls" style="display: none;">
	<button onclick="window.location.reload()" title="Làm mới">
		<i class="fas fa-sync"></i>
	</button>
</div>

<!-- Keyboard Navigation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
	// Show reading controls
	const controls = document.getElementById('readingControls');
	if (controls) {
		setTimeout(() => {
			controls.style.display = 'flex';
			controls.style.animation = 'fadeInUp 0.5s ease-out';
		}, 1000);
	}

	// Keyboard shortcuts toggle
	let controlsVisible = true;
	document.addEventListener('keydown', function(e) {
		if (e.key === 'h' || e.key === 'H') {
			controlsVisible = !controlsVisible;
			controls.style.display = controlsVisible ? 'flex' : 'none';
		}
	});
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
body {
	background: #0a0a0a;
}

.reader-container {
	background: linear-gradient(to bottom, rgba(15, 23, 42, 0.9), rgba(10, 10, 10, 1));
	border-radius: 24px;
	padding: 40px;
	border: 1px solid rgba(51, 65, 85, 0.3);
	max-width: 100%;
	margin: 0 auto;
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	box-shadow: var(--shadow-2xl);
}

/* Reading Controls */
.reading-controls {
	position: fixed;
	bottom: 30px;
	right: 30px;
	z-index: 1000;
	background: rgba(30, 41, 59, 0.95);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border: 1px solid rgba(51, 65, 85, 0.5);
	border-radius: 16px;
	padding: 1rem;
	box-shadow: var(--shadow-2xl);
	display: flex;
	flex-direction: column;
	gap: 0.75rem;
}

.reading-controls button {
	background: var(--primary-color);
	color: white;
	border: none;
	width: 48px;
	height: 48px;
	border-radius: 12px;
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	transition: all 0.3s ease;
	box-shadow: var(--shadow-md);
}

.reading-controls button:hover {
	background: var(--primary-dark);
	transform: translateY(-2px);
	box-shadow: var(--shadow-lg);
}


	.chapter-image {
		width: 100%;
		height: auto;
		margin-bottom: 20px;
		border-radius: 12px;
		transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
		box-shadow: 0 8px 24px rgba(0,0,0,0.3);
		background: linear-gradient(135deg, rgba(139, 126, 248, 0.1) 0%, rgba(255, 159, 102, 0.1) 100%);
		padding: 4px;
	}

	.chapter-image:hover {
		transform: scale(1.01);
		box-shadow: 0 12px 40px rgba(139, 126, 248, 0.2);
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
.btn-outline-primary,
.btn-outline-secondary,
.btn-outline-info {
	border-color: #6c5ce7;
	color: #6c5ce7;
	background: transparent;
}

.btn-outline-primary:hover,
.btn-outline-secondary:hover,
.btn-outline-info:hover {
	background-color: #6c5ce7;
	border-color: #6c5ce7;
	color: white;
}

.btn-primary {
	background: #6c5ce7;
	border-color: #6c5ce7;
}

.btn-primary:hover {
	background: #5a4fc7;
	border-color: #5a4fc7;
}

.dropdown-menu {
	background: #252525;
	border: 1px solid #444;
}

.dropdown-item {
	color: #e0e0e0;
}

.dropdown-item:hover {
	background: #2d3436;
	color: #fff;
}

.dropdown-item.active {
	background: #6c5ce7;
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

@keyframes fadeInUp {
	from {
		opacity: 0;
		transform: translateY(20px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

/* Responsive reading controls */
@media (max-width: 768px) {
	.reading-controls {
		bottom: 20px;
		right: 20px;
		padding: 0.75rem;
	}

	.reading-controls button {
		width: 40px;
		height: 40px;
	}

	.reader-container {
		padding: 20px;
	}
}
</style>
