<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Chào mừng trở lại!</h1>
                                </div>

                                <?php if (!empty($error_message)): ?>
                                    <div class="alert alert-danger">
                                        <?php echo htmlentities($error_message); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($success_message)): ?>
                                    <div class="alert alert-success">
                                        <?php echo htmlentities($success_message); ?>
                                    </div>
                                <?php endif; ?>

                                <form class="user" method="POST" action="<?php echo Uri::create('admin/login'); ?>">
                                    <?php echo Form::csrf(); ?>
                                    <div class="form-group">
                                        <input type="text" id="username" name="username" class="form-control form-control-user" required autocomplete="username" placeholder="Tên đăng nhập hoặc Email">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" id="password" name="password" class="form-control form-control-user" required autocomplete="current-password" placeholder="Mật khẩu">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Đăng nhập
                                    </button>
                                </form>
                                <hr>
                                <div class="text-center">
                                    <a href="<?php echo Uri::create('admin/google_login'); ?>" class="btn btn-google btn-user btn-block">
                                        <i class="fab fa-google fa-fw"></i> Đăng nhập với Google
                                    </a>
                                </div>
                                <hr>
                                <div class="text-center">
									<a class="small" href="#">Quên mật khẩu?</a>
									<?php if (!empty($show_register_link)): ?>
										<div class="mt-2">
											<a class="small" href="<?php echo Uri::create('admin/register'); ?>">Tạo tài khoản Admin đầu tiên</a>
										</div>
									<?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
