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
require_once INCLUDES_PATH . '/SchemaInvoker.php';
SchemaInvoker::Init();
$dbas = File::Scan(SCHEMA_PATH, '/^[a-zA-Z]+Schema\.php$/');
foreach ($dbas as $path => $dba) {
	try {
		SchemaInvoker::Request($dba->name, $dba->uri);
	} catch (Exception $e) {
		echo $e->Message();
	}
}

/**
 * Rebuild paths for autoloading
 */
Autoloader::Build();

/**
 * Auto-load necessary files
 */
function __autoload($classname) {
	Autoloader::Resolve($classname);
}

/**
 * Rebuild URI maps for routing
 */
Router::Build();

/**
 * Update setting records in Database
 */
require_once INCLUDES_PATH . '/modules/Setting.php';
Setting::Populate();
