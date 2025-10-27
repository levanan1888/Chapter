<?php
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.9-dev
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2019 Fuel Development Team
 * @link       https://fuelphp.com
 */

return array(
	/**
	 * -------------------------------------------------------------------------
	 *  Default route
	 * -------------------------------------------------------------------------
	 *
	 */

	'_root_' => 'client/index',

	/**
	 * -------------------------------------------------------------------------
	 *  Page not found
	 * -------------------------------------------------------------------------
	 *
	 */

	'_404_' => 'welcome/404',

	/**
	 * -------------------------------------------------------------------------
	 *  Example for Presenter
	 * -------------------------------------------------------------------------
	 *
	 *  A route for showing page using Presenter
	 *
	 */

	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),

	/**
	 * -------------------------------------------------------------------------
	 *  Admin routes
	 * -------------------------------------------------------------------------
	 *
	 *  Routes cho hệ thống admin
	 *
	 */
	// Admin authentication routes
	'admin' => 'admin/dashboard/index',
	'admin/login' => 'admin/auth/login',
	'admin/register' => 'admin/auth/register',
	'admin/logout' => 'admin/auth/logout',
	
	// Admin dashboard routes
	'admin/dashboard' => 'admin/dashboard/index',
	'admin/dashboard/stats' => 'admin/dashboard/api_stats',
	'admin/dashboard/latest' => 'admin/dashboard/api_latest_stories',
	'admin/dashboard/hot' => 'admin/dashboard/api_hot_stories',
	'admin/dashboard/most-viewed' => 'admin/dashboard/api_most_viewed_stories',
	
	// Admin user management routes
	'admin/users' => 'admin/user/index',
	'admin/users/add' => 'admin/user/add',
	'admin/users/edit/(:num)' => 'admin/user/edit/$1',
	'admin/users/delete/(:num)' => 'admin/user/delete/$1',
	'admin/users/deleted' => 'admin/user/deleted',
	'admin/users/restore/(:num)' => 'admin/user/restore/$1',
	'admin/users/delete-permanent/(:num)' => 'admin/user/delete_permanent/$1',
	'admin/users/bulk-delete' => 'admin/user/bulk_delete',
	'admin/users/bulk-restore' => 'admin/user/bulk_restore',
	'admin/users/bulk-delete-permanent' => 'admin/user/bulk_delete_permanent',
	
	// Story management routes
	'admin/stories' => 'admin/story/index',
	'admin/stories/add' => 'admin/story/add',
	'admin/stories/edit/(:num)' => 'admin/story/edit/$1',
	'admin/stories/view/(:num)' => 'admin/story/view/$1',
	'admin/stories/delete/(:num)' => 'admin/story/delete/$1',
	'admin/stories/bulk-delete' => 'admin/story/bulk_delete',
	'admin/stories/trash' => 'admin/story/trash',
	'admin/stories/restore/(:num)' => 'admin/story/restore/$1',
	'admin/stories/force_delete/(:num)' => 'admin/story/force_delete/$1',
	'admin/stories/bulk-restore' => 'admin/story/bulk_restore',
	'admin/stories/bulk-force-delete' => 'admin/story/bulk_force_delete',
	'admin/stories/toggle_visibility' => 'admin/story/toggle_visibility',
	'admin/stories/api/list' => 'admin/story/api_list',
	'admin/stories/api/search' => 'admin/story/api_search',
	
	// Chapter management routes
	'admin/chapters/(:num)' => 'admin/chapter/index/$1',
	'admin/chapters/add/(:num)' => 'admin/chapter/add/$1',
	'admin/chapters/edit/(:num)' => 'admin/chapter/edit/$1',
	'admin/chapters/delete/(:num)' => 'admin/chapter/delete/$1',
	'admin/chapters/bulk-delete' => 'admin/chapter/bulk_delete',
	'admin/chapters/trash/(:num)' => 'admin/chapter/trash/$1',
	'admin/chapters/restore/(:num)' => 'admin/chapter/restore/$1',
	'admin/chapters/force-delete/(:num)' => 'admin/chapter/force_delete/$1',
	'admin/chapters/bulk-restore' => 'admin/chapter/bulk_restore',
	'admin/chapters/bulk-force-delete' => 'admin/chapter/bulk_force_delete',
	'admin/chapters/api/list/(:num)' => 'admin/chapter/api_list/$1',
	
	// Category management routes
	'admin/categories' => 'admin/category/index',
	'admin/categories/add' => 'admin/category/add',
	'admin/categories/edit/(:num)' => 'admin/category/edit/$1',
	'admin/categories/view/(:num)' => 'admin/category/view/$1',
	'admin/categories/delete/(:num)' => 'admin/category/delete/$1',
	'admin/categories/trash' => 'admin/category/trash',
	'admin/categories/restore/(:num)' => 'admin/category/restore/$1',
	'admin/categories/force_delete/(:num)' => 'admin/category/force_delete/$1',
	'admin/categories/bulk-delete' => 'admin/category/bulk_delete',
	'admin/categories/bulk-restore' => 'admin/category/bulk_restore',
	'admin/categories/bulk-force-delete' => 'admin/category/bulk_force_delete',
	'admin/categories/api/list' => 'admin/category/api_list',
	'admin/categories/api/create' => 'admin/category/api_create',
	'admin/categories/api/update-order' => 'admin/category/api_update_order',
	'admin/categories/toggle_status/(:num)' => 'admin/category/toggle_status/$1',
	
	// Author management routes
	'admin/authors' => 'admin/author/index',
	'admin/authors/add' => 'admin/author/add',
	'admin/authors/edit/(:num)' => 'admin/author/edit/$1',
	'admin/authors/delete/(:num)' => 'admin/author/delete/$1',
	'admin/authors/view/(:num)' => 'admin/author/view/$1',
	'admin/authors/trash' => 'admin/author/trash',
	'admin/authors/restore/(:num)' => 'admin/author/restore/$1',
	'admin/authors/force_delete/(:num)' => 'admin/author/force_delete/$1',
	'admin/authors/bulk-delete' => 'admin/author/bulk_delete',
	'admin/authors/bulk-restore' => 'admin/author/bulk_restore',
	'admin/authors/bulk-force-delete' => 'admin/author/bulk_force_delete',
	'admin/authors/api/list' => 'admin/author/api_list',
	'admin/authors/api/create' => 'admin/author/api_create',
	'admin/authors/api/search' => 'admin/author/api_search',
	
	/**
	 * -------------------------------------------------------------------------
	 *  Client routes
	 * -------------------------------------------------------------------------
	 *
	 *  Routes cho phía người dùng
	 *
	 */
	// Client routes
	'client' => 'client/index',
	'client/stories' => 'client/stories',
	'client/search' => 'client/search',
	'client/story/(:any)' => 'client/story/$1',
	'client/read/(:any)/(:num)' => 'client/read/$1/$2',
	'client/category/(:any)' => 'client/category/$1',
	'client/author/(:any)' => 'client/author/$1',
	
	// Client API routes
	'client/api/stories' => 'client/api_stories',
	'client/api/search' => 'client/api_search',
	
	// Comment routes
	'comment/add' => 'comment/add',
	'comment/get_comments' => 'comment/get_comments',
	'comment/delete/(:num)' => 'comment/delete/$1',
	
	// Admin comment routes
	'admin/comments' => 'admin/comment/index',
	'admin/comments/view/(:num)' => 'admin/comment/view/$1',
	'admin/comments/approve/(:num)' => 'admin/comment/approve/$1',
	'admin/comments/disapprove/(:num)' => 'admin/comment/disapprove/$1',
	'admin/comments/delete/(:num)' => 'admin/comment/delete/$1',
	'admin/comments/save_reply' => 'admin/comment/save_reply',
	
	/**
	 * -------------------------------------------------------------------------
	 *  User Authentication routes
	 * -------------------------------------------------------------------------
	 *
	 *  Routes cho đăng nhập, đăng ký người dùng
	 *
	 */
	// User authentication routes
	'user/login' => 'user/login',
	'user/register' => 'user/register',
	'user/logout' => 'user/logout',
	'user/profile' => 'user/profile',
	'user/google_login' => 'user/google_login',
	'user/google_callback' => 'user/google_callback',
	'user/check_status' => 'user/check_status',
	'user/forgot-password' => 'user/forgot_password',
	'user/verify-token' => 'user/verify_token',
	'user/reset-password' => 'user/reset_password',
);
