<?php
/**
 * @file 
 * Handle system updates
 */

require_once 'includes/bootstrap.php';

$dbas = File::ScanDirectory(DBA_PATH . 'core/', '/DBA\.php$/');
foreach ($dbas as $path => $dba) {
	
	try {
		DBAInvoker::Request($dba->name, $dba->uri);
	} catch (Exception $e) {
		print_r($dba);
		echo $e->Message();
	}

}
