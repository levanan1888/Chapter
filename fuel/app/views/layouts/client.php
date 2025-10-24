<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo isset($title) ? $title : 'Đọc truyện tranh online'; ?></title>
	
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Font Awesome -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
	<!-- Custom CSS -->
	<style>
		:root {
			--primary-color: #007bff;
			--secondary-color: #6c757d;
			--success-color: #28a745;
			--danger-color: #dc3545;
			--warning-color: #ffc107;
			--info-color: #17a2b8;
			--light-color: #f8f9fa;
			--dark-color: #343a40;
		}

		body {
			background-color: #f8f9fa;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}

		.navbar-brand {
			font-weight: bold;
			font-size: 1.5rem;
		}

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

		.footer {
			background-color: var(--dark-color);
			color: white;
			padding: 2rem 0;
			margin-top: 3rem;
		}

		.search-box {
			position: relative;
		}

		.search-results {
			position: absolute;
			top: 100%;
			left: 0;
			right: 0;
			background: white;
			border: 1px solid #ddd;
			border-radius: 0 0 8px 8px;
			box-shadow: 0 4px 10px rgba(0,0,0,0.1);
			z-index: 1000;
			max-height: 300px;
			overflow-y: auto;
		}

		.search-item {
			padding: 10px 15px;
			border-bottom: 1px solid #eee;
			cursor: pointer;
			transition: background-color 0.2s;
		}

		.search-item:hover {
			background-color: #f8f9fa;
		}

		.chapter-list {
			max-height: 400px;
			overflow-y: auto;
		}

		.reader-container {
			background: white;
			border-radius: 8px;
			padding: 20px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
		}

		.chapter-image {
			width: 100%;
			height: auto;
			margin-bottom: 10px;
			border-radius: 4px;
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

		/* Dark mode */
		.dark-mode {
			background-color: #1a1a1a;
			color: #e0e0e0;
		}

		.dark-mode .card {
			background-color: #2d2d2d;
			color: #e0e0e0;
		}

		.dark-mode .navbar {
			background-color: #2d2d2d !important;
		}

		.dark-mode .footer {
			background-color: #1a1a1a;
		}
	</style>
</head>
<body>
	<!-- Navigation -->
	<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
		<div class="container">
			<a class="navbar-brand" href="<?php echo Uri::base(); ?>">
				<i class="fas fa-book-open me-2"></i>
				ComicHub
			</a>
			
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
				<span class="navbar-toggler-icon"></span>
			</button>
			
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav me-auto">
					<li class="nav-item">
						<a class="nav-link" href="<?php echo Uri::base(); ?>">
							<i class="fas fa-home me-1"></i>Trang chủ
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo Uri::base(); ?>client/stories">
							<i class="fas fa-list me-1"></i>Danh sách
						</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
							<i class="fas fa-th-large me-1"></i>Danh mục
						</a>
						<ul class="dropdown-menu">
							<?php if (isset($categories) && !empty($categories)): ?>
								<?php foreach ($categories as $category): ?>
									<li>
										<a class="dropdown-item" href="<?php echo Uri::base(); ?>client/category/<?php echo $category->slug; ?>">
											<?php echo $category->name; ?>
										</a>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</li>
				</ul>
				
				<!-- Search Box -->
				<form class="d-flex search-box" id="searchForm">
					<div class="input-group">
						<input class="form-control" type="search" placeholder="Tìm kiếm truyện..." id="searchInput">
						<button class="btn btn-outline-light" type="submit">
							<i class="fas fa-search"></i>
						</button>
					</div>
					<div class="search-results" id="searchResults" style="display: none;"></div>
				</form>
				
				<ul class="navbar-nav ms-3">
					<li class="nav-item">
						<button class="btn btn-outline-light" id="darkModeToggle">
							<i class="fas fa-moon"></i>
						</button>
					</li>
					<?php if (Session::get('user_id')): ?>
						<!-- User is logged in -->
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
								<i class="fas fa-user me-1"></i>
								<?php echo Security::htmlentities(Session::get('user_full_name', Session::get('user_username', 'User'))); ?>
							</a>
							<ul class="dropdown-menu dropdown-menu-end">
								<li>
									<a class="dropdown-item" href="<?php echo Uri::base(); ?>user/profile">
										<i class="fas fa-user-circle me-2"></i>Thông tin tài khoản
									</a>
								</li>
								<li><hr class="dropdown-divider"></li>
								<li>
									<a class="dropdown-item" href="<?php echo Uri::base(); ?>user/logout">
										<i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
									</a>
								</li>
							</ul>
						</li>
					<?php else: ?>
						<!-- User is not logged in -->
						<li class="nav-item">
							<a class="nav-link" href="<?php echo Uri::base(); ?>user/login">
								<i class="fas fa-sign-in-alt me-1"></i>Đăng nhập
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo Uri::base(); ?>user/register">
								<i class="fas fa-user-plus me-1"></i>Đăng ký
							</a>
						</li>
					<?php endif; ?>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo Uri::base(); ?>admin">
							<i class="fas fa-cog me-1"></i>Admin
						</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<!-- Main Content -->
	<main class="container-fluid py-4">
		<?php if (isset($content)): ?>
			<?php echo $content; ?>
		<?php endif; ?>
	</main>

	<!-- Footer -->
	<footer class="footer">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<h5>ComicHub</h5>
					<p>Nền tảng đọc truyện tranh online miễn phí</p>
				</div>
				<div class="col-md-6 text-md-end">
					<p>&copy; 2024 ComicHub. All rights reserved.</p>
				</div>
			</div>
		</div>
	</footer>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	
	<script>
		// Dark mode toggle
		document.getElementById('darkModeToggle').addEventListener('click', function() {
			document.body.classList.toggle('dark-mode');
			const icon = this.querySelector('i');
			if (document.body.classList.contains('dark-mode')) {
				icon.className = 'fas fa-sun';
				localStorage.setItem('darkMode', 'true');
			} else {
				icon.className = 'fas fa-moon';
				localStorage.setItem('darkMode', 'false');
			}
		});

		// Load dark mode preference
		if (localStorage.getItem('darkMode') === 'true') {
			document.body.classList.add('dark-mode');
			document.getElementById('darkModeToggle').querySelector('i').className = 'fas fa-sun';
		}

		// Search functionality
		let searchTimeout;
		document.getElementById('searchInput').addEventListener('input', function() {
			clearTimeout(searchTimeout);
			const query = this.value.trim();
			
			if (query.length < 2) {
				document.getElementById('searchResults').style.display = 'none';
				return;
			}

			searchTimeout = setTimeout(() => {
				// AJAX search request
				fetch(`<?php echo Uri::base(); ?>client/api/search?q=${encodeURIComponent(query)}`)
					.then(response => response.json())
					.then(data => {
						const results = document.getElementById('searchResults');
						if (data.success && data.data.length > 0) {
							results.innerHTML = data.data.map(story => `
								<div class="search-item" onclick="window.location.href='<?php echo Uri::base(); ?>client/story/${story.slug}'">
									<div class="d-flex">
										<img src="${story.cover_image || '<?php echo Uri::base(); ?>assets/img/default-story-cover.svg'}" 
											 class="me-3" style="width: 50px; height: 70px; object-fit: cover;">
										<div>
											<h6 class="mb-1">${story.title}</h6>
											<small class="text-muted">${story.author_name || 'Unknown'}</small>
										</div>
									</div>
								</div>
							`).join('');
							results.style.display = 'block';
						} else {
							results.innerHTML = '<div class="search-item text-center text-muted">Không tìm thấy kết quả</div>';
							results.style.display = 'block';
						}
					})
					.catch(error => {
						console.error('Search error:', error);
					});
			}, 300);
		});

		// Hide search results when clicking outside
		document.addEventListener('click', function(e) {
			if (!e.target.closest('.search-box')) {
				document.getElementById('searchResults').style.display = 'none';
			}
		});

		// Search form submission
		document.getElementById('searchForm').addEventListener('submit', function(e) {
			e.preventDefault();
			const query = document.getElementById('searchInput').value.trim();
			if (query) {
				window.location.href = `<?php echo Uri::base(); ?>client/search?q=${encodeURIComponent(query)}`;
			}
		});
	</script>
</body>
</html>
