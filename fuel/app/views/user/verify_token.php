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
                            <i class="fas fa-mail-bulk text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="fw-bold text-primary">Nhập mã xác thực</h2>
                        <p class="text-muted">Mã xác thực đã được gửi đến email của bạn</p>
                    </div>

                    <form method="POST" action="<?php echo Uri::base(); ?>user/verify-token" id="verify-form">
                        <?php echo Form::csrf(); ?>
                        
                        <input type="hidden" name="email" value="<?php echo Security::htmlentities($email); ?>">
                        
                        <div class="mb-4">
                            <label for="email_display" class="form-label">Email đã gửi mã đến</label>
                            <input type="text" class="form-control" id="email_display" 
                                   value="<?php echo Security::htmlentities($email); ?>" readonly>
                        </div>
                        
                        <div class="mb-4">
                            <label for="token" class="form-label">
                                <i class="fas fa-key me-2"></i>Mã xác thực *
                            </label>
                            <input type="text" class="form-control form-control-lg text-center placeholder-white" id="token" name="token" 
                                   placeholder="Nhập mã xác thực" required maxlength="64" 
                                   value="<?php echo isset($form_data['token']) ? Security::htmlentities($form_data['token']) : ''; ?>"
                                   style="font-size: 1.2rem; letter-spacing: 2px;">
                            <div class="form-text text-center text-light" id="countdownHelp">
                                <i class="fas fa-info-circle me-1"></i>
                                Kiểm tra email và nhập mã xác thực bạn nhận được
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-check me-2"></i>Xác thực mã
                        </button>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                Không nhận được email? <a href="<?php echo Uri::base(); ?>user/forgot-password" id="resend-link">Gửi lại mã</a>
                            </small>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Hướng dẫn -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-question-circle me-2"></i>Mã xác thực không đến?
                    </h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Kiểm tra thư mục Spam/Junk
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Đợi vài phút, email có thể chậm
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Kiểm tra địa chỉ email đã nhập
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Thử lại sau một lúc
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Placeholder to white for dark backgrounds */
.placeholder-white::placeholder { color: #ffffff !important; opacity: 0.75; }
input.form-control::placeholder { color: #ffffff !important; opacity: 0.75; }
/* For various browsers */
.placeholder-white:-ms-input-placeholder { color: #ffffff !important; }
.placeholder-white::-ms-input-placeholder { color: #ffffff !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Focus vào input token
    const tokenInput = document.getElementById('token');
    if (tokenInput) {
        tokenInput.focus();
        
        // Auto uppercase và remove spaces
        tokenInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/\s/g, '');
        });
    }
    
    // Auto submit khi nhập đủ ký tự
    if (tokenInput) {
        tokenInput.addEventListener('keyup', function() {
            if (this.value.length >= 6) {
                // Không auto submit, để user tự submit
            }
        });
    }
    // Countdown timer for token validity (default 10 minutes unless configured)
    const help = document.getElementById('countdownHelp');
    const resendLink = document.getElementById('resend-link');
    // You can adjust this if token expiry differs
    const expirySeconds = 10 * 60; // 10 minutes
    let remaining = expirySeconds;
    function formatTime(s) {
        const m = Math.floor(s / 60);
        const ss = (s % 60).toString().padStart(2, '0');
        return m + ':' + ss;
    }
    function tick() {
        if (!help) return;
        if (remaining <= 0) {
            help.innerHTML = '<i class="fas fa-info-circle me-1"></i>Mã đã hết hạn. Vui lòng <a href="<?php echo Uri::base(); ?>user/forgot-password">gửi lại mã</a>.';
            if (resendLink) resendLink.classList.remove('disabled');
            return;
        }
        help.innerHTML = '<i class="fas fa-info-circle me-1"></i>Mã có hiệu lực trong ' + formatTime(remaining) + ' phút.';
        remaining -= 1;
        setTimeout(tick, 1000);
    }
    tick();
});
</script>
