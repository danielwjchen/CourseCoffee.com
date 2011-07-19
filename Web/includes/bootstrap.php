<?php

define('INCLUDES_PATH', __DIR__);
define('ROOT_PATH', str_replace('includes', '', INCLUDES_PATH));
define('DBA_PATH', ROOT_PATH . '/dba');
define('DAO_PATH', ROOT_PATH . '/dao');
define('CONTROLLER_PATH', ROOT_PATH . '/controller');
define('MODEL_PATH', ROOT_PATH . '/model');
define('VIEW_PATH', ROOT_PATH . '/view');
define('LIB_PATH', ROOT_PATH . '/lib');

require_once INCLUDES_PATH . '/Autoload.php';
require_once INCLUDES_PATH . '/Config.php';
require_once INCLUDES_PATH . '/Session.php';
require_once INCLUDES_PATH . '/DB.php';
require_once INCLUDES_PATH . '/DBAInvoker.php';
require_once INCLUDES_PATH . '/File.php';
require_once INCLUDES_PATH . '/Router.php';
