<?php
/**
 * @file
 * Define the configuration for the system
 */

$config = new Config(array(
	'db' => array(
		'system' => array(
			'name' => 'plnnr_hub_system',
			'driver' => 'mysql',
			'host' => 'localhost',
			'user' => 'root',
			'password' => 'bTaEaFaTl2aBl',
			'port' => '3306',
		),
		'core' => array(
			'name' => 'plnnr_hub_core',
			'driver' => 'mysql',
			'host' => 'localhost',
			'user' => 'root',
			'password' => 'bTaEaFaTl2aBl',
			'port' => '3306',
		),
	),
));
