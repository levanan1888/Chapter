<?php
function displayReplyRecursive($reply, $depth = 0) {
    $indent = $depth * 40;
    $font_size = max(0.875, 1.0 - ($depth * 0.025));
    
    $reply_display_name = 'Anonymous';
    if (isset($reply->user) && is_object($reply->user)) {
        if (isset($reply->user->full_name) && !empty($reply->user->full_name)) {
            $reply_display_name = $reply->user->full_name;
        } elseif (isset($reply->user->username)) {
            $reply_display_name = $reply->user->username;
        }
    }
    ?>
    <div class="reply-item mb-2 p-3 bg-light rounded" style="margin-left: <?php echo $indent; ?>px; border-left: 3px solid rgba(0, 123, 255, <?php echo max(0.2, 0.4 - $depth * 0.1); ?>);">
        <div class="d-flex justify-content-between align-items-start">
            <div class="reply-header">
                <h6 class="mb-1" style="font-size: <?php echo $font_size; ?>rem;">
                    <i class="fas fa-reply me-2"></i>
                    <?php echo html_entity_decode(htmlspecialchars($reply_display_name), ENT_QUOTES, 'UTF-8'); ?>
                    <span class="badge bg-secondary ms-2">#<?php echo $reply->id; ?></span>
                </h6>
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    <?php echo date('d/m/Y H:i', strtotime($reply->created_at)); ?>
                </small>
            </div>
            <div class="reply-actions">
                <?php if ($reply->is_approved): ?>
                    <span class="badge bg-success me-2">Đã duyệt</span>
                <?php else: ?>
                    <span class="badge bg-warning me-2">Chưa duyệt</span>
                <?php endif; ?>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary" 
                            onclick="toggleEditReplyForm(<?php echo $reply->id; ?>)" title="Sửa">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-outline-info" 
                            onclick="toggleReplyForm(<?php echo $reply->id; ?>, <?php echo $reply->parent_id; ?>)" title="Trả lời">
                        <i class="fas fa-reply"></i>
                    </button>
                    <?php if ($reply->is_approved): ?>
                        <a href="<?php echo Uri::base(); ?>admin/comments/disapprove/<?php echo $reply->id; ?>" 
                           class="btn btn-outline-warning" title="Ẩn trả lời">
                            <i class="fas fa-eye-slash"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo Uri::base(); ?>admin/comments/approve/<?php echo $reply->id; ?>" 
                           class="btn btn-outline-success" title="Duyệt trả lời">
                            <i class="fas fa-check"></i>
                        </a>
                    <?php endif; ?>
                    <form method="POST" action="<?php echo Uri::base(); ?>admin/comments/delete/<?php echo $reply->id; ?>" 
                          style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa trả lời này?')">
                        <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
                        <button type="submit" class="btn btn-outline-danger" title="Xóa">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="reply-content mt-2" style="font-size: <?php echo $font_size; ?>rem;">
            <p class="mb-0" id="reply-content-text-<?php echo $reply->id; ?>"><?php echo nl2br(html_entity_decode(htmlspecialchars($reply->content), ENT_QUOTES, 'UTF-8')); ?></p>
        </div>
        
        <!-- Edit Form for replies -->
        <div id="edit-form-reply-<?php echo $reply->id; ?>" style="display: none;" class="mt-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa trả lời #<?php echo $reply->id; ?>
                    </h6>
                    <form method="POST" action="<?php echo Uri::base(); ?>admin/comments/edit" class="edit-form">
                        <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
                        <input type="hidden" name="comment_id" value="<?php echo $reply->id; ?>">
                        
                        <div class="mb-3">
                            <label for="edit-content-reply-<?php echo $reply->id; ?>" class="form-label">Nội dung trả lời *</label>
                            <textarea class="form-control" id="edit-content-reply-<?php echo $reply->id; ?>" 
                                      name="content" rows="3" required><?php echo htmlspecialchars(html_entity_decode($reply->content, ENT_QUOTES, 'UTF-8')); ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" 
                                    onclick="toggleEditReplyForm(<?php echo $reply->id; ?>)">
                                <i class="fas fa-times me-2"></i>Hủy
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Reply Form for child comments -->
        <div id="reply-form-<?php echo $reply->id; ?>" style="display: none;" class="mt-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-reply me-2"></i>Trả lời bình luận #<?php echo $reply->id; ?>
                    </h6>
                    <form method="POST" action="<?php echo Uri::base(); ?>admin/comments/save_reply" class="reply-form">
                        <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
                        <input type="hidden" name="parent_id" value="<?php echo $reply->id; ?>">
                        
                        <div class="mb-3">
                            <label for="reply-content-<?php echo $reply->id; ?>" class="form-label">Nội dung trả lời *</label>
                            <textarea class="form-control" id="reply-content-<?php echo $reply->id; ?>" 
                                      name="content" rows="3" placeholder="Nhập nội dung trả lời..." required></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" 
                                    onclick="toggleReplyForm(<?php echo $reply->id; ?>, <?php echo $reply->parent_id; ?>)">
                                <i class="fas fa-times me-2"></i>Hủy
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Gửi trả lời
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (isset($reply->replies) && !empty($reply->replies)): ?>
        <?php foreach ($reply->replies as $nested_reply): ?>
            <?php displayReplyRecursive($nested_reply, $depth + 1); ?>
        <?php endforeach; ?>
    <?php endif; ?>
<?php
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-comments me-2"></i>Quản lý Bình luận
	</h2>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
	<div class="card-body">
		<form method="GET" action="<?php echo Uri::base(); ?>admin/comments" class="row g-3">
			<div class="col-md-4">
				<label for="search" class="form-label">Tìm kiếm</label>
				<input type="text" class="form-control" id="search" name="search" 
					   value="<?php echo htmlspecialchars(Input::get('search', '')); ?>" 
					   placeholder="Tìm theo nội dung, tên user...">
			</div>
			<div class="col-md-3">
				<label for="story_id" class="form-label">Truyện</label>
				<select class="form-select" id="story_id" name="story_id">
					<option value="">Tất cả truyện</option>
					<?php if (isset($stories) && !empty($stories)): ?>
						<?php foreach ($stories as $story): ?>
							<option value="<?php echo $story->id; ?>" 
									<?php echo Input::get('story_id') == $story->id ? 'selected' : ''; ?>>
								<?php echo htmlspecialchars($story->title); ?>
							</option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
			<div class="col-md-3">
				<label for="status" class="form-label">Trạng thái</label>
				<select class="form-select" id="status" name="status">
					<option value="">Tất cả trạng thái</option>
					<option value="approved" <?php echo Input::get('status') == 'approved' ? 'selected' : ''; ?>>Đã duyệt</option>
					<option value="pending" <?php echo Input::get('status') == 'pending' ? 'selected' : ''; ?>>Chưa duyệt</option>
				</select>
			</div>
			<div class="col-md-2">
				<label class="form-label">&nbsp;</label>
				<div class="d-flex gap-2">
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-search"></i>
					</button>
					<a href="<?php echo Uri::base(); ?>admin/comments" class="btn btn-secondary">
						<i class="fas fa-times"></i>
					</a>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- Comments Table -->
<div class="card">
	<div class="card-body">
		<!-- Success/Error Messages -->
		<?php if (Session::get_flash('success')): ?>
			<div class="alert alert-success">
				<i class="fas fa-check-circle me-2"></i>
				<?php echo Session::get_flash('success'); ?>
			</div>
		<?php endif; ?>

		<?php if (Session::get_flash('error')): ?>
			<div class="alert alert-danger">
				<i class="fas fa-exclamation-triangle me-2"></i>
				<?php echo Session::get_flash('error'); ?>
			</div>
		<?php endif; ?>

		<?php if (isset($comments) && !empty($comments)): ?>
			<div class="comments-thread">
				<?php foreach ($comments as $comment): ?>
				<!-- Parent Comment -->
				<div class="comment-item mb-3">
					<div class="card">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-start mb-2">
								<div class="comment-header">
									<h6 class="mb-1">
										<i class="fas fa-user-circle me-2"></i>
										<?php 
										// Get display name (full_name preferred)
										$display_name = 'Anonymous';
										if (isset($comment->user) && is_object($comment->user)) {
											if (isset($comment->user->full_name) && !empty($comment->user->full_name)) {
												$display_name = $comment->user->full_name;
											} elseif (isset($comment->user->username)) {
												$display_name = $comment->user->username;
											}
										} elseif (isset($comment->user) && is_string($comment->user)) {
											$display_name = $comment->user;
										}
										echo html_entity_decode(htmlspecialchars($display_name), ENT_QUOTES, 'UTF-8');
										?>
										<span class="badge bg-primary ms-2">#<?php echo $comment->id; ?></span>
									</h6>
									<small class="text-muted">
										<i class="fas fa-clock me-1"></i>
										<?php echo date('d/m/Y H:i', strtotime($comment->created_at)); ?>
										<?php if (isset($comment->story)): ?>
											| <i class="fas fa-book me-1"></i>
											<?php echo htmlspecialchars($comment->story->title ?? 'N/A'); ?>
											<?php if (isset($comment->chapter)): ?>
												- Chương <?php echo htmlspecialchars($comment->chapter->chapter_number ?? 'N/A'); ?>
											<?php endif; ?>
											| <a href="<?php echo Uri::base(); ?>client/read/<?php echo $comment->story->slug ?? 'story-' . $comment->story_id; ?>/<?php echo $comment->chapter->chapter_number ?? $comment->chapter_id; ?>" 
											   target="_blank" class="text-decoration-none">
												<i class="fas fa-external-link-alt me-1"></i>Xem chapter
											</a>
										<?php endif; ?>
									</small>
								</div>
                                <div class="comment-actions">
                                    <?php if ($comment->is_approved): ?>
                                        <span class="badge bg-success me-2">Đã duyệt</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning me-2">Chưa duyệt</span>
                                    <?php endif; ?>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary" 
                                                onclick="toggleEditForm(<?php echo $comment->id; ?>)" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="toggleReplyForm(<?php echo $comment->id; ?>)" title="Trả lời">
                                            <i class="fas fa-reply"></i>
                                        </button>
                                        <?php if ($comment->is_approved): ?>
                                            <a href="<?php echo Uri::base(); ?>admin/comments/disapprove/<?php echo $comment->id; ?>" 
                                               class="btn btn-outline-warning" title="Ẩn bình luận">
                                                <i class="fas fa-eye-slash"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo Uri::base(); ?>admin/comments/approve/<?php echo $comment->id; ?>" 
                                               class="btn btn-outline-success" title="Duyệt bình luận">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <form method="POST" action="<?php echo Uri::base(); ?>admin/comments/delete/<?php echo $comment->id; ?>" 
                                              style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?')">
                                            <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
                                            <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
							</div>
							<div class="comment-content">
								<p class="mb-0" id="comment-content-text-<?php echo $comment->id; ?>"><?php echo nl2br(html_entity_decode(htmlspecialchars($comment->content), ENT_QUOTES, 'UTF-8')); ?></p>
							</div>
							
							<!-- Edit Form for main comment -->
							<div id="edit-form-<?php echo $comment->id; ?>" style="display: none;" class="mt-3">
								<div class="card bg-light">
									<div class="card-body">
										<h6 class="card-title">
											<i class="fas fa-edit me-2"></i>Chỉnh sửa bình luận #<?php echo $comment->id; ?>
										</h6>
										<form method="POST" action="<?php echo Uri::base(); ?>admin/comments/edit" class="edit-form">
											<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
											<input type="hidden" name="comment_id" value="<?php echo $comment->id; ?>">
											
											<div class="mb-3">
												<label for="edit-content-<?php echo $comment->id; ?>" class="form-label">Nội dung bình luận *</label>
                                            <textarea class="form-control" id="edit-content-<?php echo $comment->id; ?>" 
                                                      name="content" rows="3" required><?php echo htmlspecialchars(html_entity_decode($comment->content, ENT_QUOTES, 'UTF-8')); ?></textarea>
											</div>
											
											<div class="d-flex justify-content-end gap-2">
												<button type="button" class="btn btn-secondary" 
														onclick="toggleEditForm(<?php echo $comment->id; ?>)">
													<i class="fas fa-times me-2"></i>Hủy
												</button>
												<button type="submit" class="btn btn-primary">
													<i class="fas fa-save me-2"></i>Lưu thay đổi
												</button>
											</div>
										</form>
									</div>
								</div>
							</div>
							
							<!-- Replies Section -->
							<?php if (isset($comment->replies) && !empty($comment->replies)): ?>
								<div class="replies-section mt-3">
									<button class="btn btn-sm btn-outline-secondary mb-2" 
											onclick="toggleReplies(<?php echo $comment->id; ?>)" 
											id="toggle-replies-<?php echo $comment->id; ?>">
										<i class="fas fa-chevron-down me-1"></i>
										Xem <?php echo count($comment->replies); ?> trả lời
									</button>
									<div class="replies-list" id="replies-<?php echo $comment->id; ?>" style="display: none;">
										<?php foreach ($comment->replies as $reply): ?>
											<?php displayReplyRecursive($reply, 0); ?>
										<?php endforeach; ?>
								</div>
									</div>
								</div>
							<?php endif; ?>
							
							<!-- Reply Form -->
							<div id="reply-form-<?php echo $comment->id; ?>" style="display: none;" class="mt-3">
								<div class="card bg-light">
									<div class="card-body">
										<h6 class="card-title">
											<i class="fas fa-reply me-2"></i>Trả lời bình luận #<?php echo $comment->id; ?>
										</h6>
										<form method="POST" action="<?php echo Uri::base(); ?>admin/comments/save_reply" class="reply-form">
											<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
											<input type="hidden" name="parent_id" value="<?php echo $comment->id; ?>">
											
											<div class="mb-3">
												<label for="reply-content-<?php echo $comment->id; ?>" class="form-label">Nội dung trả lời *</label>
												<textarea class="form-control" id="reply-content-<?php echo $comment->id; ?>" 
														  name="content" rows="3" placeholder="Nhập nội dung trả lời..." required></textarea>
											</div>
											
											<div class="d-flex justify-content-end gap-2">
												<button type="button" class="btn btn-secondary" 
														onclick="toggleReplyForm(<?php echo $comment->id; ?>)">
													<i class="fas fa-times me-2"></i>Hủy
												</button>
												<button type="submit" class="btn btn-primary">
													<i class="fas fa-paper-plane me-2"></i>Gửi trả lời
												</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>

			<!-- Pagination -->
			<?php if (isset($total_pages) && $total_pages > 1): ?>
				<div class="d-flex justify-content-between align-items-center mt-4">
					<div class="text-muted">
						Hiển thị <?php echo (($current_page - 1) * 20) + 1; ?> - <?php echo min($current_page * 20, $total_count); ?> 
						của <?php echo $total_count; ?> bình luận
					</div>
					<nav aria-label="Page navigation">
						<ul class="pagination mb-0">
							<?php if (isset($current_page) && $current_page > 1): ?>
								<li class="page-item">
									<a class="page-link" href="<?php echo Uri::base(); ?>admin/comments?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>">
										<i class="fas fa-chevron-left"></i>
									</a>
								</li>
							<?php endif; ?>

							<?php 
							$start_page = max(1, $current_page - 2);
							$end_page = min($total_pages, $current_page + 2);
							?>

							<?php if ($start_page > 1): ?>
								<li class="page-item">
									<a class="page-link" href="<?php echo Uri::base(); ?>admin/comments?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a>
								</li>
								<?php if ($start_page > 2): ?>
									<li class="page-item disabled"><span class="page-link">...</span></li>
								<?php endif; ?>
							<?php endif; ?>

							<?php for ($i = $start_page; $i <= $end_page; $i++): ?>
								<?php if ($i == $current_page): ?>
									<li class="page-item active">
										<span class="page-link"><?php echo $i; ?></span>
									</li>
								<?php else: ?>
									<li class="page-item">
										<a class="page-link" href="<?php echo Uri::base(); ?>admin/comments?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
									</li>
								<?php endif; ?>
							<?php endfor; ?>

							<?php if ($end_page < $total_pages): ?>
								<?php if ($end_page < $total_pages - 1): ?>
									<li class="page-item disabled"><span class="page-link">...</span></li>
								<?php endif; ?>
								<li class="page-item">
									<a class="page-link" href="<?php echo Uri::base(); ?>admin/comments?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>"><?php echo $total_pages; ?></a>
								</li>
							<?php endif; ?>

							<?php if (isset($current_page) && $current_page < $total_pages): ?>
								<li class="page-item">
									<a class="page-link" href="<?php echo Uri::base(); ?>admin/comments?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>">
										<i class="fas fa-chevron-right"></i>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</nav>
				</div>
			<?php endif; ?>

		<?php else: ?>
			<div class="text-center py-5">
				<i class="fas fa-comments fa-3x text-muted mb-3"></i>
				<h5 class="text-muted">Chưa có bình luận nào</h5>
			</div>
		<?php endif; ?>
	</div>
</div>

<script>
function toggleEditForm(commentId) {
    const formDiv = document.getElementById('edit-form-' + commentId);
    if (formDiv.style.display === 'none') {
        // Hide all other edit and reply forms first
        document.querySelectorAll('[id^="edit-form-"]').forEach(div => {
            div.style.display = 'none';
        });
        document.querySelectorAll('[id^="reply-form-"]').forEach(div => {
            div.style.display = 'none';
        });
        // Show this form
        formDiv.style.display = 'block';
        // Focus on textarea
        setTimeout(() => {
            const textarea = document.getElementById('edit-content-' + commentId);
            if (textarea) {
                textarea.focus();
            }
        }, 100);
    } else {
        formDiv.style.display = 'none';
    }
}

function toggleEditReplyForm(replyId) {
    const formDiv = document.getElementById('edit-form-reply-' + replyId);
    if (formDiv.style.display === 'none') {
        // Hide all other edit and reply forms first
        document.querySelectorAll('[id^="edit-form-"]').forEach(div => {
            div.style.display = 'none';
        });
        document.querySelectorAll('[id^="reply-form-"]').forEach(div => {
            div.style.display = 'none';
        });
        // Show this form
        formDiv.style.display = 'block';
        // Focus on textarea
        setTimeout(() => {
            const textarea = document.getElementById('edit-content-reply-' + replyId);
            if (textarea) {
                textarea.focus();
            }
        }, 100);
    } else {
        formDiv.style.display = 'none';
    }
}

function toggleReplyForm(commentId, parentId) {
    const formDiv = document.getElementById('reply-form-' + commentId);
    if (formDiv.style.display === 'none') {
        // Hide all other edit and reply forms first
        document.querySelectorAll('[id^="edit-form-"]').forEach(div => {
            div.style.display = 'none';
        });
        document.querySelectorAll('[id^="reply-form-"]').forEach(div => {
            div.style.display = 'none';
        });
        // Show this form
        formDiv.style.display = 'block';
        // Focus on textarea
        setTimeout(() => {
            const textarea = document.getElementById('reply-content-' + commentId);
            if (textarea) {
                textarea.focus();
            }
        }, 100);
    } else {
        formDiv.style.display = 'none';
    }
}

function toggleReplies(commentId) {
    const repliesDiv = document.getElementById('replies-' + commentId);
    const toggleBtn = document.getElementById('toggle-replies-' + commentId);
    const icon = toggleBtn.querySelector('i');
    
    if (repliesDiv.style.display === 'none') {
        repliesDiv.style.display = 'block';
        icon.className = 'fas fa-chevron-up me-1';
        toggleBtn.innerHTML = '<i class="fas fa-chevron-up me-1"></i>Ẩn trả lời';
    } else {
        repliesDiv.style.display = 'none';
        icon.className = 'fas fa-chevron-down me-1';
        toggleBtn.innerHTML = '<i class="fas fa-chevron-down me-1"></i>Xem ' + repliesDiv.querySelectorAll('.reply-item').length + ' trả lời';
    }
}

// Handle form submission for replies
document.addEventListener('DOMContentLoaded', function() {
    const replyForms = document.querySelectorAll('.reply-form');
    replyForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    // Reload page to show new reply
                    window.location.reload();
                } else {
                    throw new Error('Network response was not ok');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi gửi trả lời');
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    });
    
    // Handle form submission for edits
    const editForms = document.querySelectorAll('.edit-form');
    editForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang lưu...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    // Reload page to show updated comment
                    window.location.reload();
                } else {
                    throw new Error('Network response was not ok');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi chỉnh sửa bình luận');
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    });
});
</script>
