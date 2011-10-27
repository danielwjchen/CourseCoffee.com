<?php
/**
 * @file 
 * Run scheduled jobs periotically by cron
 */

require_once __DIR__ . '/includes/bootstrap.php';
// require_once INCLUDES_PATH . '/Scheduler.php';
require_once __DIR__ . '/config.php';

function __autoload($classname) {
	Autoloader::Resolve($classname);
}


$db_cache = new DBCache();
$db_cache->expire();

foreach($config->db['institution'] as $db_name => $db_config) {
	$book_queue = new ItemQueueModel($db_name);
	$book_queue->processItemQueue();
}
