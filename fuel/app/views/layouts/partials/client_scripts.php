<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
	// Theme toggle functionality
	const themeToggle = $('#themeToggle');
	const body = $('body');
	
	// Load saved theme
	const savedTheme = localStorage.getItem('theme');
	if (savedTheme === 'dark') {
		body.addClass('dark-mode');
		themeToggle.find('i').removeClass('fa-moon').addClass('fa-sun');
	}
	
	// Theme toggle click handler
	themeToggle.on('click', function() {
		body.toggleClass('dark-mode');
		
		if (body.hasClass('dark-mode')) {
			localStorage.setItem('theme', 'dark');
			themeToggle.find('i').removeClass('fa-moon').addClass('fa-sun');
		} else {
			localStorage.setItem('theme', 'light');
			themeToggle.find('i').removeClass('fa-sun').addClass('fa-moon');
		}
	});
	
	// Search functionality
	const searchInput = $('#searchInput');
	const searchResults = $('#searchResults');
	const searchForm = $('#searchForm');
	
	let searchTimeout;
	
	// Search input handler
	searchInput.on('input', function() {
		const query = $(this).val().trim();
		
		clearTimeout(searchTimeout);
		
		if (query.length < 2) {
			searchResults.hide();
			return;
		}
		
		searchTimeout = setTimeout(function() {
			performSearch(query);
		}, 300);
	});
	
	// Search form submit handler
	searchForm.on('submit', function(e) {
		e.preventDefault();
		const query = searchInput.val().trim();
		
		if (query.length >= 2) {
			window.location.href = '<?php echo Uri::create("stories"); ?>?search=' + encodeURIComponent(query);
		}
	});
	
	// Hide search results when clicking outside
	$(document).on('click', function(e) {
		if (!$(e.target).closest('.search-box').length) {
			searchResults.hide();
		}
	});
	
	// Search results click handler
	$(document).on('click', '.search-item', function() {
		const storyId = $(this).data('id');
		window.location.href = '<?php echo Uri::create("story"); ?>/' + storyId;
	});
	
	// Perform search function
	function performSearch(query) {
		$.ajax({
			url: '<?php echo Uri::create("api/search"); ?>',
			method: 'GET',
			data: { q: query },
			dataType: 'json',
			success: function(response) {
				if (response.success && response.data.length > 0) {
					displaySearchResults(response.data);
				} else {
					searchResults.html('<div class="search-item"><em>Không tìm thấy truyện nào</em></div>').show();
				}
			},
			error: function() {
				searchResults.html('<div class="search-item"><em>Lỗi tìm kiếm</em></div>').show();
			}
		});
	}
	
	// Display search results
	function displaySearchResults(stories) {
		let html = '';
		
		stories.forEach(function(story) {
			html += '<div class="search-item" data-id="' + story.id + '">';
			html += '<div class="search-item-title">' + story.title + '</div>';
			if (story.author_name) {
				html += '<div class="search-item-author">' + story.author_name + '</div>';
			}
			html += '</div>';
		});
		
		searchResults.html(html).show();
	}
});
</script>
