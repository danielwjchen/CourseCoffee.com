<?php
/**
 * @file
 * Define the configuration for the system
 */

$config = new Config(array(
	'db' => array(
		'sys' => array(
			'name' => 'plnnr_sys',
			'driver' => 'mysql',
			'host' => 'localhost',
			'user' => 'root',
			'password' => 'bTaEaFaTl2aBl',
			'port' => '3306',
		),
		'core' => array(
			'name' => 'plnnr_core',
			'driver' => 'mysql',
			'host' => 'localhost',
			'user' => 'root',
			'password' => 'bTaEaFaTl2aBl',
			'port' => '3306',
		),
	),
));
