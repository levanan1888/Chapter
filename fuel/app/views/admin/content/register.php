<?php if (isset($error_message) && !empty($error_message)): ?>
	<div class="alert alert-danger">
		<i class="fas fa-exclamation-triangle me-2"></i>
		<?php echo $error_message; ?>
	</div>
<?php endif; ?>

<?php if (isset($success_message) && !empty($success_message)): ?>
	<div class="alert alert-success">
		<i class="fas fa-check-circle me-2"></i>
		<?php echo $success_message; ?>
	</div>
<?php endif; ?>

<form method="POST" action="<?php echo Uri::base(); ?>admin/register">
	<div class="mb-3">
		<label for="username" class="form-label">
			<i class="fas fa-user me-2"></i>Tên đăng nhập *
		</label>
		<input type="text" class="form-control" id="username" name="username" 
			   value="<?php echo isset($form_data['username']) ? $form_data['username'] : ''; ?>" required>
	</div>
	
	<div class="mb-3">
		<label for="email" class="form-label">
			<i class="fas fa-envelope me-2"></i>Email *
		</label>
		<input type="email" class="form-control" id="email" name="email" 
			   value="<?php echo isset($form_data['email']) ? $form_data['email'] : ''; ?>" required>
	</div>
	
	<div class="mb-3">
		<label for="full_name" class="form-label">
			<i class="fas fa-id-card me-2"></i>Họ và tên
		</label>
		<input type="text" class="form-control" id="full_name" name="full_name" 
			   value="<?php echo isset($form_data['full_name']) ? $form_data['full_name'] : ''; ?>">
	</div>
	
	<div class="mb-3">
		<label for="password" class="form-label">
			<i class="fas fa-lock me-2"></i>Mật khẩu *
		</label>
		<input type="password" class="form-control" id="password" name="password" required>
	</div>
	
	<div class="mb-3">
		<label for="confirm_password" class="form-label">
			<i class="fas fa-lock me-2"></i>Xác nhận mật khẩu *
		</label>
		<input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
	</div>
	
	<button type="submit" class="btn btn-primary w-100 mb-3">
		<i class="fas fa-user-plus me-2"></i>Đăng ký
	</button>
	
	<div class="text-center">
		<small class="text-muted">
			<a href="<?php echo Uri::base(); ?>admin/login">Đã có tài khoản? Đăng nhập ngay</a>
		</small>
	</div>
</form>