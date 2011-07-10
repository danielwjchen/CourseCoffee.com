<?php

define('INCLUDES_PATH', __DIR__ . '/');
define('ROOT_PATH', str_replace('includes/', '', INCLUDES_PATH));
define('DBA_PATH', ROOT_PATH . 'dba/');
define('DAO_PATH', ROOT_PATH . 'dao/');
define('CONTROLLER_PATH', ROOT_PATH . 'controller/');
define('MODEL_PATH', ROOT_PATH . 'model/');
define('VIEW_PATH', ROOT_PATH . 'view/');
define('LIB_PATH', ROOT_PATH . 'lib/');
define('ROUTER_PATH', ROOT_PATH . 'router/');

require_once INCLUDES_PATH . '/config.php';
require_once INCLUDES_PATH . '/session.php';
require_once INCLUDES_PATH . '/factory.php';
require_once INCLUDES_PATH . '/DB.php';
require_once INCLUDES_PATH . '/DBAInvoker.php';
require_once INCLUDES_PATH . '/file.php';
require_once INCLUDES_PATH . '/router.php';
