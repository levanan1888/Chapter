<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-comment me-2"></i>Chi tiết bình luận
	</h2>
	<a href="<?php echo Uri::base(); ?>admin/comments" class="btn btn-outline-secondary">
		<i class="fas fa-arrow-left me-2"></i>Quay lại
	</a>
</div>

<div class="row">
	<div class="col-lg-8">
		<!-- Comment Details -->
		<div class="card mb-4">
			<div class="card-header">
				<h4 class="mb-0">Bình luận #<?php echo $comment->id; ?></h4>
			</div>
			<div class="card-body">
				<div class="row mb-3">
					<div class="col-md-6">
						<strong>Người dùng:</strong>
						<p class="mb-0"><?php echo Security::htmlentities($comment->user->username ?? 'Unknown'); ?> (ID: <?php echo $comment->user_id; ?>)</p>
					</div>
					<div class="col-md-6">
						<strong>Thời gian:</strong>
						<p class="mb-0"><?php echo date('d/m/Y H:i:s', strtotime($comment->created_at)); ?></p>
					</div>
				</div>
				
				<div class="row mb-3">
					<div class="col-md-6">
						<strong>Truyện:</strong>
						<p class="mb-0">
							<a href="<?php echo Uri::base(); ?>client/story/<?php echo $comment->story->slug; ?>" 
							   class="text-decoration-none" target="_blank">
								<?php echo Security::htmlentities($comment->story->title); ?>
							</a>
						</p>
					</div>
					<div class="col-md-6">
						<strong>Chương:</strong>
						<p class="mb-0">
							<?php if ($comment->chapter): ?>
								Chương <?php echo $comment->chapter->chapter_number; ?>: 
								<?php echo Security::htmlentities($comment->chapter->title); ?>
							<?php else: ?>
								Bình luận chung về truyện
							<?php endif; ?>
						</p>
					</div>
				</div>
				
				<div class="mb-3">
					<strong>Trạng thái:</strong>
					<?php if ($comment->is_approved): ?>
						<span class="badge bg-success ms-2">Đã duyệt</span>
					<?php else: ?>
						<span class="badge bg-warning ms-2">Chờ duyệt</span>
					<?php endif; ?>
				</div>
				
				<div class="mb-3">
					<strong>Nội dung:</strong>
					<div class="mt-2 p-3 bg-light rounded">
						<?php echo nl2br(html_entity_decode(Security::htmlentities($comment->content), ENT_QUOTES, 'UTF-8')); ?>
					</div>
				</div>
				
				<?php if ($comment->parent): ?>
				<div class="mb-3">
					<strong>Trả lời cho:</strong>
					<div class="mt-2 p-3 bg-light rounded">
						<p class="mb-0">
							<a href="<?php echo Uri::base(); ?>admin/comments/view/<?php echo $comment->parent->id; ?>" 
							   class="text-decoration-none">
								Bình luận #<?php echo $comment->parent->id; ?>
							</a>
						</p>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>
		
		<!-- Replies -->
		<?php if (!empty($replies)): ?>
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0">Trả lời (<?php echo count($replies); ?>)</h4>
			</div>
			<div class="card-body">
				<?php foreach ($replies as $reply): ?>
				<div class="reply-item mb-3 p-3 border rounded">
					<div class="d-flex justify-content-between align-items-start mb-2">
						<div>
							<strong><?php echo Security::htmlentities($reply->user->username ?? 'Unknown'); ?></strong>
							<small class="text-muted ms-2">
								<?php echo date('d/m/Y H:i', strtotime($reply->created_at)); ?>
							</small>
						</div>
						<div class="btn-group btn-group-sm">
							<?php if (!$reply->is_approved): ?>
								<a href="<?php echo Uri::base(); ?>admin/comments/approve/<?php echo $reply->id; ?>" 
								   class="btn btn-outline-success" title="Duyệt">
									<i class="fas fa-check"></i>
								</a>
							<?php else: ?>
								<a href="<?php echo Uri::base(); ?>admin/comments/disapprove/<?php echo $reply->id; ?>" 
								   class="btn btn-outline-warning" title="Ẩn">
									<i class="fas fa-eye-slash"></i>
								</a>
							<?php endif; ?>
							<a href="<?php echo Uri::base(); ?>admin/comments/delete/<?php echo $reply->id; ?>" 
							   class="btn btn-outline-danger" title="Xóa"
							   onclick="return confirm('Xóa trả lời này?')">
								<i class="fas fa-trash"></i>
							</a>
						</div>
					</div>
					<div class="reply-content">
						<?php echo nl2br(Security::htmlentities($reply->content)); ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
	
	<div class="col-lg-4">
		<!-- Actions -->
		<div class="card mb-4">
			<div class="card-header">
				<h5 class="mb-0">Thao tác</h5>
			</div>
			<div class="card-body">
				<div class="d-grid gap-2">
					<a href="<?php echo Uri::base(); ?>admin/comments/reply/<?php echo $comment->id; ?>" 
					   class="btn btn-primary">
						<i class="fas fa-reply me-2"></i>Trả lời bình luận
					</a>
					
					<?php if (!$comment->is_approved): ?>
						<a href="<?php echo Uri::base(); ?>admin/comments/approve/<?php echo $comment->id; ?>" 
						   class="btn btn-success"
						   onclick="return confirm('Duyệt bình luận này?')">
							<i class="fas fa-check me-2"></i>Duyệt bình luận
						</a>
					<?php else: ?>
						<a href="<?php echo Uri::base(); ?>admin/comments/disapprove/<?php echo $comment->id; ?>" 
						   class="btn btn-warning"
						   onclick="return confirm('Ẩn bình luận này?')">
							<i class="fas fa-eye-slash me-2"></i>Ẩn bình luận
						</a>
					<?php endif; ?>
					
					<a href="<?php echo Uri::base(); ?>admin/comments/delete/<?php echo $comment->id; ?>" 
					   class="btn btn-danger"
					   onclick="return confirm('Xóa bình luận này? Hành động này không thể hoàn tác!')">
						<i class="fas fa-trash me-2"></i>Xóa bình luận
					</a>
				</div>
			</div>
		</div>
		
		<!-- Story Info -->
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0">Thông tin truyện</h5>
			</div>
			<div class="card-body">
				<p><strong>Tên truyện:</strong><br>
					<a href="<?php echo Uri::base(); ?>client/story/<?php echo $comment->story->slug; ?>" 
					   class="text-decoration-none" target="_blank">
						<?php echo Security::htmlentities($comment->story->title); ?>
					</a>
				</p>
				
				<?php if ($comment->chapter): ?>
				<p><strong>Chương:</strong><br>
					Chương <?php echo $comment->chapter->chapter_number; ?>: 
					<?php echo Security::htmlentities($comment->chapter->title); ?>
				</p>
				<?php endif; ?>
				
				<p><strong>Tác giả:</strong><br>
					<?php echo Security::htmlentities($comment->story->author_name ?? 'Unknown'); ?>
				</p>
			</div>
		</div>
	</div>
</div>
