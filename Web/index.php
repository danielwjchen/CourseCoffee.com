<?php

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config.php';

/**
 * Auto-load necessary files
 */
Session::Init();
Autoload::Init($config->db['sys']);
function __autoload($classname) {
	Autoload::Add($classname);
}

Router::Init($config->db['sys']);

$uri = isset($_GET['q']) ? $_GET['q'] : 'welcome';
Router::Dispatch($uri);

