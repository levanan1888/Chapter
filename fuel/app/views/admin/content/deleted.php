<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-trash me-2"></i>Admin đã xóa
	</h2>
	<a href="<?php echo Uri::base(); ?>admin/users" class="btn btn-outline-secondary">
		<i class="fas fa-arrow-left me-2"></i>Quay lại
	</a>
</div>

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
							<th>Ngày xóa</th>
							<th>Thao tác</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($users as $user): ?>
						<tr>
							<td>
								<div class="d-flex align-items-center">
									<div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
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
							<td><?php echo date('d/m/Y H:i', strtotime($user->deleted_at)); ?></td>
							<td>
								<div class="btn-group" role="group">
									<a href="<?php echo Uri::base(); ?>admin/users/restore/<?php echo $user->id; ?>" 
									   class="btn btn-sm btn-outline-success" title="Khôi phục" 
									   onclick="return confirm('Bạn có chắc chắn muốn khôi phục admin này?')">
										<i class="fas fa-undo"></i>
									</a>
									<a href="<?php echo Uri::base(); ?>admin/users/delete-permanent/<?php echo $user->id; ?>" 
									   class="btn btn-sm btn-outline-danger" title="Xóa vĩnh viễn" 
									   onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn admin này? Hành động này không thể hoàn tác!')">
										<i class="fas fa-trash-alt"></i>
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
				<i class="fas fa-trash fa-3x text-muted mb-3"></i>
				<h5 class="text-muted">Không có admin nào bị xóa</h5>
				<p class="text-muted">Tất cả admin đều đang hoạt động</p>
			</div>
		<?php endif; ?>
	</div>
</div>