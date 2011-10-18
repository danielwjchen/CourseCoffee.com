<?php

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config.php';

/**
 * Auto-load necessary files
 */
function __autoload($classname) {
	Autoloader::Resolve($classname);
}

Session::Init();

Router::Dispatch();
