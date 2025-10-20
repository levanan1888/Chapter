<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Quản lý Admin</h1>
    <div>
        <a href="<?php echo Uri::create('admin/add'); ?>" class="btn btn-success btn-sm shadow-sm">
            <i class="fas fa-user-plus fa-sm text-white-50"></i> Thêm Admin mới
        </a>
        <a href="<?php echo Uri::create('admin/dashboard'); ?>" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-tachometer-alt fa-sm text-white-50"></i> Dashboard
        </a>
    </div>
</div>

<!-- Statistics Row -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tổng số Admin</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_admins; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Admin hoạt động</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php 
                            $active_count = 0;
                            if (!empty($admins)) {
                                foreach ($admins as $admin) {
                                    if ($admin->is_active) $active_count++;
                                }
                            }
                            echo $active_count;
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách Admin</h6>
        <div>
            <a href="<?php echo Uri::create('admin/deleted'); ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-trash-restore"></i> Đã xóa
            </a>
            <button id="bulk-delete" class="btn btn-danger btn-sm ml-2">
                <i class="fas fa-trash"></i> Xóa đã chọn
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($admins)): ?>
            <div class="table-responsive">
                <form id="bulk-form">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Họ tên</th>
                            <th>Trạng thái</th>
                            <th>Lần đăng nhập cuối</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><input type="checkbox" class="js-select" name="ids[]" value="<?php echo $admin->id; ?>"></td>
                            <td><?php echo $admin->id; ?></td>
                            <td>
                                <strong><?php echo htmlentities($admin->username); ?></strong>
                                <?php if ($admin->id == Session::get('admin_id')): ?>
                                    <span class="badge badge-info">(Bạn)</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlentities($admin->email); ?></td>
                            <td><?php echo htmlentities($admin->full_name ?: '-'); ?></td>
                            <td>
                                <?php if ($admin->is_active): ?>
                                    <span class="badge badge-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Không hoạt động</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($admin->last_login): ?>
                                    <?php echo date('d/m/Y H:i', strtotime($admin->last_login)); ?>
                                <?php else: ?>
                                    <span class="text-muted">Chưa đăng nhập</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($admin->created_at)); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo Uri::create('admin/edit/' . $admin->id); ?>" 
                                       class="btn btn-warning btn-sm" 
                                       title="Sửa admin">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($admin->id != Session::get('admin_id')): ?>
                                    <button type="button" 
                                            class="btn btn-danger btn-sm js-delete-admin" 
                                            data-id="<?php echo $admin->id; ?>"
                                            data-username="<?php echo htmlentities($admin->username); ?>"
                                            title="Xóa admin">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </form>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="d-flex justify-content-center">
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo Uri::create('admin/manage', array('page' => $current_page - 1)); ?>">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="page-item active">
                            <span class="page-link">
                                Trang <?php echo $current_page; ?> / <?php echo $total_pages; ?>
                            </span>
                        </li>

                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo Uri::create('admin/manage', array('page' => $current_page + 1)); ?>">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                <h4 class="text-gray-500">Chưa có admin nào</h4>
                <p class="text-gray-500">Hãy thêm admin đầu tiên để bắt đầu sử dụng hệ thống.</p>
                <a href="<?php echo Uri::create('admin/add'); ?>" class="btn btn-success">
                    <i class="fas fa-user-plus"></i> Thêm Admin đầu tiên
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa admin <strong id="delete-username"></strong>?</p>
                <p class="text-danger"><small>Hành động này không thể hoàn tác.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Xóa</button>
            </div>
        </div>
    </div>
</div>

<!-- Alternative confirmation using browser confirm -->
<div id="confirm-delete-alternative" style="display: none;">
    <p>Bạn có chắc chắn muốn xóa admin này?</p>
</div>

<script>
// Chờ jQuery load xong
function waitForJQuery(callback) {
    if (typeof jQuery !== 'undefined') {
        callback(jQuery);
    } else {
        setTimeout(function() {
            waitForJQuery(callback);
        }, 100);
    }
}

waitForJQuery(function($) {
    let deleteId = null;
    
    // Xử lý click nút xóa
    $(document).on('click', '.js-delete-admin', function(e) {
        e.preventDefault();
        deleteId = $(this).data('id');
        const username = $(this).data('username');
        
        // Thử sử dụng Bootstrap modal trước
        try {
            $('#delete-username').text(username);
            $('#deleteModal').modal('show');
        } catch (error) {
            // Fallback: sử dụng browser confirm
            console.log('Modal error, using browser confirm:', error);
            if (confirm('Bạn có chắc chắn muốn xóa admin "' + username + '"?')) {
                performDelete(deleteId);
            }
        }
    });
    
    // Xử lý xác nhận xóa từ modal
    $(document).on('click', '#confirm-delete', function() {
        if (deleteId) {
            performDelete(deleteId);
        }
    });
    
    // Select all toggle
    $(document).on('change', '#select-all', function() {
        $('.js-select').prop('checked', $(this).is(':checked'));
    });

    // Bulk delete
    $(document).on('click', '#bulk-delete', function(e) {
        e.preventDefault();
        const ids = $('.js-select:checked').map(function(){ return $(this).val(); }).get();
        if (ids.length === 0) { alert('Chọn ít nhất 1 admin.'); return; }
        if (!confirm('Bạn có chắc chắn muốn xóa ' + ids.length + ' admin đã chọn?')) return;
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(this).prop('disabled', true).text('Đang xóa...');
        $.ajax({
            url: '<?php echo Uri::create('admin/bulk_delete'); ?>',
            type: 'POST',
            dataType: 'json',
            data: { ids: ids, fuel_csrf_token: csrfToken },
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(resp){ alert(resp.message); location.reload(); },
            error: function(){ alert('Có lỗi xảy ra.'); },
            complete: function(){ $('#bulk-delete').prop('disabled', false).text('Xóa đã chọn'); }
        });
    });
    
    // Hàm thực hiện xóa
    function performDelete(id) {
        // Disable button để tránh double click
        $('#confirm-delete').prop('disabled', true).text('Đang xóa...');
        
        // Lấy CSRF token từ meta tag
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        // Debug CSRF token
        console.log('CSRF Token:', csrfToken);
        
        if (!csrfToken) {
            alert('Lỗi: Không tìm thấy CSRF token. Vui lòng reload trang và thử lại.');
            return;
        }
        
        $.ajax({
            url: '<?php echo Uri::create("admin/delete/"); ?>' + id,
            type: 'POST',
            dataType: 'json',
            data: {
                fuel_csrf_token: csrfToken
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                if (response.success) {
                    // Hiển thị thông báo thành công
                    alert(response.message);
                    // Reload trang
                    location.reload();
                } else {
                    alert('Lỗi: ' + response.message);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra khi xóa admin.');
            },
            complete: function() {
                // Reset button
                $('#confirm-delete').prop('disabled', false).text('Xóa');
                try {
                    $('#deleteModal').modal('hide');
                } catch (e) {
                    // Ignore modal hide error
                }
            }
        });
    }
});
</script>

