<?php if (isset($error_message) && !empty($error_message)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?php echo Security::htmlentities($error_message); ?>
    </div>
<?php endif; ?>

<?php if (isset($success_message) && !empty($success_message)): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo Security::htmlentities($success_message); ?>
    </div>
<?php endif; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">Đăng ký</h2>
                        <p class="text-muted">Tạo tài khoản mới để bắt đầu</p>
                    </div>

                    <form method="POST" action="<?php echo Uri::base(); ?>user/register">
                        <?php echo Form::csrf(); ?>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-2"></i>Tên đăng nhập *
                            </label>
                            <input type="text" class="form-control form-control-lg" id="username" name="username" 
                                   value="<?php echo isset($form_data['username']) ? $form_data['username'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email *
                            </label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                   value="<?php echo isset($form_data['email']) ? $form_data['email'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">
                                <i class="fas fa-id-card me-2"></i>Họ và tên
                            </label>
                            <input type="text" class="form-control form-control-lg" id="full_name" name="full_name" 
                                   value="<?php echo isset($form_data['full_name']) ? $form_data['full_name'] : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Mật khẩu *
                            </label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                            <small class="text-muted">Mật khẩu phải có ít nhất 6 ký tự</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Xác nhận mật khẩu *
                            </label>
                            <input type="password" class="form-control form-control-lg" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-user-plus me-2"></i>Đăng ký
                        </button>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                Đã có tài khoản? <a href="<?php echo Uri::base(); ?>user/login">Đăng nhập ngay</a>
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

