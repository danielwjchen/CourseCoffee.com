<?php

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config.php';

/**
 * Auto-load necessary files
 */
function __autoload($classname) {
	Autoload::Init($config->db['sys']);
	Autoload::Add($classname);
}

Router::Init();

$uri = explode('/', $_GET['q']);

Router::Dispatch($uri);
