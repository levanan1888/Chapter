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
	
	<div class="col-lg-3 col-md-6 mb-4">
		<div class="card bg-secondary text-white">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="card-title"><?php echo isset($total_views) ? number_format($total_views) : 0; ?></h4>
						<p class="card-text">Tổng lượt xem</p>
					</div>
					<div class="align-self-center">
						<i class="fas fa-eye fa-2x"></i>
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
						<h4 class="card-title"><?php echo isset($active_stories) ? $active_stories : 0; ?></h4>
						<p class="card-text">Truyện đang hoạt động</p>
					</div>
					<div class="align-self-center">
						<i class="fas fa-play-circle fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-lg-3 col-md-6 mb-4">
		<div class="card bg-danger text-white">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="card-title"><?php echo isset($featured_stories) ? $featured_stories : 0; ?></h4>
						<p class="card-text">Truyện nổi bật</p>
					</div>
					<div class="align-self-center">
						<i class="fas fa-star fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<!-- Story Status Pie Chart -->
	<div class="col-lg-4 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0">
					<i class="fas fa-chart-pie me-2"></i>Trạng thái truyện
				</h5>
			</div>
			<div class="card-body">
				<canvas id="storyStatusChart" height="200"></canvas>
			</div>
		</div>
	</div>
	
	<!-- Stories by Category Bar Chart -->
	<div class="col-lg-8 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0">
					<i class="fas fa-chart-bar me-2"></i>Truyện theo danh mục
				</h5>
			</div>
			<div class="card-body">
				<canvas id="storiesByCategoryChart" height="200"></canvas>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<!-- Views Trend Area Chart -->
	<div class="col-lg-6 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0">
					<i class="fas fa-chart-area me-2"></i>Xu hướng lượt xem
				</h5>
			</div>
			<div class="card-body">
				<canvas id="viewsTrendChart" height="200"></canvas>
			</div>
		</div>
	</div>
	
	<!-- Top Viewed Stories -->
	<div class="col-lg-6 mb-4">
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
	
	// Story Status Pie Chart
	const storyStatusCtx = document.getElementById('storyStatusChart').getContext('2d');
	const storyStatusChart = new Chart(storyStatusCtx, {
		type: 'doughnut',
		data: {
			labels: chartData.story_status_stats.map(item => 
				item.status === 'active' ? 'Đang hoạt động' : 'Không hoạt động'
			),
			datasets: [{
				data: chartData.story_status_stats.map(item => item.count),
				backgroundColor: [
					'rgba(40, 167, 69, 0.8)',
					'rgba(108, 117, 125, 0.8)'
				],
				borderColor: [
					'rgba(40, 167, 69, 1)',
					'rgba(108, 117, 125, 1)'
				],
				borderWidth: 2
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: {
					position: 'bottom'
				}
			}
		}
	});

	// Stories by Category Bar Chart
	const storiesByCategoryCtx = document.getElementById('storiesByCategoryChart').getContext('2d');
	const storiesByCategoryChart = new Chart(storiesByCategoryCtx, {
		type: 'bar',
		data: {
			labels: chartData.stories_by_category.map(item => 
				item.name.length > 15 ? item.name.substring(0, 15) + '...' : item.name
			),
			datasets: [{
				label: 'Số truyện',
				data: chartData.stories_by_category.map(item => item.story_count),
				backgroundColor: 'rgba(54, 162, 235, 0.8)',
				borderColor: 'rgba(54, 162, 235, 1)',
				borderWidth: 1
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: {
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

	// Views Trend Area Chart
	const viewsTrendCtx = document.getElementById('viewsTrendChart').getContext('2d');
	const viewsTrendChart = new Chart(viewsTrendCtx, {
		type: 'line',
		data: {
			labels: chartData.views_by_month.map(item => {
				const date = new Date(item.month + '-01');
				return date.toLocaleDateString('vi-VN', { month: 'short', year: 'numeric' });
			}),
			datasets: [{
				label: 'Lượt xem',
				data: chartData.views_by_month.map(item => item.total_views),
				borderColor: 'rgb(255, 99, 132)',
				backgroundColor: 'rgba(255, 99, 132, 0.2)',
				fill: true,
				tension: 0.4
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: {
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
