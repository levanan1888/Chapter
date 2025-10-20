<!-- SB Admin 2 Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
	<!-- Sidebar - Brand -->
	<a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo Uri::create('admin/dashboard'); ?>">
		<div class="sidebar-brand-icon rotate-n-15">
			<i class="fas fa-laugh-wink"></i>
		</div>
		<div class="sidebar-brand-text mx-3">Admin</div>
	</a>

	<hr class="sidebar-divider my-0">
	<li class="nav-item <?php echo (Uri::segment(2) == 'dashboard' || Uri::segment(2) == '') ? 'active' : ''; ?>">
		<a class="nav-link" href="<?php echo Uri::create('admin/dashboard'); ?>">
			<i class="fas fa-fw fa-tachometer-alt"></i>
			<span>Dashboard</span></a>
	</li>

	<hr class="sidebar-divider">
	<li class="nav-item <?php echo Uri::segment(2) == 'manage' ? 'active' : ''; ?>">
		<a class="nav-link" href="<?php echo Uri::create('admin/manage'); ?>">
			<i class="fas fa-fw fa-users-cog"></i>
			<span>Quản lý Admin</span></a>
	</li>
	<li class="nav-item <?php echo Uri::segment(2) == 'add' ? 'active' : ''; ?>">
		<a class="nav-link" href="<?php echo Uri::create('admin/add'); ?>">
			<i class="fas fa-fw fa-user-plus"></i>
			<span>Thêm Admin</span></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo Uri::create('admin/logout'); ?>">
			<i class="fas fa-fw fa-sign-out-alt"></i>
			<span>Đăng xuất</span></a>
	</li>

	<!-- Sidebar Toggler (Sidebar) -->
	<hr class="sidebar-divider d-none d-md-block">
	<div class="text-center d-none d-md-inline">
		<button class="rounded-circle border-0" id="sidebarToggle"></button>
	</div>
</ul>