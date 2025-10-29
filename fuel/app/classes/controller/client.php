<?php

/**
 * Controller Client
 * 
 * Xử lý logic cho phía người dùng
 * Bao gồm: xem truyện, đọc truyện, tìm kiếm, lọc
 * 
 * @package    App
 * @subpackage Controller
 */
class Controller_Client extends Controller
{
	/**
	 * Trang chủ - hiển thị truyện mới nhất, hot, được xem nhiều
	 * 
	 * @return void
	 */
	public function action_index()
	{
		$data = array();
		
		// Lấy truyện mới nhất
		$data['latest_stories'] = Model_Story::get_latest_stories(12);
		
		// Lấy truyện hot
		$data['hot_stories'] = Model_Story::get_hot_stories(8);
		
		// Lấy truyện được xem nhiều nhất
		$data['most_viewed_stories'] = Model_Story::get_most_viewed_stories(8);
		
		// Lấy danh sách categories
		$data['categories'] = Model_Category::get_all_categories();

		$data['title'] = 'Trang chủ - Đọc truyện tranh online';
		$data['content'] = View::forge('client/content/index', $data, false);
		return View::forge('layouts/client', $data);
	}

	/**
	 * Danh sách truyện với phân trang và lọc
	 * 
	 * @return void
	 */
	public function action_stories()
	{
		$data = array();
		
		// Lấy tham số lọc
		$category_id = Input::get('category', null);
		$author_id = Input::get('author', null);
		$status = Input::get('status', null);
		$sort = Input::get('sort', 'latest'); // latest, popular, view
		
		// Phân trang
		$page = Input::get('page', 1);
		$limit = 20;
		$offset = ($page - 1) * $limit;

		// Lấy danh sách truyện theo điều kiện lọc
		if ($category_id) {
			$data['stories'] = Model_Story::get_stories_by_category($category_id, $limit, $offset);
			$data['total_stories'] = Model_Category::find($category_id)->get_story_count();
		} elseif ($author_id) {
			$data['stories'] = Model_Story::get_stories_by_author($author_id, $limit, $offset);
			$data['total_stories'] = Model_Author::find($author_id)->get_story_count();
		} else {
			// Sắp xếp theo yêu cầu
			$order_by = 'created_at';
			$order_direction = 'DESC';
			
			switch ($sort) {
				case 'popular':
					$order_by = 'like_count';
					$order_direction = 'DESC';
					break;
				case 'view':
					$order_by = 'view_count';
					$order_direction = 'DESC';
					break;
				default:
					$order_by = 'created_at';
					$order_direction = 'DESC';
					break;
			}
			
			$data['stories'] = Model_Story::get_all_stories($limit, $offset, $order_by, $order_direction, $status);
			$data['total_stories'] = Model_Story::count_all();
		}

		$data['current_page'] = $page;
		$data['total_pages'] = ceil($data['total_stories'] / $limit);
		
		// Lấy danh sách categories và authors cho filter
		$data['categories'] = Model_Category::get_all_categories();
		$data['authors'] = Model_Author::get_all_authors();
		
		// Lưu tham số filter
		$data['filter_params'] = array(
			'category' => $category_id,
			'author' => $author_id,
			'status' => $status,
			'sort' => $sort,
		);

		$data['title'] = 'Danh sách Truyện';
		$data['content'] = View::forge('client/content/stories', $data, false);
		return View::forge('layouts/client', $data);
	}

	/**
	 * Tìm kiếm truyện
	 * 
	 * @return void
	 */
	public function action_search()
	{
		$data = array();
		
		$keyword = Input::get('q', '');
		$data['keyword'] = $keyword;
		
		if (!empty($keyword)) {
			// Phân trang
			$page = Input::get('page', 1);
			$limit = 20;
			$offset = ($page - 1) * $limit;

			// Tìm kiếm truyện
			$data['stories'] = Model_Story::search_stories($keyword, $limit, $offset);
			$data['total_stories'] = count(Model_Story::search_stories($keyword)); // Đếm tổng số kết quả
			$data['current_page'] = $page;
			$data['total_pages'] = ceil($data['total_stories'] / $limit);
		} else {
			$data['stories'] = array();
			$data['total_stories'] = 0;
			$data['current_page'] = 1;
			$data['total_pages'] = 0;
		}

		$data['title'] = 'Tìm kiếm: ' . $keyword;
		$data['content'] = View::forge('client/content/search', $data, false);
		return View::forge('layouts/client', $data);
	}

	/**
	 * Chi tiết truyện
	 * 
	 * @param string $slug Slug của truyện
	 * @return void
	 */
	public function action_story($slug = null)
	{
		if (empty($slug)) {
			Response::redirect('client/index');
		}

		$story = Model_Story::find_by_slug($slug);
		if (!$story) {
			Response::redirect('client/index');
		}

		// Kiểm tra truyện có đang hiển thị không
		if (!$story->is_visible()) {
			Response::redirect('client/index');
		}

		$data = array();
		$data['story'] = $story;
		
		// Tăng view count
		$story->increment_view_count();
		
		// Lấy thông tin author
		$data['author'] = $story->get_author();
		
		// Lấy danh sách categories
		$data['categories'] = $story->get_categories();
		
		// Lấy danh sách chapters
		$data['chapters'] = $story->get_chapters();
		
		// Lấy truyện tương tự (cùng categories)
		$data['related_stories'] = array();
		if (!empty($data['categories'])) {
			$category_ids = array();
			foreach ($data['categories'] as $category) {
				$category_ids[] = $category->id;
			}
			
			// Lấy truyện từ categories tương tự (trừ truyện hiện tại)
			$related_stories = array();
			foreach ($category_ids as $category_id) {
				$stories_in_category = Model_Story::get_stories_by_category($category_id, 5);
				foreach ($stories_in_category as $related_story) {
					if ($related_story->id != $story->id && !in_array($related_story->id, array_column($related_stories, 'id'))) {
						$related_stories[] = $related_story;
					}
				}
			}
			$data['related_stories'] = array_slice($related_stories, 0, 6);
		}

		$data['title'] = $story->title . ' - Đọc truyện tranh online';
		$data['content'] = View::forge('client/content/story', $data, false);
		return View::forge('layouts/client', $data);
	}

	/**
	 * Đọc truyện (hiển thị chapter)
	 * 
	 * @param string $story_slug Slug của truyện
	 * @param int $chapter_number Số chương
	 * @return void
	 */
	public function action_read($story_slug = null, $chapter_number = null)
	{
		if (empty($story_slug) || empty($chapter_number)) {
			Response::redirect('client/index');
		}

		$story = Model_Story::find_by_slug($story_slug);
		if (!$story) {
			Response::redirect('client/index');
		}

		// Kiểm tra truyện có đang hiển thị không
		if (!$story->is_visible()) {
			Response::redirect('client/index');
		}

		$chapter = Model_Chapter::find_by_story_and_number($story->id, $chapter_number);
		if (!$chapter) {
			\Log::error('Chapter not found: story_id=' . $story->id . ', chapter_number=' . $chapter_number);
			Response::redirect('client/story/' . $story_slug);
		}

		$data = array();
		$data['story'] = $story;
		$data['chapter'] = $chapter;
		
		// Tăng view count cho chapter
		$chapter->increment_view_count();
		
		// Lấy chapter trước và sau
		$data['previous_chapter'] = $chapter->get_previous_chapter();
		$data['next_chapter'] = $chapter->get_next_chapter();
		
		// Lấy danh sách tất cả chapters của truyện
		$data['all_chapters'] = $story->get_chapters();
		
		// Lấy truyện tương tự
		$categories = $story->get_categories();
		$data['related_stories'] = array();
		if (!empty($categories)) {
			$category_ids = array();
			foreach ($categories as $category) {
				$category_ids[] = $category->id;
			}
			
			$related_stories = array();
			foreach ($category_ids as $category_id) {
				$stories_in_category = Model_Story::get_stories_by_category($category_id, 5);
				foreach ($stories_in_category as $related_story) {
					if ($related_story->id != $story->id && !in_array($related_story->id, array_column($related_stories, 'id'))) {
						$related_stories[] = $related_story;
					}
				}
			}
			$data['related_stories'] = array_slice($related_stories, 0, 6);
		}

		$data['title'] = $story->title . ' - Chương ' . $chapter_number . ' - ' . $chapter->title;
		$data['content'] = View::forge('client/content/read', $data, false);
		return View::forge('layouts/client', $data);
	}

	/**
	 * Danh sách truyện theo category
	 * 
	 * @param string $slug Slug của category
	 * @return void
	 */
	public function action_category($slug = null)
	{
		if (empty($slug)) {
			Response::redirect('client/stories');
		}

		$category = Model_Category::find_by_slug($slug);
		if (!$category) {
			Response::redirect('client/stories');
		}

		$data = array();
		$data['category'] = $category;
		
		// Phân trang
		$page = Input::get('page', 1);
		$limit = 20;
		$offset = ($page - 1) * $limit;

		// Lấy danh sách truyện trong category
		$data['stories'] = Model_Story::get_stories_by_category($category->id, $limit, $offset);
		$data['total_stories'] = $category->get_story_count();
		$data['current_page'] = $page;
		$data['total_pages'] = ceil($data['total_stories'] / $limit);

		$data['title'] = 'Danh mục: ' . $category->name;
		$data['content'] = View::forge('client/content/category', $data, false);
		return View::forge('layouts/client', $data);
	}

	/**
	 * Danh sách truyện theo author
	 * 
	 * @param string $slug Slug của author
	 * @return void
	 */
	public function action_author($slug = null)
	{
		if (empty($slug)) {
			Response::redirect('client/stories');
		}

		$author = Model_Author::find_by_slug($slug);
		if (!$author) {
			Response::redirect('client/stories');
		}

		$data = array();
		$data['author'] = $author;
		
		// Phân trang
		$page = Input::get('page', 1);
		$limit = 20;
		$offset = ($page - 1) * $limit;

		// Lấy danh sách truyện của author
		$data['stories'] = Model_Story::get_stories_by_author($author->id, $limit, $offset);
		$data['total_stories'] = $author->get_story_count();
		$data['current_page'] = $page;
		$data['total_pages'] = ceil($data['total_stories'] / $limit);

		$data['title'] = 'Tác giả: ' . $author->name;
		$data['content'] = View::forge('client/content/author', $data, false);
		return View::forge('layouts/client', $data);
	}

	/**
	 * API endpoint để lấy danh sách truyện (cho AJAX)
	 * 
	 * @return Response
	 */
	public function action_api_stories()
	{
		$page = Input::get('page', 1);
		$limit = Input::get('limit', 20);
		$category_id = Input::get('category', null);
		$author_id = Input::get('author', null);
		$sort = Input::get('sort', 'latest');
		
		$offset = ($page - 1) * $limit;

		// Lấy danh sách truyện theo điều kiện
		if ($category_id) {
			$stories = Model_Story::get_stories_by_category($category_id, $limit, $offset);
			$total = Model_Category::find($category_id)->get_story_count();
		} elseif ($author_id) {
			$stories = Model_Story::get_stories_by_author($author_id, $limit, $offset);
			$total = Model_Author::find($author_id)->get_story_count();
		} else {
			$order_by = 'created_at';
			$order_direction = 'DESC';
			
			switch ($sort) {
				case 'popular':
					$order_by = 'like_count';
					$order_direction = 'DESC';
					break;
				case 'view':
					$order_by = 'view_count';
					$order_direction = 'DESC';
					break;
			}
			
			$stories = Model_Story::get_all_stories($limit, $offset, $order_by, $order_direction);
			$total = Model_Story::count_all();
		}

		$response = array(
			'success' => true,
			'data' => $stories,
			'pagination' => array(
				'current_page' => $page,
				'total_pages' => ceil($total / $limit),
				'total_items' => $total,
				'items_per_page' => $limit
			)
		);

		$response_obj = Response::forge(json_encode($response));
		$response_obj->set_header('Content-Type', 'application/json');
		return $response_obj;
	}

	/**
	 * API endpoint để tìm kiếm truyện (cho AJAX)
	 * 
	 * @return Response
	 */
	public function action_api_search()
	{
		$keyword = Input::get('q', '');
		$page = Input::get('page', 1);
		$limit = Input::get('limit', 20);
		
		$offset = ($page - 1) * $limit;

		if (!empty($keyword)) {
			$stories = Model_Story::search_stories($keyword, $limit, $offset);
			$total = count(Model_Story::search_stories($keyword));
		} else {
			$stories = array();
			$total = 0;
		}

		$response = array(
			'success' => true,
			'data' => $stories,
			'keyword' => $keyword,
			'pagination' => array(
				'current_page' => $page,
				'total_pages' => ceil($total / $limit),
				'total_items' => $total,
				'items_per_page' => $limit
			)
		);

		$response_obj = Response::forge(json_encode($response));
		$response_obj->set_header('Content-Type', 'application/json');
		return $response_obj;
	}
}
