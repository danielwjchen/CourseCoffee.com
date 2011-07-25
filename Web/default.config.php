<?php
/**
 * @file
 * Template configuration file
 *
 * This file should be edited with installation specific values
 */

$config = new Config(array(
	'db' => array(
		/**
		 * Define a database connection for system database
		 */
		'sys' => array(
			'name' => 'example_sys',
			'driver' => 'mysql',
			'host' => 'localhost',
			'user' => 'username',
			'password' => 'password',
			'port' => '3306',
		),
		/**
		 * Define a database connection for core database
		 */
		'core' => array(
			'name' => 'exmple_core',
			'driver' => 'mysql',
			'host' => 'localhost',
			'user' => 'username',
			'password' => 'password',
			'port' => '3306',
		),
	),
	/**
	 * Define the Facebook app
	 */
	'facebooky' => array(
		'app_key' => '',
		'app_secret' => '',
	),
));
