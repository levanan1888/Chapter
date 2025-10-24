<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>Thông tin tài khoản
                    </h4>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($user)): ?>
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <div class="mb-3">
                                    <i class="fas fa-user-circle fa-5x text-primary"></i>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <h3 class="mb-3"><?php echo Security::htmlentities($user->full_name ?: $user->username); ?></h3>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-user me-2"></i>
                                    <strong>Username:</strong> <?php echo Security::htmlentities($user->username); ?>
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-envelope me-2"></i>
                                    <strong>Email:</strong> <?php echo Security::htmlentities($user->email); ?>
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-calendar me-2"></i>
                                    <strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($user->created_at)); ?>
                                </p>
                                <?php if ($user->last_login): ?>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-clock me-2"></i>
                                        <strong>Đăng nhập lần cuối:</strong> <?php echo date('d/m/Y H:i', strtotime($user->last_login)); ?>
                                    </p>
                                <?php endif; ?>
                                <p class="mb-2">
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Đang hoạt động
                                    </span>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Thông tin chi tiết
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td width="30%"><strong>ID:</strong></td>
                                                <td><?php echo $user->id; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Username:</strong></td>
                                                <td><?php echo Security::htmlentities($user->username); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td><?php echo Security::htmlentities($user->email); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Họ và tên:</strong></td>
                                                <td><?php echo Security::htmlentities($user->full_name ?: 'Chưa cập nhật'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Loại tài khoản:</strong></td>
                                                <td>
                                                    <span class="badge bg-info">Người dùng</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Trạng thái:</strong></td>
                                                <td>
                                                    <?php if ($user->is_active): ?>
                                                        <span class="badge bg-success">Đang hoạt động</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Đã khóa</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Ngày tạo:</strong></td>
                                                <td><?php echo date('d/m/Y H:i:s', strtotime($user->created_at)); ?></td>
                                            </tr>
                                            <?php if ($user->last_login): ?>
                                                <tr>
                                                    <td><strong>Đăng nhập lần cuối:</strong></td>
                                                    <td><?php echo date('d/m/Y H:i:s', strtotime($user->last_login)); ?></td>
                                                </tr>
                                            <?php else: ?>
                                                <tr>
                                                    <td><strong>Đăng nhập lần cuối:</strong></td>
                                                    <td>Chưa có</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 text-center">
                                <a href="<?php echo Uri::base(); ?>client" class="btn btn-primary">
                                    <i class="fas fa-home me-2"></i>Về trang chủ
                                </a>
                                <a href="<?php echo Uri::base(); ?>user/logout" class="btn btn-outline-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Không tìm thấy thông tin người dùng.
                        </div>
                        <div class="text-center">
                            <a href="<?php echo Uri::base(); ?>user/login" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

