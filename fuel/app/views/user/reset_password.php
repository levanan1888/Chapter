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
                        <div class="mb-3">
                            <i class="fas fa-lock text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="fw-bold text-primary">Đặt lại mật khẩu</h2>
                        <?php if (isset($valid_token) && $valid_token): ?>
                            <p class="text-muted">Tạo mật khẩu mới cho tài khoản của bạn</p>
                        <?php else: ?>
                            <p class="text-muted">Mã xác thực không hợp lệ hoặc đã hết hạn</p>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($success_message) && !empty($success_message)): ?>
                        <!-- Hiển thị khi thành công -->
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-success mb-3">Thành công!</h4>
                            <p class="text-muted mb-4">
                                Mật khẩu của bạn đã được đặt lại thành công.
                            </p>
                            <a href="<?php echo Uri::base(); ?>user/login" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập ngay
                            </a>
                        </div>
                    <?php elseif (isset($valid_token) && $valid_token): ?>
                        <!-- Hiển thị form đặt lại mật khẩu -->
                        <form method="POST" action="<?php echo Uri::base(); ?>user/reset-password">
                            <?php echo Form::csrf(); ?>
                            
                            <div class="mb-3">
                                <label for="email_display" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email_display" 
                                       value="<?php echo Security::htmlentities($email); ?>" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Mật khẩu mới *
                                </label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" 
                                       placeholder="Nhập mật khẩu mới" required>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Mật khẩu phải có ít nhất 6 ký tự
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Xác nhận mật khẩu *
                                </label>
                                <input type="password" class="form-control form-control-lg" id="confirm_password" name="confirm_password" 
                                       placeholder="Nhập lại mật khẩu mới" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-save me-2"></i>Đặt lại mật khẩu
                            </button>
                        </form>
                    <?php else: ?>
                        <!-- Hiển thị khi token không hợp lệ -->
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="fas fa-exclamation-circle text-warning" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-warning mb-3">Không thể đặt lại mật khẩu</h4>
                            <p class="text-muted mb-4">
                                Mã xác thực không hợp lệ, đã hết hạn hoặc đã được sử dụng. 
                                Vui lòng yêu cầu đặt lại mật khẩu mới.
                            </p>
                            <a href="<?php echo Uri::base(); ?>user/forgot-password" class="btn btn-primary">
                                <i class="fas fa-redo me-2"></i>Yêu cầu mã mới
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-4">
                        <small class="text-muted">
                            Nhớ mật khẩu? <a href="<?php echo Uri::base(); ?>user/login">Đăng nhập ngay</a>
                        </small>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    // Focus vào input password
    if (passwordInput) {
        passwordInput.focus();
    }
    
    // Validate password strength
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = getPasswordStrength(password);
            updatePasswordStrength(strength);
        });
    }
    
    // Validate password confirmation
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.classList.add('is-invalid');
                if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Mật khẩu xác nhận không khớp';
                    this.parentNode.appendChild(feedback);
                }
            } else {
                this.classList.remove('is-invalid');
                const feedback = this.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.remove();
                }
            }
        });
    }
    
    function getPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 6) strength++;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        return strength;
    }
    
    function updatePasswordStrength(strength) {
        // Có thể thêm UI hiển thị độ mạnh mật khẩu ở đây
        console.log('Password strength:', strength);
    }
});
</script>
