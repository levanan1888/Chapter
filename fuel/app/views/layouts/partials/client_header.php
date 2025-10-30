<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Đọc truyện tranh online miễn phí">
	<meta name="keywords" content="truyện tranh, manga, comic, đọc truyện online">
	<meta name="author" content="An - NetTruyen">
	
	
	
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	
	<!-- Custom CSS -->
	<style>
		:root {
			/* Color System - Unified with main layout */
			--primary-color: #8b7ef8;
			--primary-dark: #6b5ddc;
			--secondary-color: #a8a3ff;
			--accent-warm: #ff9f66;
			--accent-cool: #66d9ef;
			--success-color: #4ade80;
			--danger-color: #ef4444;
			--warning-color: #fbbf24;
			--info-color: #3b82f6;
			
			/* Text Colors */
			--text-primary: #ffffff;
			--text-secondary: #e2e8f0;
			--text-muted: #94a3b8;
			--text-light: #cbd5e1;
			--text-dark: #1f2937;
			
			/* Background Colors */
			--bg-primary: #0f172a;
			--bg-secondary: #1e293b;
			--bg-surface: rgba(30, 41, 59, 0.6);
			--bg-card: rgba(15, 23, 42, 0.8);
			--bg-hover: rgba(30, 41, 59, 0.8);
			
			/* Border Colors */
			--border-primary: rgba(51, 65, 85, 0.5);
			--border-light: rgba(51, 65, 85, 0.3);
			
			/* Font System */
			--font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', 'Roboto', sans-serif;
			--font-size-xs: 0.75rem;
			--font-size-sm: 0.875rem;
			--font-size-base: 1rem;
			--font-size-lg: 1.125rem;
			--font-size-xl: 1.25rem;
			--font-size-2xl: 1.5rem;
			--font-size-3xl: 1.875rem;
			--font-size-4xl: 2.25rem;
			
			/* Font Weights */
			--font-weight-normal: 400;
			--font-weight-medium: 500;
			--font-weight-semibold: 600;
			--font-weight-bold: 700;
			
			/* Line Heights */
			--line-height-tight: 1.25;
			--line-height-normal: 1.5;
			--line-height-relaxed: 1.75;
		}

		body {
			font-family: var(--font-family);
			font-size: var(--font-size-base);
			font-weight: var(--font-weight-normal);
			line-height: var(--line-height-normal);
			background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
			background-attachment: fixed;
			color: var(--text-secondary);
			transition: all 0.3s ease;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}

		/* Dark mode styles */
		body.dark-mode {
			background-color: var(--dark-bg);
			color: var(--dark-text);
		}

		/* Navbar styles - Compact and aligned */
		.navbar {
			background: rgba(30, 41, 59, 0.95) !important;
			backdrop-filter: blur(20px);
			-webkit-backdrop-filter: blur(20px);
			border-bottom: 1px solid rgba(51, 65, 85, 0.3);
			padding: 0.5rem 0;
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
			transition: all 0.3s ease;
		}

		/* Circular brand/logo images */
		.brand-logo {
			border-radius: 50%;
			object-fit: cover;
			display: inline-block;
		}

		.navbar-brand {
			font-weight: 700;
			font-size: 1.4rem;
			color: #fff !important;
			text-decoration: none;
			display: flex;
			align-items: center;
			gap: 0.5rem;
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
			display: flex;
			align-items: center;
			gap: 0.5rem;
		}

		.nav-link:hover {
			background: rgba(139, 126, 248, 0.1);
			color: var(--primary-color) !important;
			transform: translateY(-1px);
		}

		/* Compact navbar items */
		.navbar-nav {
			align-items: center;
		}

		.navbar-nav .nav-item {
			display: flex;
			align-items: center;
		}

		/* Navbar container alignment */
		.navbar .container {
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		.navbar .navbar-collapse {
			display: flex;
			align-items: center;
			justify-content: space-between;
			flex-grow: 1;
		}

		/* User dropdown alignment */
		.navbar-nav .dropdown {
			position: relative;
		}

		.navbar-nav .dropdown-menu {
			position: absolute;
			top: 100%;
			right: 0;
			left: auto;
			z-index: 1000;
		}

		/* Search box styles - Compact and modern */
		.search-box {
			position: relative;
			flex: 1;
			max-width: 400px;
			margin: 0 1rem;
		}

		.search-box .form-control {
			background: rgba(30, 41, 59, 0.6);
			border: 2px solid rgba(51, 65, 85, 0.5);
			border-radius: 12px;
			color: #fff;
			padding: 0.5rem 1rem;
			height: 40px;
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

		.search-box .btn {
			height: 40px;
			padding: 0 1rem;
			border-radius: 0 12px 12px 0;
			border-left: none;
			background: var(--primary-color);
			border-color: var(--primary-color);
		}

		.search-box .btn:hover {
			background: var(--primary-dark);
			border-color: var(--primary-dark);
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
			border-radius: 12px;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
			z-index: 10000 !important;
			display: none;
			max-height: 300px;
			overflow-y: auto;
		}

		.search-item {
			padding: 0.75rem 1rem;
			border-bottom: 1px solid rgba(51, 65, 85, 0.3);
			cursor: pointer;
			transition: all 0.2s ease;
		}

		.search-item:last-child {
			border-bottom: none;
		}

		.search-item:hover {
			background: rgba(139, 126, 248, 0.1);
		}

		.search-item-title {
			font-weight: 600;
			color: #fff;
			margin-bottom: 0.25rem;
			font-size: 0.9rem;
		}

		.search-item-author {
			font-size: 0.8rem;
			color: #94a3b8;
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

		/* Responsive - Mobile optimized */
		@media (max-width: 992px) {
			.search-box {
				max-width: 300px;
				margin: 0 0.5rem;
			}
		}

		@media (max-width: 768px) {
			.navbar {
				padding: 0.75rem 0;
			}

			.navbar-brand {
				font-size: 1.2rem;
			}

			.nav-link {
				padding: 0.4rem 0.8rem !important;
				font-size: 0.9rem;
			}

			.search-box {
				margin: 0.5rem 0 0 0;
				max-width: 100%;
				order: 3;
				width: 100%;
			}

			.navbar-collapse {
				text-align: center;
			}

			.navbar-nav {
				margin: 0.5rem 0;
			}

			.navbar-nav .nav-item {
				justify-content: center;
			}
		}

		@media (max-width: 576px) {
			.navbar-brand {
				font-size: 1.1rem;
			}

			.nav-link {
				padding: 0.3rem 0.6rem !important;
				font-size: 0.85rem;
			}

			.search-box .form-control {
				height: 36px;
				font-size: 0.9rem;
			}

			.search-box .btn {
				height: 36px;
				padding: 0 0.8rem;
			}
		}
	</style>
</head>
<body>
