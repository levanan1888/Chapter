<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="h3 mb-0">
		<i class="fas fa-plus me-2"></i>Thêm chương mới
		<?php if (isset($story)): ?>
			<small class="text-muted">- <?php echo $story->title; ?></small>
		<?php endif; ?>
	</h2>
	<?php if (isset($story)): ?>
		<a href="<?php echo Uri::base(); ?>admin/chapters/<?php echo $story->id; ?>" class="btn btn-outline-secondary">
			<i class="fas fa-arrow-left me-2"></i>Quay lại
		</a>
	<?php endif; ?>
</div>

<div class="card">
	<div class="card-body">
		<?php if (isset($error_message) && !empty($error_message)): ?>
			<div class="alert alert-danger">
				<i class="fas fa-exclamation-triangle me-2"></i>
				<?php echo $error_message; ?>
			</div>
		<?php endif; ?>

		<form method="POST" action="<?php echo Uri::base(); ?>admin/chapters/add/<?php echo isset($story) ? $story->id : ''; ?>" enctype="multipart/form-data">
			<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" id="csrf-token" value="">
			<div class="row">
				<div class="col-md-6">
                    <div class="mb-3">
                        <label for="title" class="form-label">Tên chương *</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo isset($form_data['title']) ? $form_data['title'] : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="chapter_number" class="form-label">Thứ tự *</label>
                        <input type="number" class="form-control" id="chapter_number" name="chapter_number" 
                               value="<?php echo isset($form_data['chapter_number']) ? $form_data['chapter_number'] : ''; ?>" required>
                        <div class="form-text">Thứ tự hiển thị của chương trong truyện (1, 2, 3...)</div>
                    </div>
				</div>

				<div class="col-md-6">
					<div class="mb-3">
						<label class="form-label">Ảnh chương *</label>
						<div class="image-upload-container">
							<div class="row" id="image-upload-grid">
								<!-- Images will be added here -->
							</div>
							<div class="d-flex gap-2 mt-3 flex-wrap">
								<button type="button" class="btn btn-outline-primary" id="add-image-btn">
									<i class="fas fa-plus me-2"></i>Thêm ảnh
								</button>
								<button type="button" class="btn btn-outline-success" id="add-multiple-images-btn">
									<i class="fas fa-images me-2"></i>Thêm nhiều ảnh
								</button>
								<button type="button" class="btn btn-outline-warning" id="clear-all-images-btn" style="display: none;">
									<i class="fas fa-trash-alt me-2"></i>Xóa tất cả
								</button>
							</div>
						</div>
						<div class="form-text">Chọn ảnh (JPG, PNG, GIF, WebP - tối đa 10MB mỗi ảnh)</div>
						<div id="upload-progress" class="mt-2" style="display: none;">
							<div class="progress">
								<div class="progress-bar" role="progressbar" style="width: 0%"></div>
							</div>
							<small class="text-muted">Đang xử lý ảnh...</small>
						</div>
					</div>
				</div>
			</div>

			<!-- Hidden inputs for form submission -->
			<div id="hidden-inputs-container">
				<!-- Hidden file inputs will be added here -->
			</div>
			<input type="hidden" id="image-order" name="image_order" value="">
			</div>

			<div class="d-flex justify-content-end gap-2">
				<?php if (isset($story)): ?>
					<a href="<?php echo Uri::base(); ?>admin/chapters/<?php echo $story->id; ?>" class="btn btn-secondary">
						<i class="fas fa-times me-2"></i>Hủy
					</a>
				<?php endif; ?>
				<button type="submit" class="btn btn-primary">
					<i class="fas fa-save me-2"></i>Lưu chương
				</button>
			</div>
		</form>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CSRF token on page load
    window.currentCsrfToken = '<?php echo \Security::fetch_token(); ?>';
    console.log('Initial CSRF token:', window.currentCsrfToken);
    
    // Update CSRF token in meta tag and hidden form when page loads
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    const hiddenTokens = document.querySelectorAll('input[name="<?php echo \Config::get('security.csrf_token_key'); ?>"]');
    
    if (metaToken) {
        metaToken.setAttribute('content', window.currentCsrfToken);
    }
    hiddenTokens.forEach(token => {
        token.value = window.currentCsrfToken;
    });
    
    const addImageBtn = document.getElementById('add-image-btn');
    const addMultipleImagesBtn = document.getElementById('add-multiple-images-btn');
    const clearAllImagesBtn = document.getElementById('clear-all-images-btn');
    const imageUploadGrid = document.getElementById('image-upload-grid');
    const hiddenInputsContainer = document.getElementById('hidden-inputs-container');
    const imageOrderInput = document.getElementById('image-order');
    
    let imageCounter = 0;
    let selectedImages = [];

    // Function to refresh CSRF token
    function refreshCsrfToken() {
        fetch('<?php echo Uri::base(); ?>admin/chapters/add/<?php echo isset($story) ? $story->id : ''; ?>', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newToken = doc.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                            doc.querySelector('input[name="<?php echo \Config::get('security.csrf_token_key'); ?>"]')?.value;
            if (newToken) {
                window.currentCsrfToken = newToken;
                
                // Update meta tag and hidden forms with new token
                const metaToken = document.querySelector('meta[name="csrf-token"]');
                const hiddenTokens = document.querySelectorAll('input[name="<?php echo \Config::get('security.csrf_token_key'); ?>"]');
                
                if (metaToken) {
                    metaToken.setAttribute('content', newToken);
                }
                hiddenTokens.forEach(token => {
                    token.value = newToken;
                });
                
                console.log('CSRF token refreshed:', newToken);
            }
        })
        .catch(err => {
            console.error('Failed to refresh CSRF token:', err);
        });
    }
    
    // Refresh CSRF token when page becomes visible (user returns from another page)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            console.log('Page became visible, refreshing CSRF token...');
            refreshCsrfToken();
        }
    });
    
    // Also refresh token when page gains focus
    window.addEventListener('focus', function() {
        console.log('Window gained focus, refreshing CSRF token...');
        refreshCsrfToken();
    });

    // Add image button click
    addImageBtn.addEventListener('click', function() {
        addImageSlot();
    });

    // Add multiple images button click
    addMultipleImagesBtn.addEventListener('click', function() {
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.multiple = true;
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';
        
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            if (files.length === 0) return;
            
            // Validate files
            const validFiles = [];
            const errors = [];
            
            files.forEach((file, index) => {
                if (!file.type.startsWith('image/')) {
                    errors.push(`File ${index + 1}: Không phải là file ảnh`);
                    return;
                }
                
                if (file.size > 2 * 1024 * 1024) {
                    errors.push(`File ${index + 1}: Kích thước vượt quá 2MB`);
                    return;
                }
                
                validFiles.push(file);
            });
            
            if (errors.length > 0) {
                alert('Một số file không hợp lệ:\n' + errors.join('\n'));
            }
            
            if (validFiles.length > 0) {
                // Show progress indicator
                const progressContainer = document.getElementById('upload-progress');
                const progressBar = progressContainer.querySelector('.progress-bar');
                const progressText = progressContainer.querySelector('small');
                
                progressContainer.style.display = 'block';
                progressBar.style.width = '0%';
                progressText.textContent = `Đang xử lý ${validFiles.length} ảnh...`;
                
                // Process files with progress
                let processedCount = 0;
                validFiles.forEach((file, index) => {
                    setTimeout(() => {
                        addImageSlotWithFile(file);
                        processedCount++;
                        
                        const progress = Math.round((processedCount / validFiles.length) * 100);
                        progressBar.style.width = progress + '%';
                        progressText.textContent = `Đã xử lý ${processedCount}/${validFiles.length} ảnh`;
                        
                        if (processedCount === validFiles.length) {
                            setTimeout(() => {
                                progressContainer.style.display = 'none';
                            }, 1000);
                        }
                    }, index * 100); // Stagger processing for better UX
                });
            }
        });
        
        document.body.appendChild(fileInput);
        fileInput.click();
        document.body.removeChild(fileInput);
    });

    // Clear all images button
    clearAllImagesBtn.addEventListener('click', function() {
        if (confirm('Bạn có chắc chắn muốn xóa tất cả ảnh đã chọn?')) {
            // Remove all image slots
            const imageSlots = document.querySelectorAll('.image-upload-slot');
            imageSlots.forEach(slot => {
                slot.closest('.col-lg-4, .col-md-6, .col-sm-12').remove();
            });
            
            // Clear selected images
            selectedImages = [];
            imageCounter = 0;
            
            // Update UI
            updateBulkActionButtons();
            updateImageOrder();
            
            // Add one empty slot
            addImageSlot();
        }
    });


    function addImageSlot() {
        const imageId = `image_${imageCounter}`;
        imageCounter++;

        const col = document.createElement('div');
        col.className = 'col-lg-4 col-md-6 col-sm-12 mb-3';
        col.id = `image-slot-${imageId}`;
        
        col.innerHTML = `
            <div class="card image-upload-slot" style="min-height: 300px;">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                    <div class="image-preview-container" style="display: none;">
                        <img class="img-fluid rounded" style="max-height: 250px; width: 100%; object-fit: contain;" alt="Preview">
                    </div>
                    <div class="image-placeholder">
                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-3">Nhấn để chọn ảnh</p>
                        <input type="file" class="d-none" accept="image/*" data-image-id="${imageId}">
                        <button type="button" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-upload me-2"></i>Chọn ảnh
                        </button>
                    </div>
                    <div class="image-actions mt-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <small class="text-muted">Trang <span class="page-number">1</span></small>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary move-up-btn" title="Di chuyển lên">
                                    <i class="fas fa-arrow-up"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary move-down-btn" title="Di chuyển xuống">
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info view-full-btn" title="Xem full size">
                                    <i class="fas fa-expand"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-image-btn" title="Xóa ảnh">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        imageUploadGrid.appendChild(col);
        setupImageSlot(col, imageId);
    }

    function addImageSlotWithFile(file) {
        const imageId = `image_${imageCounter}`;
        imageCounter++;

        const col = document.createElement('div');
        col.className = 'col-lg-4 col-md-6 col-sm-12 mb-3';
        col.id = `image-slot-${imageId}`;
        
        col.innerHTML = `
            <div class="card image-upload-slot" style="min-height: 300px;">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                    <div class="image-preview-container" style="display: none;">
                        <img class="img-fluid rounded" style="max-height: 250px; width: 100%; object-fit: contain;" alt="Preview">
                    </div>
                    <div class="image-placeholder">
                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-3">Nhấn để chọn ảnh</p>
                        <input type="file" class="d-none" accept="image/*" data-image-id="${imageId}">
                        <button type="button" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-upload me-2"></i>Chọn ảnh
                        </button>
                    </div>
                    <div class="image-actions mt-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <small class="text-muted">Trang <span class="page-number">1</span></small>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary move-up-btn" title="Di chuyển lên">
                                    <i class="fas fa-arrow-up"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary move-down-btn" title="Di chuyển xuống">
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info view-full-btn" title="Xem full size">
                                    <i class="fas fa-expand"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-image-btn" title="Xóa ảnh">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        imageUploadGrid.appendChild(col);
        setupImageSlot(col, imageId);
        
        // Set the file directly
        const fileInput = col.querySelector('input[type="file"]');
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        
        // Trigger the file change event
        const event = new Event('change', { bubbles: true });
        fileInput.dispatchEvent(event);
    }

    function setupImageSlot(slot, imageId) {
        const fileInput = slot.querySelector('input[type="file"]');
        const placeholder = slot.querySelector('.image-placeholder');
        const previewContainer = slot.querySelector('.image-preview-container');
        const previewImg = slot.querySelector('img');
        const actions = slot.querySelector('.image-actions');
        const chooseBtn = slot.querySelector('button');
        const viewFullBtn = slot.querySelector('.view-full-btn');
        const moveUpBtn = slot.querySelector('.move-up-btn');
        const moveDownBtn = slot.querySelector('.move-down-btn');
        const removeBtn = slot.querySelector('.remove-image-btn');
        const pageNumber = slot.querySelector('.page-number');

        // Click to choose image
        chooseBtn.addEventListener('click', () => fileInput.click());
        slot.addEventListener('click', (e) => {
            if (e.target === slot || e.target === placeholder) {
                fileInput.click();
            }
        });

        // File input change
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Kích thước ảnh không được vượt quá 2MB');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    placeholder.style.display = 'none';
                    previewContainer.style.display = 'block';
                    actions.style.display = 'block';
                    
                    // Add has-image class
                    slot.classList.add('has-image');
                    
                    // Update page number
                    const currentImages = document.querySelectorAll('.image-upload-slot .image-preview-container[style*="block"]');
                    pageNumber.textContent = currentImages.length;
                    
                    // Store image data
                    selectedImages.push({
                        id: imageId,
                        file: file,
                        preview: e.target.result
                    });
                    
                    updateImageOrder();
                    updateBulkActionButtons();
                };
                reader.readAsDataURL(file);
            }
        });

        // View full size
        viewFullBtn.addEventListener('click', function() {
            showImageModal(previewImg.src);
        });

        // Remove image
        removeBtn.addEventListener('click', function() {
            slot.remove();
            selectedImages = selectedImages.filter(img => img.id !== imageId);
            updatePageNumbers();
            updateImageOrder();
            updateBulkActionButtons();
        });

        // Move up
        moveUpBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const col = slot.closest('.col-lg-4, .col-md-6, .col-sm-12');
            if (col && col.previousElementSibling) {
                col.parentNode.insertBefore(col, col.previousElementSibling);
                updatePageNumbers();
                updateImageOrder();
            }
        });

        // Move down
        moveDownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const col = slot.closest('.col-lg-4, .col-md-6, .col-sm-12');
            if (col && col.nextElementSibling) {
                col.parentNode.insertBefore(col.nextElementSibling, col);
                updatePageNumbers();
                updateImageOrder();
            }
        });
    }

    function updatePageNumbers() {
        const visibleSlots = document.querySelectorAll('.image-upload-slot .image-preview-container[style*="block"]');
        visibleSlots.forEach((slot, index) => {
            const pageNumber = slot.closest('.image-upload-slot').querySelector('.page-number');
            if (pageNumber) {
                pageNumber.textContent = index + 1;
            }
        });
    }

    function updateImageOrder() {
        const visibleSlots = document.querySelectorAll('.image-upload-slot .image-preview-container[style*="block"]');
        const order = [];
        visibleSlots.forEach(slot => {
            const fileInput = slot.closest('.image-upload-slot').querySelector('input[type="file"]');
            const imageId = fileInput.dataset.imageId;
            order.push(imageId);
        });
        imageOrderInput.value = JSON.stringify(order);
        
        // Update hidden inputs for form submission
        updateHiddenInputs();
    }

    function updateBulkActionButtons() {
        const visibleSlots = document.querySelectorAll('.image-upload-slot .image-preview-container[style*="block"]');
        const hasImages = visibleSlots.length > 0;
        
        clearAllImagesBtn.style.display = hasImages ? 'inline-block' : 'none';
    }

    function updateHiddenInputs() {
        // Clear existing hidden inputs
        hiddenInputsContainer.innerHTML = '';
        
        // Create actual file inputs for each selected image
        selectedImages.forEach((imageData, index) => {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.name = 'images[]';
            fileInput.style.display = 'none';
            
            // Create a new FileList-like object
            const dt = new DataTransfer();
            dt.items.add(imageData.file);
            fileInput.files = dt.files;
            
            hiddenInputsContainer.appendChild(fileInput);
        });
    }

    // Handle form submission: just ensure image order is updated, then allow normal submit (CSRF-safe)
    document.querySelector('form').addEventListener('submit', function() {
        // Refresh CSRF token before submission
        refreshCsrfToken();
        
        updateImageOrder();
    });

    function showImageModal(imageSrc) {
        // Create modal
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xem ảnh</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${imageSrc}" class="img-fluid" style="max-height: 80vh;">
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        modal.addEventListener('hidden.bs.modal', function() {
            modal.remove();
        });
    }

    // Add first image slot on load
    addImageSlot();
});
</script>

<style>
.image-upload-slot {
    border: 2px dashed #dee2e6;
    transition: all 0.3s ease;
    cursor: pointer;
}

.image-upload-slot:hover {
    border-color: #007bff;
    background-color: rgba(0,123,255,0.05);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.image-upload-slot.has-image {
    border-color: #28a745;
    border-style: solid;
}

.image-upload-slot .image-placeholder {
    transition: all 0.3s ease;
}

.image-upload-slot:hover .image-placeholder {
    color: #007bff;
}

.image-upload-slot .image-preview-container img {
    transition: all 0.3s ease;
}

.image-upload-slot:hover .image-preview-container img {
    transform: scale(1.02);
}

.image-actions {
    transition: all 0.3s ease;
}

#add-image-btn {
    transition: all 0.3s ease;
}

#add-image-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,123,255,0.3);
}

.modal-xl {
    max-width: 95vw;
}

@media (max-width: 768px) {
    .image-upload-slot {
        min-height: 250px;
    }
}
</style>
