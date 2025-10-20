<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Sửa Admin</h1>
    <div>
        <a href="<?php echo Uri::create('admin/manage'); ?>" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
        </a>
    </div>
</div>

<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Thông tin Admin</h6>
    </div>
    <div class="card-body">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo Uri::create('admin/edit/' . $admin->id); ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="username">Username <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               value="<?php echo htmlentities($admin->username); ?>" 
                               required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               value="<?php echo htmlentities($admin->email); ?>" 
                               required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="full_name">Họ và tên</label>
                        <input type="text" 
                               class="form-control" 
                               id="full_name" 
                               name="full_name" 
                               value="<?php echo htmlentities($admin->full_name); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="is_active">Trạng thái</label>
                        <select class="form-control" id="is_active" name="is_active">
                            <option value="1" <?php echo $admin->is_active ? 'selected' : ''; ?>>Hoạt động</option>
                            <option value="0" <?php echo !$admin->is_active ? 'selected' : ''; ?>>Không hoạt động</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password">Mật khẩu mới</label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Để trống nếu không muốn đổi mật khẩu">
                        <small class="form-text text-muted">Chỉ nhập mật khẩu mới nếu muốn thay đổi</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Cập nhật Admin
                </button>
                <a href="<?php echo Uri::create('admin/manage'); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Admin Info Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Thông tin bổ sung</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID:</strong> <?php echo $admin->id; ?></p>
                <p><strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i:s', strtotime($admin->created_at)); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Cập nhật lần cuối:</strong> <?php echo date('d/m/Y H:i:s', strtotime($admin->updated_at)); ?></p>
                <p><strong>Lần đăng nhập cuối:</strong> 
                    <?php if ($admin->last_login): ?>
                        <?php echo date('d/m/Y H:i:s', strtotime($admin->last_login)); ?>
                    <?php else: ?>
                        <span class="text-muted">Chưa đăng nhập</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>
