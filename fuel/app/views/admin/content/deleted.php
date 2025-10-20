<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-800">Admin đã xóa</h1>
	<div>
		<a href="<?php echo Uri::create('admin/manage'); ?>" class="btn btn-secondary btn-sm shadow-sm">
			<i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại danh sách
		</a>
	</div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách đã xóa</h6>
        <div>
            <button id="bulk-restore" class="btn btn-success btn-sm mr-2"><i class="fas fa-undo"></i> Khôi phục đã chọn</button>
            <button id="bulk-delete-permanent" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Xóa vĩnh viễn</button>
        </div>
    </div>
	<div class="card-body">
		<?php if (!empty($admins)): ?>
            <div class="table-responsive">
                <form id="bulk-form-deleted">
                <table class="table table-bordered" width="100%" cellspacing="0">
					<thead>
						<tr>
                            <th><input type="checkbox" id="select-all-deleted"></th>
							<th>ID</th>
							<th>Username</th>
							<th>Email</th>
							<th>Họ tên</th>
							<th>Ngày xóa</th>
							<th>Thao tác</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($admins as $admin): ?>
						<tr>
                            <td><input type="checkbox" class="js-select-deleted" value="<?php echo $admin->id; ?>"></td>
							<td><?php echo $admin->id; ?></td>
							<td><?php echo htmlentities($admin->username); ?></td>
							<td><?php echo htmlentities($admin->email); ?></td>
							<td><?php echo htmlentities($admin->full_name ?: '-'); ?></td>
							<td><?php echo date('d/m/Y H:i', strtotime($admin->deleted_at)); ?></td>
							<td>
								<button type="button" class="btn btn-success btn-sm js-restore-admin" data-id="<?php echo $admin->id; ?>" data-username="<?php echo htmlentities($admin->username); ?>">
									<i class="fas fa-undo"></i> Khôi phục
								</button>
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
								<a class="page-link" href="<?php echo Uri::create('admin/deleted', array('page' => $current_page - 1)); ?>">
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
								<a class="page-link" href="<?php echo Uri::create('admin/deleted', array('page' => $current_page + 1)); ?>">
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
				<i class="fas fa-trash-restore fa-3x text-gray-300 mb-3"></i>
				<h4 class="text-gray-500">Không có admin nào trong thùng rác</h4>
				<a href="<?php echo Uri::create('admin/manage'); ?>" class="btn btn-primary mt-3">
					<i class="fas fa-users"></i> Về danh sách admin
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>

<script>
// Chờ jQuery load xong
function waitForJQuery(callback) {
	if (typeof jQuery !== 'undefined') {
		callback(jQuery);
	} else {
		setTimeout(function() { waitForJQuery(callback); }, 100);
	}
}

waitForJQuery(function($) {
    // Select all toggle
    $(document).on('change', '#select-all-deleted', function(){
        $('.js-select-deleted').prop('checked', $(this).is(':checked'));
    });

	$(document).on('click', '.js-restore-admin', function(e) {
		e.preventDefault();
		const id = $(this).data('id');
		const username = $(this).data('username');
		
		if (!confirm('Khôi phục admin "' + username + '"?')) {
			return;
		}
		
		const csrfToken = $('meta[name="csrf-token"]').attr('content');
		$(this).prop('disabled', true).text('Đang khôi phục...');
		
		$.ajax({
			url: '<?php echo Uri::create('admin/restore/'); ?>' + id,
			type: 'POST',
			dataType: 'json',
			data: { fuel_csrf_token: csrfToken },
			headers: { 'X-CSRF-TOKEN': csrfToken },
			success: function(response) {
				if (response.success) {
					alert(response.message);
					location.reload();
				} else {
					alert('Lỗi: ' + response.message);
				}
			},
			error: function() {
				alert('Có lỗi xảy ra khi khôi phục.');
			},
			complete: function() {
				$('.js-restore-admin').prop('disabled', false).text('Khôi phục');
			}
		});
	});
});
</script>

<script>
// Bulk actions on deleted list
waitForJQuery(function($) {
    function postBulk(url, ids, btn, workingText) {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(btn).prop('disabled', true).text(workingText);
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: { ids: ids, fuel_csrf_token: csrfToken },
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(resp){ alert(resp.message); location.reload(); },
            error: function(){ alert('Có lỗi xảy ra.'); },
            complete: function(){ $(btn).prop('disabled', false); }
        });
    }

    $(document).on('click', '#bulk-restore', function(e){
        e.preventDefault();
        const ids = $('.js-select-deleted:checked').map(function(){ return $(this).val(); }).get();
        if (ids.length === 0) { alert('Chọn ít nhất 1 admin.'); return; }
        if (!confirm('Khôi phục ' + ids.length + ' admin đã chọn?')) return;
        postBulk('<?php echo Uri::create('admin/bulk_restore'); ?>', ids, this, 'Đang khôi phục...');
    });

    $(document).on('click', '#bulk-delete-permanent', function(e){
        e.preventDefault();
        const ids = $('.js-select-deleted:checked').map(function(){ return $(this).val(); }).get();
        if (ids.length === 0) { alert('Chọn ít nhất 1 admin.'); return; }
        if (!confirm('Xóa vĩnh viễn ' + ids.length + ' admin đã chọn? Hành động không thể hoàn tác.')) return;
        postBulk('<?php echo Uri::create('admin/bulk_delete_permanent'); ?>', ids, this, 'Đang xóa...');
    });
});
</script>
