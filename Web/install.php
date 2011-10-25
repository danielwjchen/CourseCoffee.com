<?php
/**
 * @file
 * Handle system installation
 *
 * This script should be run everytime a new system is created
 */
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config.php';

/**
 * Populate database with tables
 */
require_once INCLUDES_PATH . '/SchemaInvoker.php';
require_once Schema_PATH . '/SystemSchema.php';
SchemaInvoker::Init();
SchemaInvoker::Create(array('default'), SystemSchema::schema());
$core_dbas = File::ScanDirectory(Schema_PATH, '/[^System]Schema\.php$/');
foreach ($core_dbas as $path => $dba) {
	try {
		SchemaInvoker::Request($dba->name, $dba->uri);
	} catch (Exception $e) {
		echo $e->Message();
	}
}

/**
 * Build paths for autoloading
 */
Autoloader::Init($config->db['default']);
Autoloader::Build();

/**
 * Build URI maps for routing
 */
Router::Init($config->db['default']);
Router::Build();

/**
 * Generate salt for encryptions
 */
Crypto::Init($config->db['default']);
Crypto::Build();

/**
 * Update setting records in Database
 */
require_once SETTING_PATH . '/Setting.php';
Setting::Populate();
