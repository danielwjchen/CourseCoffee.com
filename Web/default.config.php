<?php
/**
 * @file
 * Template configuration file
 *
 * This file should be edited with installation specific values
 */

$config = new Config(array(
	/**
	 * Define default web admin
	 */
	'admin' => array(
		'email' => 'admin@example.com',
		'password' => 'example',
	),
	'domain' => 'eample.com',
	/**
	 * Define a database connection for system database
	 */
	'db' => array(
		'name' => 'example',
		'driver' => 'mysql',
		'host' => 'localhost',
		'user' => 'username',
		'password' => 'password',
		'port' => '3306',
	),
	/**
	 * Define the Facebook app
	 */
	'facebook' => array(
		'id'  => '',
		'key' => '',
		'secret' => '',
	),
));
