<?php

define('INCLUDES_PATH', __DIR__);
define('ROOT_PATH', str_replace('includes', '', INCLUDES_PATH));
define('CACHE_PATH', ROOT_PATH . '/cache');
define('SETTING_PATH', ROOT_PATH . '/setting');
define('SCHEMA_PATH', ROOT_PATH . '/schema');
define('DAO_PATH', ROOT_PATH . '/dao');
define('CONTROLLER_PATH', ROOT_PATH . '/controller');
define('MODEL_PATH', ROOT_PATH . '/model');
define('VIEW_PATH', ROOT_PATH . '/view');
define('LIB_PATH', ROOT_PATH . '/lib');
define('FILE_PATH', ROOT_PATH . '/files');
define('FILE_CACHE_PATH', FILE_PATH . '/cache');


require_once INCLUDES_PATH . '/Autoloader.php';
require_once INCLUDES_PATH . '/Config.php';
require_once INCLUDES_PATH . '/Emailer.php';
require_once INCLUDES_PATH . '/Session.php';
require_once INCLUDES_PATH . '/DB.php';
require_once INCLUDES_PATH . '/File.php';
require_once INCLUDES_PATH . '/Router.php';
require_once INCLUDES_PATH . '/Logger.php';
require_once INCLUDES_PATH . '/Crypto.php';
require_once INCLUDES_PATH . '/Cache.php';
require_once INCLUDES_PATH . '/Cookie.php';

/**
 * PHP settings:
 *
 * To see what PHP settings are possible, including whether they can
 * be set at runtime (ie., when ini_set() occurs), read the PHP
 * documentation at http://www.php.net/manual/en/ini.php#ini.list
 * and take a look at the .htaccess file to see which non-runtime
 * settings are used there. Settings defined here should not be
 * duplicated there so as to avoid conflict issues.
 */
ini_set('arg_separator.output',     '&amp;');
ini_set('magic_quotes_runtime',     0);
ini_set('magic_quotes_sybase',      0);
ini_set('session.cache_expire',     200000);
ini_set('session.cache_limiter',    'none');
ini_set('session.cookie_lifetime',  2000000);
ini_set('session.gc_maxlifetime',   200000);
ini_set('session.use_cookies',      1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid',    0);
ini_set('url_rewriter.tags',        '');
// this setting is not yet implemented
// ini_set('session.save_handler',     'user');
