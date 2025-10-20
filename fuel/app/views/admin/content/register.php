<div class="container">

	<!-- Outer Row -->
	<div class="row justify-content-center">

		<div class="col-xl-10 col-lg-12 col-md-9">

			<div class="card o-hidden border-0 shadow-lg my-5">
				<div class="card-body p-0">
					<!-- Nested Row within Card Body -->
					<div class="row">
						<div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
						<div class="col-lg-7">
							<div class="p-5">
								<div class="text-center">
									<h1 class="h4 text-gray-900 mb-4">Tạo tài khoản Admin đầu tiên</h1>
								</div>

								<?php if (!empty($error_message)): ?>
									<div class="alert alert-danger alert-dismissible fade show" role="alert">
										<i class="fas fa-exclamation-triangle"></i> <?php echo htmlentities($error_message); ?>
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
								<?php endif; ?>

								<form class="user" method="POST" action="<?php echo Uri::create('admin/register'); ?>">
									<?php echo Form::csrf(); ?>
									<div class="form-group row">
										<div class="col-sm-6 mb-3 mb-sm-0">
											<input type="text" class="form-control form-control-user" id="username" name="username" placeholder="Tên đăng nhập" required value="<?php echo isset($form_data['username']) ? htmlentities($form_data['username']) : ''; ?>">
										</div>
										<div class="col-sm-6">
											<input type="text" class="form-control form-control-user" id="full_name" name="full_name" placeholder="Họ và tên" value="<?php echo isset($form_data['full_name']) ? htmlentities($form_data['full_name']) : ''; ?>">
										</div>
									</div>
									<div class="form-group">
										<input type="email" class="form-control form-control-user" id="email" name="email" placeholder="Địa chỉ Email" required value="<?php echo isset($form_data['email']) ? htmlentities($form_data['email']) : ''; ?>">
									</div>
									<div class="form-group row">
										<div class="col-sm-6 mb-3 mb-sm-0">
											<input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Mật khẩu" required>
										</div>
										<div class="col-sm-6">
											<input type="password" class="form-control form-control-user" id="password_confirm" placeholder="Nhập lại mật khẩu" oninput="this.setCustomValidity(this.value !== document.getElementById('password').value ? 'Mật khẩu không khớp' : '')">
										</div>
									</div>
									<button type="submit" class="btn btn-primary btn-user btn-block">
										Đăng ký
									</button>
								</form>
								<hr>
								<div class="text-center">
									<a class="small" href="<?php echo Uri::create('admin/login'); ?>">Đã có tài khoản? Đăng nhập!</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>

	</div>

</div>

