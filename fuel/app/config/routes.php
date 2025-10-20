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

	'_root_' => 'welcome/index',

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
	// admin routes
	'admin' => 'admin/index',
	'admin/login' => 'admin/login',
	'admin/register' => 'admin/register',
	'admin/google_login' => 'admin/google_login',
	'admin/google_callback' => 'admin/google_callback',
	'admin/dashboard' => 'admin/dashboard',
	'admin/logout' => 'admin/logout', 
	'admin/manage' => 'admin/manage',
	'admin/add' => 'admin/add',
	'admin/deleted' => 'admin/deleted',
	'admin/restore/(:num)' => 'admin/restore/$1',
	'admin/delete_permanent/(:num)' => 'admin/delete_permanent/$1',
	'admin/bulk_delete' => 'admin/bulk_delete',
	'admin/bulk_restore' => 'admin/bulk_restore',
	'admin/bulk_delete_permanent' => 'admin/bulk_delete_permanent',
	'admin/edit/(:num)' => 'admin/edit/$1',
	'admin/delete/(:num)' => 'admin/delete/$1',
);
