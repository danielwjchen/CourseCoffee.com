<?php
/**
 * @file
 * Handle system installation
 *
 * This script should be run everytime a new system is created
 */
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config.php';

require_once DBA_PATH . '/sys/SystemDBA.php';
DBAInvoker::Init($config->db);
DBAInvoker::Create(SystemDBA::schema(), 'sys');

$core_dbas = File::ScanDirectory(DBA_PATH . 'core/', '/DBA\.php$/');
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
Autoload::Init($config->db['sys']);
Autoload::Build();
