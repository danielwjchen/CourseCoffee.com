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
require_once INCLUDES_PATH . '/DBAInvoker.php';
require_once DBA_PATH . '/SystemDBA.php';
DBAInvoker::Init($config->db);
DBAInvoker::Create(SystemDBA::schema());
$core_dbas = File::ScanDirectory(DBA_PATH, '/[^System]DBA\.php$/');
foreach ($core_dbas as $path => $dba) {
	try {
		DBAInvoker::Request($dba->name, $dba->uri);
	} catch (Exception $e) {
		echo $e->Message();
	}
}

/**
 * Build paths for autoloading
 */
Autoload::Init($config->db);
Autoload::Build();

/**
 * Build URI maps for routing
 */
Router::Init($config->db);
Router::Build();

/**
 * Generate salt for encryptions
 */
Crypto::Init($config->db);
Crypto::Build();
