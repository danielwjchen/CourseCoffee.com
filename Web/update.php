<?php
/**
 * @file 
 * Handle system updates
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config.php';


/**
 * Rebuild table schemas
 *
 * This is not 100% reliable.... It checks with the existing DAB schema entries 
 * but not the actual table schemas.
 */
require_once INCLUDES_PATH . '/DBAInvoker.php';
DBAInvoker::Init($config->db);
$dbas = File::ScanDirectory(DBA_PATH, '/DBA\.php$/');
foreach ($dbas as $path => $dba) {
	try {
		DBAInvoker::Request($dba->name, $dba->uri);
	} catch (Exception $e) {
		echo $e->Message();
		}
}

/**
 * Rebuild paths for autoloading
 */
Autoload::Init($config->db);
Autoload::Build();

/**
 * Rebuild URI maps for routing
 */
Router::Init($config->db);
Router::Build();
