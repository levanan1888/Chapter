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

<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h4 class="mb-0">
			<i class="fas fa-book me-2"></i>Quản lý Chương
		</h4>
		<?php if (isset($story)): ?>
			<p class="text-muted mb-0">Truyện: <strong><?php echo htmlspecialchars($story->title); ?></strong></p>
		<?php endif; ?>
	</div>
	<div>
		<a href="<?php echo Uri::base(); ?>admin/stories" class="btn btn-secondary me-2">
			<i class="fas fa-arrow-left me-2"></i>Quay lại
		</a>
		<?php if (isset($story)): ?>
			<a href="<?php echo Uri::base(); ?>admin/chapters/add/<?php echo $story->id; ?>" class="btn btn-primary">
				<i class="fas fa-plus me-2"></i>Thêm chương
			</a>
		<?php endif; ?>
	</div>
</div>

<?php if (isset($chapters) && !empty($chapters)): ?>
	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th width="5%">#</th>
							<th width="15%">Chương</th>
							<th width="30%">Tiêu đề</th>
							<th width="15%">Số ảnh</th>
							<th width="15%">Lượt xem</th>
							<th width="15%">Ngày tạo</th>
							<th width="5%">Thao tác</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($chapters as $chapter): ?>
							<tr>
								<td><?php echo $chapter->id; ?></td>
								<td>
									<span class="badge bg-primary">Chapter <?php echo $chapter->chapter_number; ?></span>
								</td>
								<td>
									<strong><?php echo htmlspecialchars($chapter->title); ?></strong>
								</td>
								<td>
                                    <?php 
                                    // Use model accessor to avoid type issues
                                    $images = method_exists($chapter, 'get_images') ? $chapter->get_images() : array();
                                    $image_count = is_array($images) ? count($images) : 0;
                                    ?>
									<span class="badge bg-info"><?php echo $image_count; ?> ảnh</span>
								</td>
								<td>
									<span class="text-muted"><?php echo number_format($chapter->views); ?></span>
								</td>
								<td>
									<small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($chapter->created_at)); ?></small>
								</td>
								<td>
									<div class="btn-group btn-group-sm">
										<a href="<?php echo Uri::base(); ?>admin/chapters/edit/<?php echo $chapter->id; ?>" 
										   class="btn btn-outline-primary" title="Sửa">
											<i class="fas fa-edit"></i>
										</a>
										<a href="<?php echo Uri::base(); ?>admin/chapters/images/<?php echo $chapter->id; ?>" 
										   class="btn btn-outline-info" title="Quản lý ảnh">
											<i class="fas fa-images"></i>
										</a>
										<button type="button" class="btn btn-outline-danger" 
												onclick="deleteChapter(<?php echo $chapter->id; ?>)" title="Xóa">
											<i class="fas fa-trash"></i>
										</button>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<?php if (isset($total_pages) && $total_pages > 1): ?>
				<nav aria-label="Page navigation">
					<ul class="pagination justify-content-center">
						<?php if ($current_page > 1): ?>
							<li class="page-item">
								<a class="page-link" href="?page=<?php echo $current_page - 1; ?>">Trước</a>
							</li>
						<?php endif; ?>
						
						<?php for ($i = 1; $i <= $total_pages; $i++): ?>
							<li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
								<a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
							</li>
						<?php endfor; ?>
						
						<?php if ($current_page < $total_pages): ?>
							<li class="page-item">
								<a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Sau</a>
							</li>
						<?php endif; ?>
					</ul>
				</nav>
			<?php endif; ?>
		</div>
	</div>
<?php else: ?>
	<div class="card">
		<div class="card-body text-center py-5">
			<i class="fas fa-book-open fa-3x text-muted mb-3"></i>
			<h5 class="text-muted">Chưa có chương nào</h5>
			<p class="text-muted">Hãy thêm chương đầu tiên cho truyện này</p>
			<?php if (isset($story)): ?>
				<a href="<?php echo Uri::base(); ?>admin/chapters/add/<?php echo $story->id; ?>" class="btn btn-primary">
					<i class="fas fa-plus me-2"></i>Thêm chương đầu tiên
				</a>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>

<script>
function deleteChapter(chapterId) {
	if (confirm('Bạn có chắc chắn muốn xóa chương này? Hành động này không thể hoàn tác.')) {
		// Tạo form ẩn để submit
		const form = document.createElement('form');
		form.method = 'POST';
		form.action = '<?php echo Uri::base(); ?>admin/chapters/delete/' + chapterId;
		
		// Thêm CSRF token
		const csrfToken = document.createElement('input');
		csrfToken.type = 'hidden';
		csrfToken.name = 'fuel_csrf_token';
		csrfToken.value = '<?php echo \Security::fetch_token(); ?>';
		form.appendChild(csrfToken);
		
		document.body.appendChild(form);
		form.submit();
	}
}
</script>