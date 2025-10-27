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
			--primary-color: #8b7ef8;
			--primary-dark: #6b5ddc;
			--secondary-color: #a8a3ff;
			--accent-warm: #ff9f66;
			--accent-cool: #66d9ef;
			--success-color: #4ade80;
			--danger-color: #ef4444;
			--warning-color: #fbbf24;
			--info-color: #3b82f6;
			--light-color: #fafafa;
			--gray-50: #f9fafb;
			--gray-100: #f3f4f6;
			--gray-200: #e5e7eb;
			--gray-300: #d1d5db;
			--gray-400: #9ca3af;
			--gray-500: #6b7280;
			--gray-600: #4b5563;
			--gray-700: #374151;
			--gray-800: #1f2937;
			--gray-900: #111827;
			--dark-bg: #0f172a;
			--dark-surface: #1e293b;
			--dark-border: #334155;
			--font-primary: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
			--font-display: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
			--shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
			--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
			--shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
			--shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
			--shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
		}

		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
			background-attachment: fixed;
			font-family: var(--font-primary);
			min-height: 100vh;
			color: #e2e8f0;
			line-height: 1.6;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}

		/* Modern Navbar */
		.navbar {
			background: rgba(30, 41, 59, 0.8) !important;
			backdrop-filter: blur(20px);
			-webkit-backdrop-filter: blur(20px);
			border-bottom: 1px solid rgba(51, 65, 85, 0.5);
			padding: 0.875rem 0;
			transition: all 0.3s ease;
		}

		.navbar-brand {
			font-weight: 700;
			font-size: 1.5rem;
			letter-spacing: -0.5px;
			color: #fff !important;
			text-decoration: none;
		}

		.navbar-brand:hover {
			color: var(--primary-color) !important;
			transform: translateY(-1px);
			transition: all 0.2s ease;
		}

		.nav-link {
			font-weight: 500;
			color: #cbd5e1 !important;
			padding: 0.5rem 1rem !important;
			border-radius: 8px;
			margin: 0 0.25rem;
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			position: relative;
		}

		.nav-link:hover {
			background: rgba(139, 126, 248, 0.1);
			color: var(--primary-color) !important;
			transform: translateY(-1px);
		}

		.nav-link i {
			margin-right: 0.5rem;
		}

		/* Story Cards - Modern Organic Design */
		.story-card {
			transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
			border: none;
			background: rgba(30, 41, 59, 0.6);
			backdrop-filter: blur(10px);
			-webkit-backdrop-filter: blur(10px);
			border-radius: 16px;
			overflow: hidden;
			position: relative;
			box-shadow: var(--shadow-lg);
			height: 100%;
			display: flex;
			flex-direction: column;
		}

		.story-card::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 3px;
			background: linear-gradient(90deg, var(--primary-color), var(--accent-warm));
			opacity: 0;
			transition: opacity 0.3s ease;
			z-index: 1;
		}

		.story-card:hover {
			transform: translateY(-6px) scale(1.02);
			box-shadow: 0 20px 40px -10px rgba(139, 126, 248, 0.3), var(--shadow-2xl);
		}

		.story-card:hover::before {
			opacity: 1;
		}

		.story-cover {
			height: 320px;
			object-fit: cover;
			transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
			border-radius: 12px 12px 0 0;
		}

		.story-card:hover .story-cover {
			transform: scale(1.05);
		}

		/* Rating Badge */
		.rating-badge {
			position: absolute;
			top: 12px;
			right: 12px;
			background: #fdcb6e;
			color: #333;
			padding: 5px 10px;
			border-radius: 4px;
			font-weight: 600;
			font-size: 0.85rem;
			z-index: 2;
		}

		.rating-badge::before {
			content: '★ ';
			color: #ff6b6b;
		}

		/* Category Badge */
		.category-badge {
			background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
			color: white;
			padding: 0.375rem 0.75rem;
			border-radius: 20px;
			font-size: 0.75rem;
			font-weight: 500;
			display: inline-block;
			box-shadow: var(--shadow-sm);
			transition: all 0.2s ease;
		}

		.category-badge:hover {
			transform: translateY(-1px);
			box-shadow: var(--shadow-md);
		}

		/* Status Badge */
		.status-badge {
			padding: 0.375rem 0.75rem;
			border-radius: 20px;
			font-size: 0.75rem;
			font-weight: 600;
			display: inline-block;
			text-transform: uppercase;
			letter-spacing: 0.5px;
			box-shadow: var(--shadow-sm);
		}

		.status-ongoing {
			background: linear-gradient(135deg, var(--success-color) 0%, #22c55e 100%);
			color: white;
		}

		.status-completed {
			background: linear-gradient(135deg, var(--info-color) 0%, #2563eb 100%);
			color: white;
		}

		.status-paused {
			background: linear-gradient(135deg, var(--warning-color) 0%, #f59e0b 100%);
			color: #1f2937;
		}

		/* Section Headers */
		.section-header {
			position: relative;
			margin-bottom: 2.5rem;
		}

		.section-header h2 {
			font-size: clamp(1.5rem, 4vw, 2rem);
			font-weight: 700;
			position: relative;
			padding-bottom: 0;
			color: #fff;
			letter-spacing: -0.5px;
			margin-bottom: 0.5rem;
		}

		.section-header p {
			color: #94a3b8;
			font-size: 0.95rem;
			margin-bottom: 0;
		}

		/* View Count */
		.view-count {
			color: #94a3b8 !important;
			font-size: 0.875rem;
			font-weight: 500;
			display: flex;
			align-items: center;
			gap: 0.25rem;
		}

		.view-count i {
			color: var(--primary-color);
			font-size: 0.875rem;
		}

		/* Small text colors */
		small {
			color: #94a3b8 !important;
		}

		/* Ensure all text in cards is visible */
		.story-card small {
			color: #94a3b8 !important;
		}

		.story-card .text-muted {
			color: #94a3b8 !important;
		}

		/* Footer */
		.footer {
			background: rgba(15, 23, 42, 0.8);
			backdrop-filter: blur(20px);
			-webkit-backdrop-filter: blur(20px);
			color: #cbd5e1;
			padding: 3rem 0;
			margin-top: 5rem;
			border-top: 1px solid rgba(51, 65, 85, 0.5);
		}

		.footer h5 {
			color: #fff;
			margin-bottom: 1rem;
		}

		/* Hot Badge */
		.hot-badge {
			position: absolute;
			top: 1rem;
			left: 1rem;
			background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
			color: white;
			padding: 0.5rem 1rem;
			border-radius: 20px;
			font-weight: 700;
			font-size: 0.75rem;
			text-transform: uppercase;
			letter-spacing: 0.5px;
			z-index: 2;
			box-shadow: var(--shadow-md);
			animation: pulse 2s ease-in-out infinite;
		}

		/* Rating Badge */
		.rating-badge {
			position: absolute;
			top: 1rem;
			right: 1rem;
			background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
			color: #1f2937;
			padding: 0.5rem 0.875rem;
			border-radius: 20px;
			font-weight: 700;
			font-size: 0.875rem;
			z-index: 2;
			box-shadow: var(--shadow-md);
			display: flex;
			align-items: center;
			gap: 0.25rem;
		}

		/* Search Box */
		.search-box {
			position: relative;
			z-index: 10000 !important;
		}

		.search-box .form-control {
			background: rgba(30, 41, 59, 0.6);
			border: 2px solid rgba(51, 65, 85, 0.5);
			border-radius: 12px;
			color: #fff;
			padding: 0.625rem 1.25rem;
			transition: all 0.3s ease;
		}

		.search-box .form-control:focus {
			background: rgba(30, 41, 59, 0.8);
			border-color: var(--primary-color);
			box-shadow: 0 0 0 3px rgba(139, 126, 248, 0.2);
		}

		.search-box .form-control::placeholder {
			color: #64748b;
		}

		.search-results {
			position: absolute;
			top: calc(100% + 8px);
			left: 0;
			right: 0;
			background: rgba(30, 41, 59, 0.98) !important;
			backdrop-filter: blur(20px);
			-webkit-backdrop-filter: blur(20px);
			border: 1px solid rgba(51, 65, 85, 0.5);
			border-radius: 16px;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
			z-index: 10000 !important;
			max-height: 400px;
			overflow-y: auto;
		}

		.search-item {
			padding: 1rem 1.25rem;
			border-bottom: 1px solid rgba(51, 65, 85, 0.5);
			cursor: pointer;
			transition: all 0.2s ease;
		}

		.search-item:last-child {
			border-bottom: none;
		}

		.search-item:hover {
			background: rgba(139, 126, 248, 0.1);
		}

		/* Chapter List */
		.chapter-list {
			max-height: 400px;
			overflow-y: auto;
		}

		/* Reader Container */
		.reader-container {
			background: white;
			border-radius: 16px;
			padding: 30px;
			box-shadow: 0 10px 30px rgba(0,0,0,0.1);
		}

		.chapter-image {
			width: 100%;
			height: auto;
			margin-bottom: 15px;
			border-radius: 8px;
			box-shadow: 0 4px 12px rgba(0,0,0,0.1);
		}

		/* Navigation Buttons */
		.navigation-buttons {
			position: fixed;
			bottom: 30px;
			right: 30px;
			z-index: 1000;
		}

		.btn-floating {
			width: 56px;
			height: 56px;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 8px;
			box-shadow: 0 8px 20px rgba(0,0,0,0.3);
			transition: all 0.3s ease;
		}

		.btn-floating:hover {
			transform: scale(1.1) rotate(5deg);
		}

		/* Hero Section */
		.hero-section {
			background: linear-gradient(135deg, rgba(139, 126, 248, 0.3) 0%, rgba(255, 159, 102, 0.2) 100%), 
			            url('https://ocafe.net/wp-content/uploads/2024/10/anh-nen-dep-1.png');
			background-size: cover;
			background-position: center center;
			background-repeat: no-repeat;
			border: 1px solid rgba(139, 126, 248, 0.2);
			color: white;
			padding: 5rem 0;
			border-radius: 24px;
			margin-bottom: 4rem;
			position: relative;
			overflow: hidden;
			backdrop-filter: blur(10px);
			-webkit-backdrop-filter: blur(10px);
			z-index: 1;
		}

		.hero-section::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(30, 41, 59, 0.7) 100%);
			z-index: 1;
		}

		.hero-section > * {
			position: relative;
			z-index: 2;
		}


		/* Card Title */
		.card-title {
			margin-bottom: 0.5rem;
		}

		.card-title a {
			color: #fff !important;
			font-weight: 600;
			transition: color 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			font-size: 1rem;
			line-height: 1.5;
			text-decoration: none;
			display: -webkit-box;
			-webkit-line-clamp: 2;
			-webkit-box-orient: vertical;
			overflow: hidden;
		}

		.card-title a:hover {
			color: var(--primary-color) !important;
		}

		/* Card Text - Author Names */
		.card-text {
			color: #94a3b8 !important;
		}

		.card-text.text-muted {
			color: #94a3b8 !important;
		}

		.card-body {
			padding: 1.25rem;
			flex: 1;
			display: flex;
			flex-direction: column;
			color: #e2e8f0;
		}

		/* Hot Badge */
		.hot-badge {
			position: absolute;
			top: 12px;
			left: 12px;
			background: #ff6b6b;
			color: white;
			padding: 5px 10px;
			border-radius: 4px;
			font-weight: 600;
			font-size: 0.75rem;
			z-index: 2;
		}

		@keyframes pulse {
			0%, 100% { opacity: 1; }
			50% { opacity: 0.7; }
		}

		/* Cards */
		.card {
			background: rgba(30, 41, 59, 0.5);
			backdrop-filter: blur(10px);
			-webkit-backdrop-filter: blur(10px);
			border: 1px solid rgba(51, 65, 85, 0.5);
			border-radius: 16px;
			color: #e2e8f0;
			box-shadow: var(--shadow-lg);
		}

		.card-body {
			background: transparent;
		}

		/* Buttons */
		.btn {
			border-radius: 12px;
			font-weight: 600;
			padding: 0.625rem 1.5rem;
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			border: none;
		}

		.btn-primary {
			background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
			color: white;
			box-shadow: 0 4px 14px 0 rgba(139, 126, 248, 0.4);
		}

		.btn-primary:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 20px 0 rgba(139, 126, 248, 0.5);
		}

		.btn-outline-primary {
			border: 2px solid var(--primary-color);
			color: var(--primary-color);
			background: transparent;
		}

		.btn-outline-primary:hover {
			background: var(--primary-color);
			color: white;
			transform: translateY(-2px);
		}

		.btn-outline-light {
			border: 2px solid rgba(255, 255, 255, 0.3);
			color: #fff;
			background: transparent;
		}

		.btn-outline-light:hover {
			background: rgba(255, 255, 255, 0.1);
			border-color: rgba(255, 255, 255, 0.5);
			transform: translateY(-1px);
		}

		.btn-lg {
			padding: 0.875rem 2rem;
			font-size: 1.1rem;
			border-radius: 16px;
		}


		/* Search results - additional styling to ensure it's on top */
		.search-results {
			background: #252525;
			position: fixed !important;
			border: 1px solid #444;
		}

		.search-item:hover {
			background: #2d3436;
		}

		/* Dropdown menu styling */
		.dropdown-menu {
			background: rgba(30, 41, 59, 0.98) !important;
			backdrop-filter: blur(20px);
			-webkit-backdrop-filter: blur(20px);
			border: 1px solid rgba(51, 65, 85, 0.5) !important;
			border-radius: 12px !important;
			box-shadow: var(--shadow-2xl) !important;
			z-index: 999999 !important;
			padding: 0.5rem 0;
			position: absolute !important;
		}

		.dropdown-item {
			color: #e2e8f0 !important;
			padding: 0.75rem 1.25rem;
			transition: all 0.2s ease;
		}

		.dropdown-item:hover {
			background: rgba(139, 126, 248, 0.15) !important;
			color: var(--primary-color) !important;
		}

		.dropdown-item.active {
			background: var(--primary-color) !important;
			color: white !important;
		}

		.dropdown-divider {
			border-color: rgba(51, 65, 85, 0.5) !important;
			margin: 0.5rem 0;
		}

		.nav-item.dropdown {
			z-index: 999999 !important;
		}

		/* Ensure navbar is on top */
		.navbar {
			z-index: 9999 !important;
		}

		.navbar-collapse {
			z-index: 9999 !important;
		}

		/* Hero section lower z-index */
		.container,
		.container-fluid {
			position: relative;
		}

		/* Breadcrumb */
		.breadcrumb {
			background: transparent;
		}

		.breadcrumb-item a {
			color: #6c5ce7;
		}

		.breadcrumb-item.active {
			color: #aaa;
		}

		/* Form controls */
		.form-control, .form-select {
			background: #252525;
			border: 1px solid #444;
			color: #e0e0e0;
		}

		.form-control:focus, .form-select:focus {
			background: #2d2d2d;
			border-color: #6c5ce7;
			color: #e0e0e0;
		}

		/* Pagination */
		.page-link {
			background: #252525;
			border: 1px solid #444;
			color: #e0e0e0;
		}

		.page-link:hover {
			background: #2d3436;
			border-color: #6c5ce7;
			color: #6c5ce7;
		}

		.page-item.active .page-link {
			background: #6c5ce7;
			border-color: #6c5ce7;
		}

		/* Table */
		.table {
			color: #e0e0e0;
		}

		.table-hover tbody tr:hover {
			background: #2d3436;
		}

		.table-light {
			background: #2d2d2d;
			color: #e0e0e0;
		}

		/* Text colors */
		.text-muted {
			color: #94a3b8 !important;
		}

		/* Fix all text colors in cards */
		.story-card,
		.story-card * {
			color: #e2e8f0;
		}

		.story-card .card-text {
			color: #94a3b8 !important;
		}

		.story-card h5,
		.story-card h5 a {
			color: #fff !important;
		}

		/* Responsive Design */
		@media (max-width: 992px) {
			.container {
				padding-left: 1rem;
				padding-right: 1rem;
			}

			.section-header h2 {
				font-size: 1.5rem;
			}

			.story-cover {
				height: 280px;
			}
		}

		@media (max-width: 768px) {
			.hero-section {
				padding: 3rem 0;
				margin-bottom: 3rem;
			}

			.story-cover {
				height: 240px;
			}

			.story-card {
				margin-bottom: 1.5rem;
			}

			.navbar {
				padding: 0.75rem 0;
			}

			.navbar-brand {
				font-size: 1.25rem;
			}

			.btn-lg {
				padding: 0.75rem 1.5rem;
				font-size: 1rem;
			}
		}

		@media (max-width: 576px) {
			.section-header {
				margin-bottom: 1.5rem;
			}

			.story-cover {
				height: 220px;
			}

			.card-body {
				padding: 1rem;
			}

			.hero-section {
				padding: 2.5rem 0;
			}
		}

		/* Smooth Scroll */
		html {
			scroll-behavior: smooth;
		}

		/* Focus styles for accessibility */
		*:focus-visible {
			outline: 2px solid var(--primary-color);
			outline-offset: 2px;
			border-radius: 4px;
		}

		/* Custom scrollbar */
		::-webkit-scrollbar {
			width: 12px;
			height: 12px;
		}

		::-webkit-scrollbar-track {
			background: rgba(15, 23, 42, 0.5);
			border-radius: 6px;
		}

		::-webkit-scrollbar-thumb {
			background: var(--primary-color);
			border-radius: 6px;
			transition: background 0.3s ease;
		}

		::-webkit-scrollbar-thumb:hover {
			background: var(--primary-dark);
		}
	</style>
</head>
<body>
	<!-- Navigation -->
	<nav class="navbar navbar-expand-lg navbar-dark">
		<div class="container">
		<a class="navbar-brand" href="<?php echo Uri::base(); ?>">
			<i class="fas fa-book-open me-2"></i>
			NetTruyen
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
				</ul>
				
				<!-- Search Box -->
				<form class="d-flex search-box" id="searchForm" action="<?php echo Uri::base(); ?>client/search" method="GET">
					<div class="input-group">
						<input class="form-control" type="search" name="q" placeholder="Tìm kiếm truyện..." id="searchInput">
						<button class="btn btn-outline-light" type="submit">
							<i class="fas fa-search"></i>
						</button>
					</div>
				</form>
				
				<ul class="navbar-nav ms-3">
					<?php if (Session::get('user_id')): ?>
						<!-- User is logged in -->
		<li class="nav-item dropdown" style="position: relative; z-index: 10000;">
			<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="fas fa-user me-1"></i>
				<?php echo Security::htmlentities(Session::get('user_full_name', Session::get('user_username', 'User'))); ?>
			</a>
			<ul class="dropdown-menu dropdown-menu-end" style="position: absolute; z-index: 10001;">
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
	<footer class="footer" style="background: #1a1a1a; border-top: 1px solid #444;">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
				<h5 style="color: #fff;">NetTruyen</h5>
				<p style="color: #aaa;">Nền tảng đọc truyện tranh online miễn phí</p>
				</div>
				<div class="col-md-6 text-md-end">
					<p style="color: #aaa;">&copy; 2024 NetTruyen. All rights reserved.</p>
				</div>
			</div>
		</div>
	</footer>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	
	<script>
		// Simple search form submission
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
