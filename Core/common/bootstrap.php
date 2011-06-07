<?php

define('COMMON_PATH', __DIR__);
define('ROOT_PATH', str_replace('common', '', COMMON_PATH));
define('DBA_PATH', ROOT_PATH . 'dba/');
define('DAO_PATH', ROOT_PATH . 'dao/');
define('CONTROLLER_PATH', ROOT_PATH . 'controller/');
define('MODEL_PATH', ROOT_PATH . 'model/');
define('VIEW_PATH', ROOT_PATH . 'view/');

require_once COMMON_PATH.'/config.php';
require_once COMMON_PATH.'/factory.php';
require_once COMMON_PATH.'/DB.php';
require_once COMMON_PATH.'/DBA.php';
require_once COMMON_PATH.'/DAO.php';
