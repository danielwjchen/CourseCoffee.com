<?php

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config.php';

/**
 * Auto-load necessary files
 */
Session::Init();
function __autoload($classname) {
	Autoloader::Resolve($classname);
}

$uri    = '';
$params = array();
if (isset($_GET['q'])) {
	$request = explode('/', $_GET['q']);
	$uri = array_shift($request);
	$params = $request;

	// debug
	// error_log('uri - ' .$uri);
	// error_log('params - ' . print_r($params, true));

} else {
	$uri = 'welcome';
}

Router::Dispatch($uri, $params);
