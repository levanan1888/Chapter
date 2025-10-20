<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Total Admins Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tổng số Admin</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_admins; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Status Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Trạng thái tài khoản</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $admin->is_active ? 'Hoạt động' : 'Không hoạt động'; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Last Login Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Lần đăng nhập cuối</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $admin->last_login ? date('d/m/Y H:i', strtotime($admin->last_login)) : 'Chưa đăng nhập'; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Chào mừng</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo htmlentities($admin->full_name ?: $admin->username); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Account Information -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin tài khoản</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Username:</strong></td>
                            <td><?php echo htmlentities($admin->username); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><?php echo htmlentities($admin->email); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Họ tên:</strong></td>
                            <td><?php echo htmlentities($admin->full_name ?: 'Chưa cập nhật'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Ngày tạo:</strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($admin->created_at)); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Lần cập nhật cuối:</strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($admin->updated_at)); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hành động nhanh</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo Uri::create('admin/manage'); ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-users-cog"></i> Quản lý Admin
                    </a>
                    <a href="<?php echo Uri::create('admin/add'); ?>" class="btn btn-success btn-lg">
                        <i class="fas fa-user-plus"></i> Thêm Admin mới
                    </a>
                    <a href="<?php echo Uri::create('admin/logout'); ?>" class="btn btn-danger btn-lg">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($recent_admins)): ?>
<!-- Recent Admins -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Admin gần đây</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Họ tên</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_admins as $recent_admin): ?>
                            <tr>
                                <td><?php echo htmlentities($recent_admin->username); ?></td>
                                <td><?php echo htmlentities($recent_admin->email); ?></td>
                                <td><?php echo htmlentities($recent_admin->full_name ?: '-'); ?></td>
                                <td>
                                    <?php if ($recent_admin->is_active): ?>
                                        <span class="badge badge-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Không hoạt động</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($recent_admin->created_at)); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


