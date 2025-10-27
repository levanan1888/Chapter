<div class="container-fluid">
	<!-- Navigation Header -->
	<div class="row py-4 mb-4" id="navigation-header" style="background: rgba(30, 41, 59, 0.95); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(51, 65, 85, 0.5); border-radius: 0 0 20px 20px; z-index: 1000; margin: 0; padding: 1rem 0; transition: all 0.3s ease;">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center text-white flex-wrap gap-3">
				<!-- Back to Story -->
				<div>
					<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>" 
					   class="btn btn-outline-light" 
					   style="border-color: rgba(255, 255, 255, 0.3) !important; color: #ffffff !important; background: rgba(255, 255, 255, 0.1) !important;">
						<i class="fas fa-arrow-left me-2"></i>Về trang truyện
					</a>
				</div>
				
				<!-- Chapter Info -->
				<div class="text-center flex-grow-1">
					<h4 class="mb-1 text-white fw-bold"><?php echo Security::htmlentities($story->title); ?></h4>
					<h5 class="mb-0" style="color: #94a3b8; font-size: 0.9rem;">Chương <?php echo $chapter->chapter_number; ?>: <?php echo Security::htmlentities($chapter->title); ?></h5>
				</div>
				
				<!-- Chapter Selector -->
				<div class="dropdown">
					<button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
							style="border-color: rgba(255, 255, 255, 0.3) !important; color: #ffffff !important; background: rgba(255, 255, 255, 0.1) !important;">
						<i class="fas fa-list me-2"></i>Danh sách chương
					</button>
					<div class="dropdown-menu dropdown-menu-end" style="max-height: 500px; overflow-y: auto; min-width: 280px; background: rgba(30, 41, 59, 0.98); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(51, 65, 85, 0.5);">
						<?php if (isset($all_chapters) && !empty($all_chapters)): ?>
							<?php foreach ($all_chapters as $ch): ?>
								<a class="dropdown-item <?php echo ($ch->chapter_number == $chapter->chapter_number) ? 'active' : ''; ?>" 
								   href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $ch->chapter_number; ?>"
								   style="color: #e2e8f0; padding: 0.75rem 1.25rem; border-radius: 8px; margin: 0.25rem;">
									<div class="d-flex justify-content-between align-items-center">
										<span class="fw-semibold">Chương <?php echo $ch->chapter_number; ?></span>
										<small class="text-muted" style="color: #64748b;"><?php echo Security::htmlentities($ch->title); ?></small>
									</div>
								</a>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Chapter Navigation Buttons -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="d-flex justify-content-between">
				<!-- Previous Chapter -->
				<?php if (isset($previous_chapter) && $previous_chapter): ?>
					<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $previous_chapter->chapter_number; ?>" 
					   class="btn btn-primary">
						<i class="fas fa-chevron-left me-2"></i>
						Chương <?php echo $previous_chapter->chapter_number; ?>
					</a>
				<?php else: ?>
					<div></div>
				<?php endif; ?>
				
				<!-- Next Chapter -->
				<?php if (isset($next_chapter) && $next_chapter): ?>
					<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $next_chapter->chapter_number; ?>" 
					   class="btn btn-primary">
						Chương <?php echo $next_chapter->chapter_number; ?>
						<i class="fas fa-chevron-right ms-2"></i>
					</a>
				<?php else: ?>
					<div></div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- Chapter Content -->
	<div class="row justify-content-center" id="chapter-content" style="margin-top: 0;">
		<div class="col-lg-6 col-md-8 col-sm-10">
			<div class="reader-container">
				<?php 
				$images = $chapter->get_images();
				if (!empty($images)): 
				?>
					<?php foreach ($images as $index => $image): ?>
						<div class="chapter-image-container mb-3">
							<img src="<?php echo Uri::base() . $image; ?>" 
								 class="img-fluid chapter-image rounded shadow" 
								 alt="<?php echo Security::htmlentities($story->title); ?> - Trang <?php echo $index + 1; ?>"
								 loading="lazy">
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="text-center py-5">
						<i class="fas fa-image fa-3x text-muted mb-3"></i>
						<h4 class="text-muted">Chương này chưa có nội dung</h4>
						<p class="text-muted">Vui lòng quay lại sau.</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- Bottom Navigation -->
	<div class="row mt-4 mb-4">
		<div class="col-12">
			<div class="d-flex justify-content-between">
				<!-- Previous Chapter -->
				<?php if (isset($previous_chapter) && $previous_chapter): ?>
					<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $previous_chapter->chapter_number; ?>" 
					   class="btn btn-outline-primary btn-lg">
						<i class="fas fa-chevron-left me-2"></i>
						Chương <?php echo $previous_chapter->chapter_number; ?>
					</a>
				<?php else: ?>
					<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>" 
					   class="btn btn-outline-secondary btn-lg">
						<i class="fas fa-arrow-left me-2"></i>
						Về trang truyện
					</a>
				<?php endif; ?>
				
				<!-- Back to Story -->
				<a href="<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>" 
				   class="btn btn-outline-info btn-lg">
					<i class="fas fa-list me-2"></i>
					Danh sách chương
				</a>
				
				<!-- Next Chapter -->
				<?php if (isset($next_chapter) && $next_chapter): ?>
					<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $next_chapter->chapter_number; ?>" 
					   class="btn btn-primary btn-lg">
						Chương <?php echo $next_chapter->chapter_number; ?>
						<i class="fas fa-chevron-right ms-2"></i>
					</a>
				<?php else: ?>
					<div></div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- Floating Navigation (for mobile) -->
	<div class="navigation-buttons d-md-none">
		<?php if (isset($previous_chapter) && $previous_chapter): ?>
			<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $previous_chapter->chapter_number; ?>" 
			   class="btn btn-primary btn-floating">
				<i class="fas fa-chevron-left"></i>
			</a>
		<?php endif; ?>
		
		<?php if (isset($next_chapter) && $next_chapter): ?>
			<a href="<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $next_chapter->chapter_number; ?>" 
			   class="btn btn-primary btn-floating">
				<i class="fas fa-chevron-right"></i>
			</a>
		<?php endif; ?>
	</div>
</div>

<!-- Comments Section -->
<div class="row justify-content-center mt-5">
	<div class="col-lg-8 col-md-10">
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0">
					<i class="fas fa-comments me-2"></i>Bình luận
					<span class="badge bg-primary ms-2" id="commentCount">0</span>
				</h4>
			</div>
			<div class="card-body">
				<!-- Comment Form -->
				<?php if (Session::get('user_id')): ?>
				<form id="commentForm" class="mb-4">
					<div class="mb-3">
						<textarea class="form-control" id="commentContent" name="content" rows="3" 
								  placeholder="Viết bình luận của bạn..." required
								  style="background: rgba(30, 41, 59, 0.8); border: 1px solid rgba(51, 65, 85, 0.5); color: #fff; border-radius: 8px;"></textarea>
					</div>
					<div class="d-flex justify-content-between align-items-center">
						<small style="color: #94a3b8;">Bình luận sẽ được hiển thị sau khi đăng</small>
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-paper-plane me-2"></i>Đăng bình luận
						</button>
					</div>
				</form>
				<?php else: ?>
				<div class="alert alert-info">
					<i class="fas fa-info-circle me-2"></i>
					<a href="<?php echo Uri::base(); ?>user/login" class="text-decoration-none">Đăng nhập</a> để bình luận
				</div>
				<?php endif; ?>
				
				<!-- Comments List -->
				<div id="commentsList">
					<div class="text-center py-4">
						<i class="fas fa-spinner fa-spin fa-2x" style="color: #94a3b8;"></i>
						<p class="mt-2" style="color: #94a3b8;">Đang tải bình luận...</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Reading Controls -->
<div class="reading-controls" id="readingControls" style="display: none;">
	<button onclick="scrollToTop()" title="Về đầu trang">
		<i class="fas fa-chevron-up"></i>
	</button>
</div>

<!-- Keyboard Navigation Script -->
<script>
function scrollToTop() {
	window.scrollTo({
		top: 0,
		behavior: 'smooth'
	});
}

document.addEventListener('DOMContentLoaded', function() {
	// Show reading controls
	const controls = document.getElementById('readingControls');
	if (controls) {
		setTimeout(() => {
			controls.style.display = 'flex';
			controls.style.animation = 'fadeInUp 0.5s ease-out';
		}, 1000);
	}

	// Keyboard shortcuts toggle
	let controlsVisible = true;
	document.addEventListener('keydown', function(e) {
		if (e.key === 'h' || e.key === 'H') {
			controlsVisible = !controlsVisible;
			if (controls) {
				controls.style.display = controlsVisible ? 'flex' : 'none';
			}
		}
	});
	
	// Comments functionality
	const storyId = <?php echo $story->id; ?>;
	const chapterId = <?php echo $chapter->id; ?>;
	
	loadComments();
	
	// Comment form submission
	const commentForm = document.getElementById('commentForm');
	if (commentForm) {
		commentForm.addEventListener('submit', function(e) {
			e.preventDefault();
			submitComment();
		});
	}
	
	// Global functions for comment functionality
	window.replyToComment = function(commentId) {
		// Check if user is logged in
		<?php if (!Session::get('user_id')): ?>
		// Show login required message
		showLoginRequired();
		return;
		<?php endif; ?>
		
		// Find existing reply form for this comment
		let replyForm = document.getElementById('reply-form-' + commentId);
		
		if (replyForm) {
			// Toggle existing form
			replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
			if (replyForm.style.display === 'block') {
				replyForm.querySelector('textarea').focus();
			}
			return;
		}
		
		// Create new reply form
		const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
		if (!commentElement) {
			console.error('Comment element not found for ID:', commentId);
			return;
		}
		
		// Check if this is a reply (has class "reply-item")
		const isReply = commentElement.classList.contains('reply-item');
		const marginLeft = isReply ? '20px' : '60px';
		
		const replyFormHtml = `
			<div id="reply-form-${commentId}" class="reply-form mt-3 p-3" style="background: rgba(30, 41, 59, 0.4); border-radius: 8px; margin-left: ${marginLeft};">
				<h6 class="text-white mb-3">
					<i class="fas fa-reply me-2"></i>Trả lời bình luận
				</h6>
				<form onsubmit="submitReply(event, ${commentId})">
					<div class="mb-3">
						<textarea class="form-control" name="content" rows="3" 
								  placeholder="Nhập nội dung trả lời..." required 
								  style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(51, 65, 85, 0.3); color: white;"></textarea>
					</div>
					<div class="d-flex gap-2">
						<button type="button" class="btn btn-secondary btn-sm" onclick="closeReplyForm(${commentId})">
							<i class="fas fa-times me-1"></i>Hủy
						</button>
						<button type="submit" class="btn btn-primary btn-sm">
							<i class="fas fa-paper-plane me-1"></i>Gửi trả lời
						</button>
					</div>
				</form>
			</div>
		`;
		
		commentElement.insertAdjacentHTML('beforeend', replyFormHtml);
		
		// Focus on textarea
		setTimeout(() => {
			document.querySelector(`#reply-form-${commentId} textarea`).focus();
		}, 100);
	};
	
	window.closeReplyForm = function(commentId) {
		const replyForm = document.getElementById('reply-form-' + commentId);
		if (replyForm) {
			replyForm.remove();
		}
	};
	
	window.submitReply = async function(event, commentId) {
		event.preventDefault();
		
		// Check if user is logged in
		<?php if (!Session::get('user_id')): ?>
		showLoginRequired();
		return;
		<?php endif; ?>
		
		const form = event.target;
		const content = form.querySelector('textarea').value.trim();
		
		if (!content) {
			alert('Vui lòng nhập nội dung trả lời');
			return;
		}
		
		const submitBtn = form.querySelector('button[type="submit"]');
		const originalText = submitBtn.innerHTML;
		
		// Disable button and show loading
		submitBtn.disabled = true;
		submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang gửi...';
		
		try {
			// Fetch fresh CSRF token
			const tokenResponse = await fetch('<?php echo Uri::base(); ?>comment/get_token');
			const tokenData = await tokenResponse.json();
			
			if (!tokenData.success) {
				throw new Error('Không thể lấy CSRF token');
			}
			
			const formData = new FormData();
			formData.append('story_id', storyId);
			formData.append('chapter_id', chapterId);
			formData.append('parent_id', commentId);
			formData.append('content', content);
			formData.append('fuel_csrf_token', tokenData.token);
			
			const response = await fetch('<?php echo Uri::base(); ?>comment/add', {
				method: 'POST',
				body: formData
			});
			
			console.log('Reply response status:', response.status);
			
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			
			const data = await response.json();
			console.log('Reply response data:', data);
			
			if (data.success) {
				// Close reply form
				closeReplyForm(commentId);
				// Reload comments
				loadComments();
				// Show success message
				alert('Trả lời đã được đăng thành công!');
			} else {
				alert('Có lỗi xảy ra: ' + (data.message || 'Không thể gửi trả lời'));
			}
		} catch (error) {
			console.error('Error submitting reply:', error);
			alert('Có lỗi xảy ra khi gửi trả lời: ' + error.message);
		} finally {
			// Re-enable button
			submitBtn.disabled = false;
			submitBtn.innerHTML = originalText;
		}
	};
	
	function loadComments() {
		
		fetch(`<?php echo Uri::base(); ?>comment/get_comments?story_id=${storyId}&chapter_id=${chapterId}`)
			.then(response => {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.json();
			})
			.then(data => {
				console.log('Comments data:', data);
				displayComments(data);
				// Count all comments including replies
				let totalCount = 0;
				data.forEach(comment => {
					totalCount++; // Count parent comment
					totalCount += comment.replies ? comment.replies.length : 0; // Count replies
				});
				document.getElementById('commentCount').textContent = totalCount;
			})
			.catch(error => {
				console.error('Error loading comments:', error);
				document.getElementById('commentsList').innerHTML = 
					'<div class="text-center py-4"><p style="color: #94a3b8;">Không thể tải bình luận</p></div>';
			});
	}
	
	function displayReply(reply, depth = 0) {
		const userName = reply.user_name || 'Anonymous';
		const marginLeft = depth * 40;
		const fontSize = Math.max(0.75, 0.9 - (depth * 0.05)) + 'rem';
		const avatarSize = Math.max(24, 32 - (depth * 4)) + 'px';
		
		let html = `
			<div class="reply-item mb-3 p-3" data-comment-id="${reply.id}" style="background: rgba(30, 41, 59, 0.${Math.max(2, 4 - depth)}); border-radius: 8px; position: relative; margin-left: ${marginLeft}px; border-left: 2px solid rgba(99, 102, 241, ${Math.max(0.1, 0.3 - depth * 0.05)}); padding-left: 20px;">
				<div class="d-flex justify-content-between align-items-start mb-2">
					<div class="d-flex align-items-center">
						<div class="user-avatar me-2" style="width: ${avatarSize}; height: ${avatarSize}; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: ${fontSize}; font-weight: bold;">
							${userName.charAt(0).toUpperCase()}
						</div>
						<div>
							<h6 class="mb-0 text-white" style="font-size: ${fontSize};">${userName}</h6>
							<small class="text-muted">${reply.created_at}</small>
						</div>
					</div>
					<button class="btn btn-sm btn-outline-primary" onclick="replyToComment(${reply.id})" style="font-size: ${fontSize}; padding: 0.25rem 0.5rem;">
						<i class="fas fa-reply me-1"></i>Trả lời
					</button>
				</div>
				<div class="mt-2">
					<p class="mb-0 text-light" style="font-size: ${fontSize};">${reply.content}</p>
				</div>
				${reply.replies && reply.replies.length > 0 ? reply.replies.map(nested => displayReply(nested, depth + 1)).join('') : ''}
			</div>
		`;
		return html;
	}
	
	function displayComments(comments) {
		const commentsList = document.getElementById('commentsList');
		
		if (comments.length === 0) {
			commentsList.innerHTML = 
				'<div class="text-center py-4"><p style="color: #94a3b8;">Chưa có bình luận nào</p></div>';
			return;
		}
		
		let html = '';
		comments.forEach(comment => {
			const userName = comment.user_name || 'Anonymous';
			const firstLetter = userName.charAt(0).toUpperCase();
			html += `
				<div class="comment-item mb-4 p-3" data-comment-id="${comment.id}" style="background: rgba(15, 23, 42, 0.6); border-radius: 12px; border: 1px solid rgba(51, 65, 85, 0.3);">
					<div class="d-flex justify-content-between align-items-start mb-2">
						<div class="d-flex align-items-center">
							<div class="user-avatar me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
								${firstLetter}
							</div>
							<div>
								<h6 class="mb-0 text-white">${userName}</h6>
								<small class="text-muted">${comment.created_at}</small>
							</div>
						</div>
						<button class="btn btn-sm btn-outline-primary" onclick="replyToComment(${comment.id})">
							<i class="fas fa-reply me-1"></i>Trả lời
						</button>
					</div>
					<div class="comment-content mb-3">
						<p class="mb-0 text-light">${comment.content}</p>
					</div>
					${comment.replies.length > 0 ? `
						<div class="replies" style="margin-left: 60px; border-left: 2px solid rgba(99, 102, 241, 0.3); padding-left: 20px;">
							${comment.replies.map(reply => displayReply(reply, 0)).join('')}
						</div>
					` : ''}
				</div>
			`;
		});
		
		commentsList.innerHTML = html;
	}
	
	async function submitComment() {
		// Check if user is logged in
		<?php if (!Session::get('user_id')): ?>
		showLoginRequired();
		return;
		<?php endif; ?>
		
		const content = document.getElementById('commentContent').value.trim();
		if (!content) {
			alert('Vui lòng nhập nội dung bình luận');
			return;
		}
		
		// Disable submit button
		const submitBtn = document.querySelector('#commentForm button[type="submit"]');
		submitBtn.disabled = true;
		submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang đăng...';
		
		try {
			// Fetch fresh CSRF token
			const tokenResponse = await fetch('<?php echo Uri::base(); ?>comment/get_token');
			const tokenData = await tokenResponse.json();
			
			if (!tokenData.success) {
				throw new Error('Không thể lấy CSRF token');
			}
			
			// Prepare form data with fresh token
			const formData = new FormData();
			formData.append('story_id', <?php echo $story->id; ?>);
			formData.append('chapter_id', <?php echo $chapter->id; ?>);
			formData.append('content', content);
			formData.append('fuel_csrf_token', tokenData.token);
			
			// Submit comment
			const response = await fetch('<?php echo Uri::base(); ?>comment/add', {
				method: 'POST',
				body: formData
			});
			
			const result = await response.json();
			
			if (response.ok && result.success) {
				document.getElementById('commentContent').value = '';
				loadComments();
				alert('Bình luận đã được đăng thành công!');
			} else {
				throw new Error(result.message || 'Có l underr xảy ra khi đăng bình luận');
			}
		} catch (error) {
			console.error('Error submitting comment:', error);
			alert('Có lỗi xảy ra khi đăng bình luận: ' + error.message);
		} finally {
			// Re-enable submit button
			submitBtn.disabled = false;
			submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Đăng bình luận';
		}
	}
	
	
	function showLoginRequired() {
		// Create modal for login required
		const modalHtml = `
			<div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content" style="background: rgba(15, 23, 42, 0.95); border: 1px solid rgba(51, 65, 85, 0.5); border-radius: 16px;">
						<div class="modal-header" style="border-bottom: 1px solid rgba(51, 65, 85, 0.3);">
							<h5 class="modal-title text-white" id="loginRequiredModalLabel">
								<i class="fas fa-exclamation-triangle me-2" style="color: #f59e0b;"></i>
								Yêu cầu đăng nhập
							</h5>
							<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body text-center py-4">
							<div class="mb-4">
								<i class="fas fa-lock fa-3x mb-3" style="color: #6c5ce7;"></i>
								<h4 class="text-white mb-3">Bạn cần đăng nhập để bình luận</h4>
								<p class="text-muted mb-4">Để có thể trả lời bình luận, bạn cần đăng nhập vào tài khoản của mình.</p>
							</div>
							<div class="d-flex gap-3 justify-content-center">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
									<i class="fas fa-times me-2"></i>Hủy
								</button>
								<a href="<?php echo Uri::base(); ?>user/login" class="btn btn-primary">
									<i class="fas fa-sign-in-alt me-2"></i>Đăng nhập ngay
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		`;
		
		// Remove existing modal if any
		const existingModal = document.getElementById('loginRequiredModal');
		if (existingModal) {
			existingModal.remove();
		}
		
		// Add modal to body
		document.body.insertAdjacentHTML('beforeend', modalHtml);
		
		// Show modal
		const modal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
		modal.show();
		
		// Remove modal from DOM when hidden
		document.getElementById('loginRequiredModal').addEventListener('hidden.bs.modal', function() {
			this.remove();
		});
	}
	
	// Keyboard navigation
	document.addEventListener('keydown', function(e) {
		// Left arrow key - previous chapter
		if (e.key === 'ArrowLeft') {
			<?php if (isset($previous_chapter) && $previous_chapter): ?>
				window.location.href = '<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $previous_chapter->chapter_number; ?>';
			<?php endif; ?>
		}
		
		// Right arrow key - next chapter
		if (e.key === 'ArrowRight') {
			<?php if (isset($next_chapter) && $next_chapter): ?>
				window.location.href = '<?php echo Uri::base(); ?>client/read/<?php echo $story->slug; ?>/<?php echo $next_chapter->chapter_number; ?>';
			<?php endif; ?>
		}
		
		// Escape key - back to story
		if (e.key === 'Escape') {
			window.location.href = '<?php echo Uri::base(); ?>client/story/<?php echo $story->slug; ?>';
		}
	});

	// Navigation sticky behavior
	let lastScrollTop = 0;
	let navBar = document.getElementById('navigation-header');
	let chapterContent = document.getElementById('chapter-content');
	let isSticky = false;
	
	window.addEventListener('scroll', function() {
		let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
		
		if (navBar && chapterContent) {
			if (scrollTop > 50) {
				// When scrolled down - make sticky
				if (!isSticky) {
					navBar.classList.add('sticky');
					chapterContent.classList.add('with-sticky-nav');
					isSticky = true;
				}
				
				// Auto-hide when scrolling down
				if (scrollTop > lastScrollTop && scrollTop > 100) {
					navBar.style.transform = 'translateY(-100%)';
				} else {
					navBar.style.transform = 'translateY(0)';
				}
			} else {
				// When at top - make normal
				if (isSticky) {
					navBar.classList.remove('sticky');
					chapterContent.classList.remove('with-sticky-nav');
					navBar.style.transform = 'translateY(0)';
					isSticky = false;
				}
			}
		}
		
		lastScrollTop = scrollTop;
	});

	// Lazy loading for images
	if ('IntersectionObserver' in window) {
		const imageObserver = new IntersectionObserver((entries, observer) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					const img = entry.target;
					img.src = img.dataset.src || img.src;
					img.classList.remove('lazy');
					imageObserver.unobserve(img);
				}
			});
		});

		document.querySelectorAll('img[loading="lazy"]').forEach(img => {
			imageObserver.observe(img);
		});
	}

	// Double-click to fullscreen
	document.querySelectorAll('.chapter-image').forEach(img => {
		img.addEventListener('dblclick', function() {
			if (this.requestFullscreen) {
				this.requestFullscreen();
			} else if (this.webkitRequestFullscreen) {
				this.webkitRequestFullscreen();
			} else if (this.msRequestFullscreen) {
				this.msRequestFullscreen();
			}
		});
	});
});
</script>

<style>
body {
	background: #0a0a0a;
}

.reader-container {
	background: linear-gradient(to bottom, rgba(15, 23, 42, 0.9), rgba(10, 10, 10, 1));
	border-radius: 24px;
	padding: 40px;
	border: 1px solid rgba(51, 65, 85, 0.3);
	max-width: 100%;
	margin: 0 auto;
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	box-shadow: var(--shadow-2xl);
}

/* Reading Controls */
.reading-controls {
	position: fixed;
	bottom: 30px;
	right: 30px;
	z-index: 1000;
	background: rgba(30, 41, 59, 0.95);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border: 1px solid rgba(51, 65, 85, 0.5);
	border-radius: 16px;
	padding: 1rem;
	box-shadow: var(--shadow-2xl);
	display: flex;
	flex-direction: column;
	gap: 0.75rem;
}

.reading-controls button {
	background: var(--primary-color);
	color: white;
	border: none;
	width: 48px;
	height: 48px;
	border-radius: 12px;
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	transition: all 0.3s ease;
	box-shadow: var(--shadow-md);
}

.reading-controls button:hover {
	background: var(--primary-dark);
	transform: translateY(-2px);
	box-shadow: var(--shadow-lg);
}


	.chapter-image {
		width: 100%;
		height: auto;
		margin-bottom: 20px;
		border-radius: 12px;
		transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
		box-shadow: 0 8px 24px rgba(0,0,0,0.3);
		background: linear-gradient(135deg, rgba(139, 126, 248, 0.1) 0%, rgba(255, 159, 102, 0.1) 100%);
		padding: 4px;
	}

	.chapter-image:hover {
		transform: scale(1.01);
		box-shadow: 0 12px 40px rgba(139, 126, 248, 0.2);
	}

.chapter-image-container {
	text-align: center;
}

/* Responsive adjustments for centered content */
@media (max-width: 768px) {
	.reader-container {
		padding: 15px;
		margin: 0 10px;
	}
	
	.chapter-image {
		margin-bottom: 8px;
	}
}

.navigation-buttons {
	position: fixed;
	bottom: 20px;
	right: 20px;
	z-index: 1000;
}

.btn-floating {
	width: 50px;
	height: 50px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	margin: 5px;
	box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

/* Mobile optimizations */
@media (max-width: 768px) {
	.reader-container {
		padding: 10px;
	}
	
	.chapter-image {
		margin-bottom: 5px;
	}
	
	.navigation-buttons {
		bottom: 10px;
		right: 10px;
	}
}

/* Dark mode support */
.btn-outline-primary,
.btn-outline-secondary,
.btn-outline-info {
	border-color: #6c5ce7;
	color: #6c5ce7;
	background: transparent;
}

.btn-outline-primary:hover,
.btn-outline-secondary:hover,
.btn-outline-info:hover {
	background-color: #6c5ce7;
	border-color: #6c5ce7;
	color: white;
}

/* Navigation header button styling */
#navigation-header .btn-outline-light {
	border-color: rgba(255, 255, 255, 0.3) !important;
	color: #ffffff !important;
	background: rgba(255, 255, 255, 0.1) !important;
	backdrop-filter: blur(10px);
	-webkit-backdrop-filter: blur(10px);
	transition: all 0.3s ease;
}

#navigation-header .btn-outline-light:hover {
	background-color: rgba(255, 255, 255, 0.2) !important;
	border-color: rgba(255, 255, 255, 0.5) !important;
	color: #ffffff !important;
	transform: translateY(-1px);
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

#navigation-header .btn-outline-light:focus {
	box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25) !important;
}

#navigation-header .btn-outline-light:active {
	background-color: rgba(255, 255, 255, 0.3) !important;
	border-color: rgba(255, 255, 255, 0.7) !important;
	color: #ffffff !important;
}

/* Dropdown toggle specific styling */
#navigation-header .btn-outline-light.dropdown-toggle::after {
	border-top-color: #ffffff !important;
}

#navigation-header .btn-outline-light.dropdown-toggle:focus {
	box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25) !important;
}

.btn-primary {
	background: #6c5ce7;
	border-color: #6c5ce7;
}

.btn-primary:hover {
	background: #5a4fc7;
	border-color: #5a4fc7;
}

.dropdown-menu {
	background: #252525;
	border: 1px solid #444;
}

.dropdown-item {
	color: #e0e0e0;
}

.dropdown-item:hover {
	background: #2d3436;
	color: #fff;
}

.dropdown-item.active {
	background: #6c5ce7;
	color: white;
}

/* Smooth scrolling */
html {
	scroll-behavior: smooth;
}

/* Comment threading styles */
.comment-item {
	position: relative;
}

.replies {
	position: relative;
}

.replies::before {
	content: '';
	position: absolute;
	left: -20px;
	top: 0;
	bottom: 0;
	width: 2px;
	background: linear-gradient(to bottom, rgba(99, 102, 241, 0.3), rgba(99, 102, 241, 0.1));
}

.reply-item {
	position: relative;
	transition: all 0.3s ease;
}

.reply-item:hover {
	background: rgba(30, 41, 59, 0.6) !important;
	transform: translateX(5px);
}

/* Loading animation for images */
.chapter-image {
	opacity: 0;
	animation: fadeIn 0.5s ease-in-out forwards;
}

@keyframes fadeIn {
	from { opacity: 0; }
	to { opacity: 1; }
}

@keyframes fadeInUp {
	from {
		opacity: 0;
		transform: translateY(20px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

/* Navigation sticky behavior */
#navigation-header {
	transition: all 0.3s ease;
}

#navigation-header.sticky {
	position: fixed !important;
	top: 0 !important;
	left: 0 !important;
	right: 0 !important;
	margin: 0 !important;
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

#chapter-content {
	transition: margin-top 0.3s ease;
}

#chapter-content.with-sticky-nav {
	margin-top: 100px !important;
}

/* Responsive reading controls */
@media (max-width: 768px) {
	.reading-controls {
		bottom: 20px;
		right: 20px;
		padding: 0.75rem;
	}

	.reading-controls button {
		width: 40px;
		height: 40px;
	}

	.reader-container {
		padding: 20px;
	}
	
	#chapter-content.with-sticky-nav {
		margin-top: 80px !important;
	}
}

/* Login Required Modal Styles */
#loginRequiredModal .modal-content {
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
}

#loginRequiredModal .modal-header {
	background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 126, 248, 0.1));
}

#loginRequiredModal .btn-primary {
	background: linear-gradient(135deg, #6c5ce7, #5a4fc7);
	border: none;
	padding: 12px 24px;
	border-radius: 8px;
	font-weight: 600;
	transition: all 0.3s ease;
}

#loginRequiredModal .btn-primary:hover {
	background: linear-gradient(135deg, #5a4fc7, #4c3fb8);
	transform: translateY(-2px);
	box-shadow: 0 8px 25px rgba(108, 92, 231, 0.3);
}

#loginRequiredModal .btn-secondary {
	background: rgba(51, 65, 85, 0.6);
	border: 1px solid rgba(51, 65, 85, 0.8);
	color: #e2e8f0;
	padding: 12px 24px;
	border-radius: 8px;
	font-weight: 600;
	transition: all 0.3s ease;
}

#loginRequiredModal .btn-secondary:hover {
	background: rgba(51, 65, 85, 0.8);
	border-color: rgba(51, 65, 85, 1);
	color: white;
}

/* Comment form textarea placeholder styling */
#commentContent::placeholder,
.reply-form textarea::placeholder {
	color: #ffffff !important;
	opacity: 0.8;
}

#commentContent::-webkit-input-placeholder,
.reply-form textarea::-webkit-input-placeholder {
	color: #ffffff !important;
	opacity: 0.8;
}

#commentContent::-moz-placeholder,
.reply-form textarea::-moz-placeholder {
	color: #ffffff !important;
	opacity: 0.8;
}

#commentContent:-ms-input-placeholder,
.reply-form textarea:-ms-input-placeholder {
	color: #ffffff !important;
	opacity: 0.8;
}

#commentContent:-moz-placeholder,
.reply-form textarea:-moz-placeholder {
	color: #ffffff !important;
	opacity: 0.8;
}
</style>
