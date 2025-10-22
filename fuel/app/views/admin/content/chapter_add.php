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
			<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key'); ?>" value="<?php echo \Security::fetch_token(); ?>">
			<div class="row">
				<div class="col-md-6">
					<div class="mb-3">
						<label for="title" class="form-label">Tên chương *</label>
						<input type="text" class="form-control" id="title" name="title" 
							   value="<?php echo isset($form_data['title']) ? $form_data['title'] : ''; ?>" required>
					</div>

					<div class="mb-3">
						<label for="chapter_number" class="form-label">Số chương *</label>
						<input type="number" class="form-control" id="chapter_number" name="chapter_number" 
							   value="<?php echo isset($form_data['chapter_number']) ? $form_data['chapter_number'] : ''; ?>" required>
					</div>
				</div>

				<div class="col-md-6">
					<div class="mb-3">
						<label class="form-label">Ảnh chương *</label>
						<div class="image-upload-container">
							<div class="row" id="image-upload-grid">
								<!-- Images will be added here -->
							</div>
							<button type="button" class="btn btn-outline-primary mt-3" id="add-image-btn">
								<i class="fas fa-plus me-2"></i>Thêm ảnh
							</button>
						</div>
						<div class="form-text">Chọn ảnh (JPG, PNG, GIF - tối đa 2MB mỗi ảnh)</div>
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
    const addImageBtn = document.getElementById('add-image-btn');
    const imageUploadGrid = document.getElementById('image-upload-grid');
    const hiddenInputsContainer = document.getElementById('hidden-inputs-container');
    const imageOrderInput = document.getElementById('image-order');
    
    let imageCounter = 0;
    let selectedImages = [];

    // Add image button click
    addImageBtn.addEventListener('click', function() {
        addImageSlot();
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

    function updateHiddenInputs() {
        // Clear existing hidden inputs
        hiddenInputsContainer.innerHTML = '';
        
        // Create hidden inputs for each selected image
        selectedImages.forEach((imageData, index) => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'images[]';
            hiddenInput.value = imageData.file.name;
            hiddenInputsContainer.appendChild(hiddenInput);
        });
    }

    // Handle form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();

        // Build FormData manually to control file order and avoid duplicates
        const form = this;
        const formData = new FormData();

        // Append non-file fields from the form
        const rawFormData = new FormData(form);
        for (const [key, value] of rawFormData.entries()) {
            if (key === 'images[]') {
                continue; // skip any auto-captured file inputs
            }
            formData.append(key, value);
        }

        // Append files in the specified order
        const order = JSON.parse(imageOrderInput.value || '[]');
        order.forEach(imageId => {
            const slot = document.getElementById(`image-slot-${imageId}`);
            if (slot) {
                const fileInput = slot.querySelector('input[type="file"]');
                if (fileInput && fileInput.files[0]) {
                    formData.append('images[]', fileInput.files[0]);
                }
            }
        });

        const xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                window.location.href = window.location.href.replace('/add/', '/');
            } else {
                alert('Có lỗi xảy ra khi lưu chương');
            }
        };
        xhr.send(formData);
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
