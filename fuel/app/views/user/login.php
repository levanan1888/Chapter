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

<!-- Modal thông báo tài khoản bị khóa -->
<div class="modal fade" id="accountLockedModal" tabindex="-1" aria-labelledby="accountLockedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="accountLockedModalLabel">
                    <i class="fas fa-lock me-2"></i>Tài khoản bị khóa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    <i class="fas fa-user-lock text-danger" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-danger mb-3" id="modalTitle">Tài khoản đã bị khóa</h4>
                <p class="text-muted mb-4" id="modalMessage">
                    Tài khoản của bạn đã bị khóa bởi quản trị viên. 
                    Vui lòng liên hệ với chúng tôi để được hỗ trợ mở khóa tài khoản.
                </p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Liên hệ hỗ trợ:</strong><br>
                    Email: support@comichub.com<br>
                    Hotline: 1900-xxxx
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Đóng
                </button>
                <a href="mailto:support@comichub.com" class="btn btn-primary">
                    <i class="fas fa-envelope me-2"></i>Liên hệ hỗ trợ
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">Đăng nhập</h2>
                        <p class="text-muted">Chào mừng bạn quay trở lại!</p>
                    </div>

                    <form method="POST" action="<?php echo Uri::base(); ?>user/login">
                        <?php echo Form::csrf(); ?>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-2"></i>Tên đăng nhập hoặc Email
                            </label>
                            <input type="text" class="form-control form-control-lg" id="username" name="username" 
                                   value="<?php echo isset($username) ? $username : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Mật khẩu
                            </label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Ghi nhớ đăng nhập
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                        </button>
                        
                        <div class="text-center mb-3">
                            <a href="<?php echo Uri::base(); ?>user/google_login" class="btn btn-outline-danger btn-lg w-100">
                                <i class="fab fa-google me-2"></i>Đăng nhập với Google
                            </a>
                        </div>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                <a href="<?php echo Uri::base(); ?>user/forgot-password">Quên mật khẩu?</a>
                            </small>
                        </div>
                        
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                Chưa có tài khoản? <a href="<?php echo Uri::base(); ?>user/register">Đăng ký ngay</a>
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kiểm tra nếu có thông báo tài khoản bị khóa
    <?php if (isset($account_locked) && $account_locked): ?>
        // Cập nhật nội dung modal dựa trên loại lỗi
        const errorMessage = '<?php echo isset($error_message) ? Security::htmlentities($error_message) : ''; ?>';
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        
        if (errorMessage.includes('vô hiệu hóa')) {
            modalTitle.textContent = 'Tài khoản đã bị vô hiệu hóa';
            modalMessage.textContent = 'Tài khoản của bạn đã bị vô hiệu hóa bởi quản trị viên. Vui lòng liên hệ với chúng tôi để được hỗ trợ kích hoạt lại tài khoản.';
        } else {
            modalTitle.textContent = 'Tài khoản đã bị khóa';
            modalMessage.textContent = 'Tài khoản của bạn đã bị khóa bởi quản trị viên. Vui lòng liên hệ với chúng tôi để được hỗ trợ mở khóa tài khoản.';
        }
        
        // Hiển thị modal tài khoản bị khóa
        const accountLockedModal = new bootstrap.Modal(document.getElementById('accountLockedModal'));
        accountLockedModal.show();
        
        // Ẩn alert thông thường
        const alertElement = document.querySelector('.alert-danger');
        if (alertElement) {
            alertElement.style.display = 'none';
        }
    <?php endif; ?>
    
    // Ngăn không cho submit form nếu tài khoản bị khóa
    const loginForm = document.querySelector('form[method="POST"]');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const submitButton = document.querySelector('button[type="submit"]');
    
    // Lưu trữ username bị khóa để kiểm tra
    let lockedUsername = null;
    
    <?php if (isset($account_locked) && $account_locked): ?>
        // Lưu username bị khóa
        lockedUsername = '<?php echo isset($_POST['username']) ? Security::htmlentities($_POST['username']) : ''; ?>';
        
        // Disable form khi tài khoản bị khóa hoặc vô hiệu hóa
        if (usernameInput) usernameInput.disabled = true;
        if (passwordInput) passwordInput.disabled = true;
        if (submitButton) {
            submitButton.disabled = true;
            const buttonText = errorMessage.includes('vô hiệu hóa') ? 'Tài khoản bị vô hiệu hóa' : 'Tài khoản bị khóa';
            submitButton.innerHTML = '<i class="fas fa-lock me-2"></i>' + buttonText;
        }
    <?php endif; ?>
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const username = usernameInput.value;
            const password = passwordInput.value;
            
            // Kiểm tra nếu đây là tài khoản bị khóa
            if (lockedUsername && username === lockedUsername) {
                e.preventDefault();
                // Hiển thị modal nếu chưa hiển thị
                const accountLockedModal = new bootstrap.Modal(document.getElementById('accountLockedModal'));
                accountLockedModal.show();
                return false;
            }
        });
    }
    
    // Kiểm tra khi user nhập username
    if (usernameInput) {
        usernameInput.addEventListener('input', function() {
            const currentUsername = this.value;
            if (lockedUsername && currentUsername === lockedUsername) {
                // Disable form nếu là tài khoản bị khóa hoặc vô hiệu hóa
                if (passwordInput) passwordInput.disabled = true;
                if (submitButton) {
                    submitButton.disabled = true;
                    const buttonText = errorMessage.includes('vô hiệu hóa') ? 'Tài khoản bị vô hiệu hóa' : 'Tài khoản bị khóa';
                    submitButton.innerHTML = '<i class="fas fa-lock me-2"></i>' + buttonText;
                }
            } else {
                // Enable form nếu không phải tài khoản bị khóa
                if (passwordInput) passwordInput.disabled = false;
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Đăng nhập';
                }
            }
        });
    }
});
</script>

