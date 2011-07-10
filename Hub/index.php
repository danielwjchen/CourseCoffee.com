<?php

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config.php';

RouterInvoker::Init();
Factory::Init($config->db['core']);

$params = explode('/', $_GET['q']);

$controller = Factory::Controller(RouterInvoker::Dispatch($params[0]));
$action = $params[1];
if (isset($params[2])) {
	$params = $params[2];

}
call_user_func(array($controller, 'beforeAction'), $params);
call_user_func(array($controller, $action), $params);
call_user_func(array($controller, 'afterAction'), $params);
