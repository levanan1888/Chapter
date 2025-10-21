<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
	<div class="container">
		<a class="navbar-brand" href="<?php echo Uri::create('/'); ?>">
			<i class="fas fa-book-open me-2"></i>Comic Reader
		</a>
		
		<button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
			<span class="navbar-toggler-icon"></span>
		</button>
		
		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav me-auto">
				<li class="nav-item">
					<a class="nav-link" href="<?php echo Uri::create('/'); ?>">
						<i class="fas fa-home me-1"></i>Trang chủ
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo Uri::create('stories'); ?>">
						<i class="fas fa-list me-1"></i>Tất cả truyện
					</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown">
						<i class="fas fa-tags me-1"></i>Thể loại
					</a>
					<ul class="dropdown-menu">
						<?php if (isset($categories) && !empty($categories)): ?>
							<?php foreach ($categories as $category): ?>
								<li><a class="dropdown-item" href="<?php echo Uri::create('stories?category=' . $category->id); ?>"><?php echo htmlspecialchars($category->name); ?></a></li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
				</li>
			</ul>
			
			<div class="d-flex align-items-center">
				<div class="search-box me-3">
					<form class="d-flex" id="searchForm">
						<input class="form-control" type="search" id="searchInput" placeholder="Tìm truyện..." autocomplete="off" style="border-radius: 25px 0 0 25px; border-right: none;">
						<button class="btn btn-primary" type="submit" style="border-radius: 0 25px 25px 0;">
							<i class="fas fa-search"></i>
						</button>
					</form>
					<div class="search-results" id="searchResults"></div>
				</div>
				
				<button class="theme-toggle" id="themeToggle" title="Chuyển đổi chế độ sáng/tối">
					<i class="fas fa-moon"></i>
				</button>
			</div>
		</div>
	</div>
</nav>
