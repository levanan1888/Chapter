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
	
	<!-- Top Viewed Stories -->
	<div class="col-lg-4 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0">
					<i class="fas fa-fire me-2"></i>Truyện hot nhất
				</h5>
			</div>
			<div class="card-body">
				<?php if (isset($chart_data['top_viewed_stories']) && !empty($chart_data['top_viewed_stories'])): ?>
					<?php foreach ($chart_data['top_viewed_stories'] as $index => $story): ?>
					<div class="d-flex justify-content-between align-items-center mb-2">
						<div class="flex-grow-1">
							<span class="badge bg-primary me-2"><?php echo $index + 1; ?></span>
							<span class="text-truncate" style="max-width: 150px;" title="<?php echo Security::htmlentities($story['title']); ?>">
								<?php echo Security::htmlentities($story['title']); ?>
							</span>
						</div>
						<small class="text-muted"><?php echo number_format($story['views']); ?></small>
					</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="text-center text-muted py-3">
						<i class="fas fa-chart-line fa-2x mb-2"></i>
						<p class="mb-0">Chưa có dữ liệu</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<!-- Charts Section -->
<div class="row mt-4">
	<!-- Stories and Chapters Trend Chart -->
	<div class="col-12 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0">
					<i class="fas fa-chart-line me-2"></i>Xu hướng truyện và chương (7 tháng gần nhất)
				</h5>
			</div>
			<div class="card-body">
				<canvas id="trendChart" height="100"></canvas>
			</div>
		</div>
	</div>
	
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Chart data from PHP
	const chartData = <?php echo json_encode($chart_data); ?>;
	
	// Trend Chart (Stories and Chapters by Month)
	const trendCtx = document.getElementById('trendChart').getContext('2d');
	const trendChart = new Chart(trendCtx, {
		type: 'line',
		data: {
			labels: chartData.stories_by_month.map(item => {
				const date = new Date(item.month + '-01');
				return date.toLocaleDateString('vi-VN', { month: 'short', year: 'numeric' });
			}),
			datasets: [{
				label: 'Truyện mới',
				data: chartData.stories_by_month.map(item => item.count),
				borderColor: 'rgb(75, 192, 192)',
				backgroundColor: 'rgba(75, 192, 192, 0.2)',
				tension: 0.1
			}, {
				label: 'Chương mới',
				data: chartData.chapters_by_month.map(item => item.count),
				borderColor: 'rgb(255, 99, 132)',
				backgroundColor: 'rgba(255, 99, 132, 0.2)',
				tension: 0.1
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: {
					position: 'top',
				},
				title: {
					display: false
				}
			},
			scales: {
				y: {
					beginAtZero: true
				}
			}
		}
	});
	
});
</script>
