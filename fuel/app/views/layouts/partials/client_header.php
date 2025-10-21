<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Đọc truyện tranh online miễn phí">
	<meta name="keywords" content="truyện tranh, manga, comic, đọc truyện online">
	<meta name="author" content="Comic Reader">
	
	
	
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	
	<!-- Custom CSS -->
	<style>
		:root {
			--primary-color: #ff6b35;
			--secondary-color: #2c3e50;
			--accent-color: #f39c12;
			--success-color: #27ae60;
			--warning-color: #f39c12;
			--danger-color: #e74c3c;
			--info-color: #3498db;
			
			--light-bg: #ffffff;
			--light-card: #ffffff;
			--light-text: #2c3e50;
			--light-text-secondary: #7f8c8d;
			--light-border: #ecf0f1;
			
			--dark-bg: #1a1a1a;
			--dark-card: #2d2d2d;
			--dark-text: #ffffff;
			--dark-text-secondary: #b0b0b0;
			--dark-border: #404040;
		}

		body {
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
			background-color: var(--light-bg);
			color: var(--light-text);
			transition: all 0.3s ease;
		}

		/* Dark mode styles */
		body.dark-mode {
			background-color: var(--dark-bg);
			color: var(--dark-text);
		}

		/* Navbar styles */
		.navbar {
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
			transition: all 0.3s ease;
		}

		body.dark-mode .navbar {
			background-color: var(--dark-card) !important;
			border-bottom: 1px solid var(--dark-border);
		}

		.navbar-brand {
			font-weight: 700;
			font-size: 1.5rem;
			color: var(--primary-color) !important;
			text-decoration: none;
		}

		.navbar-brand:hover {
			color: var(--accent-color) !important;
		}

		.nav-link {
			font-weight: 500;
			color: var(--light-text) !important;
			transition: color 0.3s ease;
		}

		body.dark-mode .nav-link {
			color: var(--dark-text) !important;
		}

		.nav-link:hover {
			color: var(--primary-color) !important;
		}

		/* Search box styles */
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
			box-shadow: 0 4px 6px rgba(0,0,0,0.1);
			z-index: 1000;
			display: none;
			max-height: 300px;
			overflow-y: auto;
		}

		body.dark-mode .search-results {
			background: var(--dark-card);
			border-color: var(--dark-border);
		}

		.search-item {
			padding: 12px 16px;
			border-bottom: 1px solid #eee;
			cursor: pointer;
			transition: background-color 0.2s;
		}

		body.dark-mode .search-item {
			border-bottom-color: var(--dark-border);
		}

		.search-item:hover {
			background-color: #f8f9fa;
		}

		body.dark-mode .search-item:hover {
			background-color: var(--dark-bg);
		}

		.search-item:last-child {
			border-bottom: none;
		}

		.search-item-title {
			font-weight: 600;
			color: var(--light-text);
			margin-bottom: 4px;
		}

		body.dark-mode .search-item-title {
			color: var(--dark-text);
		}

		.search-item-author {
			font-size: 0.875rem;
			color: var(--light-text-secondary);
		}

		body.dark-mode .search-item-author {
			color: var(--dark-text-secondary);
		}

		/* Theme toggle */
		.theme-toggle {
			background: none;
			border: none;
			font-size: 1.2rem;
			color: var(--light-text);
			cursor: pointer;
			padding: 8px;
			border-radius: 50%;
			transition: all 0.3s ease;
		}

		.theme-toggle:hover {
			background-color: rgba(0,0,0,0.1);
			color: var(--primary-color);
		}

		body.dark-mode .theme-toggle {
			color: var(--dark-text);
		}

		body.dark-mode .theme-toggle:hover {
			background-color: rgba(255,255,255,0.1);
		}

		/* Card styles */
		.card {
			border: none;
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
			transition: all 0.3s ease;
		}

		body.dark-mode .card {
			background-color: var(--dark-card);
			box-shadow: 0 2px 8px rgba(0,0,0,0.3);
		}

		.card:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(0,0,0,0.15);
		}

		body.dark-mode .card:hover {
			box-shadow: 0 4px 12px rgba(0,0,0,0.4);
		}

		/* Form controls */
		.form-control {
			border: 1px solid #ddd;
			transition: all 0.3s ease;
		}

		body.dark-mode .form-control {
			background-color: var(--dark-card);
			border-color: var(--dark-border);
			color: var(--dark-text);
		}

		.form-control:focus {
			border-color: var(--primary-color);
			box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
		}

		/* Footer */
		.footer {
			background-color: var(--secondary-color);
			color: white;
			padding: 2rem 0;
			margin-top: 3rem;
		}

		body.dark-mode .footer {
			background-color: var(--dark-bg);
		}

		/* Responsive */
		@media (max-width: 768px) {
			.navbar-brand {
				font-size: 1.25rem;
			}
			
			.search-box {
				margin-top: 1rem;
			}
		}
	</style>
</head>
<body>
