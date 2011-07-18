<?php

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config.php';

Factory::Init($config->db['core']);
Router::Init();

$params = explode('/', $_GET['q']);

RouterInvoker::Dispatch($uri[0], $uri[1]);
