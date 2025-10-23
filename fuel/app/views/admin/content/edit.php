<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-edit me-2"></i>Sửa admin
	</h2>
	<a href="<?php echo Uri::base(); ?>admin/users" class="btn btn-outline-secondary">
		<i class="fas fa-arrow-left me-2"></i>Quay lại
	</a>
</div>

<div class="card">
	<div class="card-body">
		<?php if (isset($error_message) && !empty($error_message)): ?>
			<div class="alert alert-danger">
				<i class="fas fa-exclamation-triangle me-2"></i>
				<?php echo $error_message; ?>
			</div>
		<?php endif; ?>

		<form method="POST" action="<?php echo Uri::base(); ?>admin/users/edit/<?php echo isset($admin) ? $admin->id : ''; ?>">
			<?php echo \Form::csrf(); ?>
			<div class="row">
				<div class="col-md-6">
					<div class="mb-3">
						<label for="username" class="form-label">Tên đăng nhập *</label>
						<input type="text" class="form-control" id="username" name="username" 
							   value="<?php echo isset($admin) ? $admin->username : ''; ?>" required>
					</div>

					<div class="mb-3">
						<label for="email" class="form-label">Email *</label>
						<input type="email" class="form-control" id="email" name="email" 
							   value="<?php echo isset($admin) ? $admin->email : ''; ?>" required>
					</div>
				</div>

				<div class="col-md-6">
					<div class="mb-3">
						<label for="full_name" class="form-label">Họ và tên</label>
						<input type="text" class="form-control" id="full_name" name="full_name" 
							   value="<?php echo isset($admin) ? $admin->full_name : ''; ?>">
					</div>

					<div class="mb-3">
						<label for="password" class="form-label">Mật khẩu mới</label>
						<input type="password" class="form-control" id="password" name="password">
						<div class="form-text">Để trống nếu không muốn thay đổi mật khẩu</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<div class="mb-3">
						<label for="user_type" class="form-label">Loại người dùng *</label>
						<select class="form-control" id="user_type" name="user_type" required>
							<option value="admin" <?php echo (isset($admin) && $admin->user_type == 'admin') ? 'selected' : ''; ?>>Admin</option>
							<option value="user" <?php echo (isset($admin) && $admin->user_type == 'user') ? 'selected' : ''; ?>>User</option>
						</select>
					</div>
				</div>
			</div>

			<div class="mb-3">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" name="is_active" value="1" 
						   id="is_active" <?php echo (isset($admin) && $admin->is_active) ? 'checked' : ''; ?>>
					<label class="form-check-label" for="is_active">
						Hoạt động
					</label>
				</div>
			</div>

			<div class="d-flex justify-content-end gap-2">
				<a href="<?php echo Uri::base(); ?>admin/users" class="btn btn-secondary">
					<i class="fas fa-times me-2"></i>Hủy
				</a>
				<button type="submit" class="btn btn-primary">
					<i class="fas fa-save me-2"></i>Cập nhật admin
				</button>
			</div>
		</form>
	</div>
</div>