<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Thêm Admin mới</h1>
    <div>
        <a href="<?php echo Uri::create('admin/manage'); ?>" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
        </a>
        <a href="<?php echo Uri::create('admin/dashboard'); ?>" class="btn btn-info btn-sm shadow-sm">
            <i class="fas fa-tachometer-alt fa-sm text-white-50"></i> Dashboard
        </a>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Form Card -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin Admin</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo htmlentities($success_message); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo htmlentities($error_message); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo Uri::create('admin/add'); ?>">
                    <?php echo Form::csrf(); ?>
                    <div class="form-group">
                        <label for="username" class="font-weight-bold">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               class="form-control" 
                               required 
                               value="<?php echo isset($form_data['username']) ? htmlentities($form_data['username']) : ''; ?>"
                               placeholder="Nhập tên đăng nhập (không được trùng)">
                    </div>

                    <div class="form-group">
                        <label for="email" class="font-weight-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control" 
                               required 
                               value="<?php echo isset($form_data['email']) ? htmlentities($form_data['email']) : ''; ?>"
                               placeholder="Nhập địa chỉ email">
                    </div>

                    <div class="form-group">
                        <label for="full_name" class="font-weight-bold">Họ và tên</label>
                        <input type="text" 
                               id="full_name" 
                               name="full_name" 
                               class="form-control"
                               value="<?php echo isset($form_data['full_name']) ? htmlentities($form_data['full_name']) : ''; ?>"
                               placeholder="Nhập họ và tên đầy đủ">
                    </div>

                    <div class="form-group">
                        <label for="password" class="font-weight-bold">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control" 
                               required
                               placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Thêm Admin
                        </button>
                        <a href="<?php echo Uri::create('admin/manage'); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hướng dẫn</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="font-weight-bold text-primary">Tên đăng nhập:</h6>
                    <p class="small text-gray-600">Chỉ được chứa chữ cái, số và dấu gạch dưới. Không được trùng với admin khác.</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="font-weight-bold text-primary">Email:</h6>
                    <p class="small text-gray-600">Phải là địa chỉ email hợp lệ và không được trùng.</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="font-weight-bold text-primary">Mật khẩu:</h6>
                    <p class="small text-gray-600">Tối thiểu 6 ký tự, nên bao gồm cả chữ hoa, chữ thường và số.</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="font-weight-bold text-primary">Họ và tên:</h6>
                    <p class="small text-gray-600">Thông tin tùy chọn để hiển thị trong hệ thống.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Kiểm tra mật khẩu mạnh
document.getElementById('password').addEventListener('input', function() {
	const password = this.value;
	const feedback = document.getElementById('password-feedback') || document.createElement('div');
	feedback.id = 'password-feedback';
	
	if (!document.getElementById('password-feedback')) {
		this.parentNode.appendChild(feedback);
	}
	
	if (password.length < 6) {
		feedback.innerHTML = '<small style="color: #e74c3c;">Mật khẩu phải có ít nhất 6 ký tự</small>';
	} else if (password.length >= 6 && password.length < 8) {
		feedback.innerHTML = '<small style="color: #f39c12;">Mật khẩu yếu, nên có ít nhất 8 ký tự</small>';
	} else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
		feedback.innerHTML = '<small style="color: #f39c12;">Nên bao gồm chữ hoa, chữ thường và số</small>';
	} else {
		feedback.innerHTML = '<small style="color: #27ae60;">Mật khẩu mạnh</small>';
	}
});

// Kiểm tra username
document.getElementById('username').addEventListener('input', function() {
	const username = this.value;
	const feedback = document.getElementById('username-feedback') || document.createElement('div');
	feedback.id = 'username-feedback';
	
	if (!document.getElementById('username-feedback')) {
		this.parentNode.appendChild(feedback);
	}
	
	if (username.length < 3) {
		feedback.innerHTML = '<small style="color: #e74c3c;">Username phải có ít nhất 3 ký tự</small>';
	} else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
		feedback.innerHTML = '<small style="color: #e74c3c;">Username chỉ được chứa chữ cái, số và dấu gạch dưới</small>';
	} else {
		feedback.innerHTML = '<small style="color: #27ae60;">Username hợp lệ</small>';
	}
});
</script>


