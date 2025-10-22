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

<form method="POST" action="<?php echo Uri::base(); ?>admin/login">
    <?php echo Form::csrf(); ?>
	<div class="mb-3">
		<label for="username" class="form-label">
			<i class="fas fa-user me-2"></i>Tên đăng nhập
		</label>
		<input type="text" class="form-control" id="username" name="username" 
			   value="<?php echo isset($username) ? $username : ''; ?>" required>
	</div>
	
	<div class="mb-3">
		<label for="password" class="form-label">
			<i class="fas fa-lock me-2"></i>Mật khẩu
		</label>
		<input type="password" class="form-control" id="password" name="password" required>
	</div>
	
	<div class="mb-3 form-check">
		<input type="checkbox" class="form-check-input" id="remember" name="remember">
		<label class="form-check-label" for="remember">
			Ghi nhớ đăng nhập
		</label>
	</div>
	
	<button type="submit" class="btn btn-primary w-100 mb-3">
		<i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
	</button>
	
	<div class="text-center">
		<a href="<?php echo Uri::base(); ?>admin/google_login" class="btn btn-outline-danger">
			<i class="fab fa-google me-2"></i>Đăng nhập với Google
		</a>
	</div>
	
	<div class="text-center mt-3">
		<small class="text-muted">
			<a href="<?php echo Uri::base(); ?>admin/register">Chưa có tài khoản? Đăng ký ngay</a>
		</small>
	</div>
</form>
