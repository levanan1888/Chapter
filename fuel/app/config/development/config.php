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

/**
 * -----------------------------------------------------------------------------
 *  Configuration settings for development environment
 * -----------------------------------------------------------------------------
 *
 *  These settings get merged with the global settings.
 *
 */

return array(
	/**
	 * -------------------------------------------------------------------------
	 *  Logging threshold.
	 * -------------------------------------------------------------------------
	 *
	 *  Can be set to any of the following:
	 *
	 *      Fuel::L_NONE
	 *      Fuel::L_ERROR
	 *      Fuel::L_WARNING
	 *      Fuel::L_DEBUG
	 *      Fuel::L_INFO
	 *      Fuel::L_ALL
	 *
	 */
	'log_threshold' => Fuel::L_ERROR,

	/**
	 * -------------------------------------------------------------------------
	 *  Error handling
	 * -------------------------------------------------------------------------
	 */
	'errors' => array(
		/**
		 * ---------------------------------------------------------------------
		 *  Which errors should we show, but continue execution? You can add
		 *  the following:
		 *
		 *      E_NOTICE, E_WARNING, E_DEPRECATED, E_STRICT
		 *
		 *  to mimic PHP's default behaviour (which is to continue
		 *  on non-fatal errors). We consider this bad practice.
		 * ---------------------------------------------------------------------
		 */
		'continue_on' => array(),

		/**
		 * ---------------------------------------------------------------------
		 *  How many errors should we show before we stop showing them?
		 *
		 *  Note: This is useful to prevents out-of-memory errors.
		 * ---------------------------------------------------------------------
		 */
		'throttle' => 10,

		/**
		 * ---------------------------------------------------------------------
		 *  Should notices from Error::notice() be shown?
		 * ---------------------------------------------------------------------
		 */
		'notices' => false,

		/**
		 * ---------------------------------------------------------------------
		 *  Render previous contents or show it as HTML?
		 * ---------------------------------------------------------------------
		 */
		'render_prior' => false,
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Profiling
	 * -------------------------------------------------------------------------
	 */
	'profiling' => false,

	/**
	 * -------------------------------------------------------------------------
	 *  Debug mode
	 * -------------------------------------------------------------------------
	 */
	'debug' => false,

	/**
	 * -------------------------------------------------------------------------
	 *  Error reporting
	 * -------------------------------------------------------------------------
	 */
	'error_reporting' => 0,

	/**
	 * -------------------------------------------------------------------------
	 *  Display errors
	 * -------------------------------------------------------------------------
	 */
	'display_errors' => false,
);
