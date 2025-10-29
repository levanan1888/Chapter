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
                            <i class="fas fa-key text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="fw-bold text-primary">Quên mật khẩu</h2>
                        <p class="text-muted">Nhập địa chỉ email để nhận mã xác thực đặt lại mật khẩu</p>
                    </div>

                    <form method="POST" action="<?php echo Uri::base(); ?>user/forgot-password" id="forgot-form">
                        <?php echo Form::csrf(); ?>
                        
                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Địa chỉ email *
                            </label>
                            <input type="email" class="form-control form-control-lg placeholder-white" id="email" name="email" 
                                   value="<?php echo isset($form_data['email']) ? Security::htmlentities($form_data['email']) : ''; ?>" 
                                   placeholder="Nhập địa chỉ email của bạn" required>
                            <div class="form-text text-light">
                                <i class="fas fa-info-circle me-1"></i>
                                Chúng tôi sẽ gửi mã xác thực đến email này
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-paper-plane me-2"></i>Gửi mã xác thực
                        </button>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                Nhớ mật khẩu? <a href="<?php echo Uri::base(); ?>user/login">Đăng nhập ngay</a>
                            </small>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Hướng dẫn -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-question-circle me-2"></i>Hướng dẫn
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-1 me-2 text-primary"></i>Nhập email</h6>
                            <p class="small text-muted">Nhập địa chỉ email đã đăng ký tài khoản</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-2 me-2 text-primary"></i>Kiểm tra email</h6>
                            <p class="small text-muted">Mở hộp thư và tìm email từ ComicHub</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-3 me-2 text-primary"></i>Nhập mã xác thực</h6>
                            <p class="small text-muted">Sao chép mã xác thực từ email</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-4 me-2 text-primary"></i>Đặt mật khẩu mới</h6>
                            <p class="small text-muted">Tạo mật khẩu mới an toàn</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Make placeholders white for dark background */
.placeholder-white::placeholder { color: #ffffff !important; opacity: 0.75; }
input.form-control::placeholder { color: #ffffff !important; opacity: 0.75; }
.placeholder-white:-ms-input-placeholder { color: #ffffff !important; }
.placeholder-white::-ms-input-placeholder { color: #ffffff !important; }
/* Helper text to white */
.form-text.text-light { color: #ffffff !important; opacity: 0.8; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Focus vào input email
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.focus();
    }
    
    // Validate email real-time
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.classList.add('is-invalid');
                if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Địa chỉ email không hợp lệ';
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
    // CSRF: làm mới token khi người dùng quay lại tab hoặc sau một thời gian dài không tương tác
    const form = document.getElementById('forgot-form');
    const tokenKey = '<?php echo \Config::get('security.csrf_token_key'); ?>';
    function refreshCsrfToken() {
        fetch('<?php echo Uri::base(); ?>user/csrf-token', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        })
        .then(r => r.ok ? r.json() : Promise.reject())
        .then(data => {
            if (data && data.success && data.data && form) {
                const hidden = form.querySelector('input[name="' + tokenKey + '"]');
                if (hidden) hidden.value = data.data.csrf_token;
            }
        })
        .catch(() => {});
    }
    // Khi tab lấy lại focus hoặc quay lại từ trang khác
    window.addEventListener('focus', refreshCsrfToken);
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) refreshCsrfToken();
    });
    // Làm mới định kỳ để tránh hết hạn (mỗi 2 phút)
    setInterval(refreshCsrfToken, 120000);
});
</script>
