<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-users me-2"></i>Quản lý Admin Users
	</h2>
	<a href="<?php echo Uri::base(); ?>admin/users/add" class="btn btn-primary">
		<i class="fas fa-plus me-2"></i>Thêm admin mới
	</a>
</div>

<!-- Users Table -->
<div class="card">
	<div class="card-body">
		<?php if (isset($users) && !empty($users)): ?>
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Tên đăng nhập</th>
							<th>Email</th>
							<th>Họ tên</th>
							<th>Trạng thái</th>
							<th>Đăng nhập cuối</th>
							<th>Ngày tạo</th>
							<th>Thao tác</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($users as $user): ?>
						<tr>
							<td>
								<div class="d-flex align-items-center">
									<div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
										<i class="fas fa-user"></i>
									</div>
									<div>
										<h6 class="mb-0"><?php echo $user->username; ?></h6>
										<?php if ($user->google_id): ?>
											<small class="text-success">
												<i class="fab fa-google me-1"></i>Google
											</small>
										<?php endif; ?>
									</div>
								</div>
							</td>
							<td><?php echo $user->email; ?></td>
							<td><?php echo $user->full_name ?: 'Chưa có'; ?></td>
							<td>
								<span class="badge bg-<?php echo $user->is_active ? 'success' : 'danger'; ?>">
									<?php echo $user->is_active ? 'Hoạt động' : 'Không hoạt động'; ?>
								</span>
							</td>
							<td>
								<?php if ($user->last_login): ?>
									<small><?php echo date('d/m/Y H:i', strtotime($user->last_login)); ?></small>
								<?php else: ?>
									<span class="text-muted">Chưa đăng nhập</span>
								<?php endif; ?>
							</td>
							<td><?php echo date('d/m/Y', strtotime($user->created_at)); ?></td>
							<td>
								<div class="btn-group" role="group">
									<a href="<?php echo Uri::base(); ?>admin/users/edit/<?php echo $user->id; ?>" 
									   class="btn btn-sm btn-outline-primary" title="Sửa">
										<i class="fas fa-edit"></i>
									</a>
									<a href="<?php echo Uri::base(); ?>admin/users/delete/<?php echo $user->id; ?>" 
									   class="btn btn-sm btn-outline-danger" title="Xóa" 
									   onclick="return confirm('Bạn có chắc chắn muốn xóa admin này?')">
										<i class="fas fa-trash"></i>
									</a>
								</div>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php else: ?>
			<div class="text-center py-5">
				<i class="fas fa-users fa-3x text-muted mb-3"></i>
				<h5 class="text-muted">Chưa có admin nào</h5>
				<p class="text-muted">Hãy thêm admin đầu tiên của bạn</p>
				<a href="<?php echo Uri::base(); ?>admin/users/add" class="btn btn-primary">
					<i class="fas fa-plus me-2"></i>Thêm admin mới
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>