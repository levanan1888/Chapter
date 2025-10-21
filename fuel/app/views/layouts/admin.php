<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="<?php echo Security::fetch_token(); ?>">
	<title><?php echo isset($title) ? $title . ' - Admin Panel' : 'Admin Panel'; ?></title>

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

		.sidebar {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			min-height: 100vh;
			position: fixed;
			top: 0;
			left: 0;
			width: 250px;
			z-index: 1000;
			box-shadow: 2px 0 10px rgba(0,0,0,0.1);
		}

		.sidebar .nav-link {
			color: rgba(255,255,255,0.8);
			padding: 12px 20px;
			border-radius: 8px;
			margin: 4px 12px;
			transition: all 0.3s ease;
		}

		.sidebar .nav-link:hover,
		.sidebar .nav-link.active {
			background-color: rgba(255,255,255,0.1);
			color: white;
			transform: translateX(5px);
		}

		.sidebar .nav-link i {
			width: 20px;
			margin-right: 10px;
		}

		.main-content {
			margin-left: 250px;
			padding: 20px;
		}

		.navbar {
			background: white;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			margin-bottom: 20px;
		}

		.card {
			border: none;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			border-radius: 12px;
		}

		.btn {
			border-radius: 8px;
			padding: 8px 16px;
		}

		.table {
			background: white;
			border-radius: 12px;
			overflow: hidden;
		}

		.table th {
			background-color: #f8f9fa;
			border: none;
			font-weight: 600;
		}

		.table td {
			border: none;
			border-bottom: 1px solid #f0f0f0;
		}

		.badge {
			padding: 6px 12px;
			border-radius: 20px;
		}

		.form-control {
			border-radius: 8px;
			border: 1px solid #e0e0e0;
		}

		.form-control:focus {
			border-color: var(--primary-color);
			box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
		}

		.alert {
			border-radius: 8px;
			border: none;
		}

		/* Login page styles */
		.login-container {
			min-height: 100vh;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.login-card {
			background: white;
			border-radius: 20px;
			box-shadow: 0 20px 40px rgba(0,0,0,0.1);
			padding: 40px;
			width: 100%;
			max-width: 400px;
		}

		.login-logo {
			text-align: center;
			margin-bottom: 30px;
		}

		.login-logo h2 {
			color: var(--primary-color);
			font-weight: bold;
		}

		/* Dark mode */
		.dark-mode {
			background-color: #1a1a1a;
			color: #e0e0e0;
		}

		.dark-mode .sidebar {
			background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
		}

		.dark-mode .card {
			background-color: #2d2d2d;
			color: #e0e0e0;
		}

		.dark-mode .navbar {
			background-color: #2d2d2d;
		}

		.dark-mode .table {
			background-color: #2d2d2d;
			color: #e0e0e0;
		}

		.dark-mode .table th {
			background-color: #3d3d3d;
		}

		.dark-mode .form-control {
			background-color: #3d3d3d;
			border-color: #555;
			color: #e0e0e0;
        }
    </style>
</head>
<body>
	<?php if (isset($is_login_page) && $is_login_page): ?>
		<!-- Login Page -->
		<div class="login-container">
			<div class="login-card">
				<div class="login-logo">
					<i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
					<h2>Admin Panel</h2>
					<p class="text-muted">Đăng nhập để tiếp tục</p>
				</div>
				
				<?php if (isset($content)): ?>
    <?php echo $content; ?>
				<?php endif; ?>
			</div>
		</div>
<?php else: ?>
		<!-- Admin Dashboard -->
		<div class="sidebar">
			<div class="p-3">
				<h4 class="text-white mb-0">
					<i class="fas fa-shield-alt me-2"></i>
					Admin Panel
				</h4>
			</div>
			
			<nav class="nav flex-column">
				<a class="nav-link <?php echo Uri::segment(2) == 'dashboard' ? 'active' : ''; ?>" href="<?php echo Uri::base(); ?>admin">
					<i class="fas fa-tachometer-alt"></i>
					Dashboard
				</a>
				<a class="nav-link <?php echo Uri::segment(2) == 'stories' ? 'active' : ''; ?>" href="<?php echo Uri::base(); ?>admin/stories">
					<i class="fas fa-book"></i>
					Quản lý Truyện
				</a>
				<a class="nav-link <?php echo Uri::segment(2) == 'categories' ? 'active' : ''; ?>" href="<?php echo Uri::base(); ?>admin/categories">
					<i class="fas fa-tags"></i>
					Quản lý Danh mục
				</a>
				<a class="nav-link <?php echo Uri::segment(2) == 'authors' ? 'active' : ''; ?>" href="<?php echo Uri::base(); ?>admin/authors">
					<i class="fas fa-user-edit"></i>
					Quản lý Tác giả
				</a>
				<a class="nav-link <?php echo Uri::segment(2) == 'users' ? 'active' : ''; ?>" href="<?php echo Uri::base(); ?>admin/users">
					<i class="fas fa-users"></i>
					Quản lý Users
				</a>
			</nav>
		</div>

		<div class="main-content">
			<!-- Top Navigation -->
			<nav class="navbar navbar-expand-lg">
				<div class="container-fluid">
					<span class="navbar-brand mb-0 h1">
						<?php echo isset($title) ? $title : 'Dashboard'; ?>
					</span>
					
					<div class="navbar-nav ms-auto">
						<div class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
								<i class="fas fa-user-circle me-1"></i>
								<?php echo \Session::get('admin_username', 'Admin'); ?>
							</a>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="<?php echo Uri::base(); ?>admin/logout">
									<i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
								</a></li>
							</ul>
						</div>
					</div>
				</div>
			</nav>

            <!-- Main Content -->
			<?php if (isset($content)): ?>
                    <?php echo $content; ?>
			<?php endif; ?>
                </div>
	<?php endif; ?>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	
    <script>
		// Auto-hide alerts
		setTimeout(function() {
			$('.alert').fadeOut();
		}, 5000);

		// Confirm delete actions
		$('.btn-danger').on('click', function(e) {
			if (!confirm('Bạn có chắc chắn muốn xóa?')) {
				e.preventDefault();
			}
		});

		// Form validation
		$('form').on('submit', function() {
			$(this).find('button[type="submit"]').prop('disabled', true);
		});
    </script>
</body>
</html>