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
 *  Global database settings
 * -----------------------------------------------------------------------------
 *
 *  Set database configurations here to override environment specific
 *  configurations
 *
 */

// fuel/app/config/db.php
return array(
    'development' => array(
        'type'    => 'mysqli',
        'connection' => array(
            'hostname'   => 'localhost',
            'database'   => 'project_story',
            'username'   => 'root',
            'password'   => '',
        ),
    ),
);
