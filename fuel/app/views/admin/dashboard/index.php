<div class="row">
	<!-- Statistics Cards -->
	<div class="col-lg-3 col-md-6 mb-4">
		<div class="card bg-primary text-white">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="card-title"><?php echo isset($total_stories) ? $total_stories : 0; ?></h4>
						<p class="card-text">Tổng truyện</p>
					</div>
					<div class="align-self-center">
						<i class="fas fa-book fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-lg-3 col-md-6 mb-4">
		<div class="card bg-success text-white">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="card-title"><?php echo isset($total_chapters) ? $total_chapters : 0; ?></h4>
						<p class="card-text">Tổng chương</p>
					</div>
					<div class="align-self-center">
						<i class="fas fa-file-alt fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-lg-3 col-md-6 mb-4">
		<div class="card bg-info text-white">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="card-title"><?php echo isset($total_categories) ? $total_categories : 0; ?></h4>
						<p class="card-text">Danh mục</p>
					</div>
					<div class="align-self-center">
						<i class="fas fa-tags fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-lg-3 col-md-6 mb-4">
		<div class="card bg-warning text-white">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="card-title"><?php echo isset($total_authors) ? $total_authors : 0; ?></h4>
						<p class="card-text">Tác giả</p>
					</div>
					<div class="align-self-center">
						<i class="fas fa-user-edit fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<!-- Latest Stories -->
	<div class="col-lg-8 mb-4">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h5 class="mb-0">
					<i class="fas fa-clock me-2"></i>Truyện mới nhất
				</h5>
				<a href="<?php echo Uri::base(); ?>admin/stories" class="btn btn-sm btn-outline-primary">
					Xem tất cả
				</a>
			</div>
			<div class="card-body">
				<?php if (isset($latest_stories) && !empty($latest_stories)): ?>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Tên truyện</th>
									<th>Tác giả</th>
									<th>Trạng thái</th>
									<th>Lượt xem</th>
									<th>Ngày tạo</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($latest_stories as $story): ?>
								<tr>
									<td>
										<a href="<?php echo Uri::base(); ?>admin/stories/edit/<?php echo $story->id; ?>" 
										   class="text-decoration-none">
											<?php echo $story->title; ?>
										</a>
									</td>
									<td><?php echo $story->author_name ?? 'Unknown'; ?></td>
									<td>
										<span class="badge bg-<?php 
											switch($story->status) {
												case 'ongoing': echo 'success'; break;
												case 'completed': echo 'info'; break;
												case 'paused': echo 'warning'; break;
												default: echo 'secondary';
											}
										?>">
											<?php 
											switch($story->status) {
												case 'ongoing': echo 'Đang cập nhật'; break;
												case 'completed': echo 'Hoàn thành'; break;
												case 'paused': echo 'Tạm dừng'; break;
												default: echo $story->status;
											}
											?>
										</span>
									</td>
									<td><?php echo number_format($story->views); ?></td>
									<td><?php echo date('d/m/Y', strtotime($story->created_at)); ?></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div class="text-center text-muted py-4">
						<i class="fas fa-book fa-3x mb-3"></i>
						<p>Chưa có truyện nào</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	
	<!-- Quick Actions -->
	<div class="col-lg-4 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0">
					<i class="fas fa-bolt me-2"></i>Thao tác nhanh
				</h5>
			</div>
			<div class="card-body">
				<div class="d-grid gap-2">
					<a href="<?php echo Uri::base(); ?>admin/stories/add" class="btn btn-primary">
						<i class="fas fa-plus me-2"></i>Thêm truyện mới
					</a>
					<a href="<?php echo Uri::base(); ?>admin/categories/add" class="btn btn-success">
						<i class="fas fa-tag me-2"></i>Thêm danh mục
					</a>
					<a href="<?php echo Uri::base(); ?>admin/authors/add" class="btn btn-info">
						<i class="fas fa-user-plus me-2"></i>Thêm tác giả
					</a>
					<a href="<?php echo Uri::base(); ?>admin/users/add" class="btn btn-warning">
						<i class="fas fa-user-cog me-2"></i>Thêm admin
					</a>
				</div>
			</div>
		</div>
		
		<!-- System Info -->

	</div>
</div>
