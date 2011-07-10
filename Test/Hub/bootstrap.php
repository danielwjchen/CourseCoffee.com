<?php
/**
 * @file
 * The boostrap proccess defines, loads, and includes the necessary files and
 * values to mimic the Hub site environment
 */

define('TEST_CORE_PATH', __DIR__);
define('TEST_PATH', str_replace('Hub', '', TEST_CORE_PATH));
define('CORE_PATH', str_replace('Test/Hub', 'Hub/', TEST_CORE_PATH));
define('TEST_CORE_DAO_PATH', TEST_CORE_PATH . '/dao/');

require_once TEST_PATH . '/simpletest/autorun.php';
require_once CORE_PATH . '/includes/bootstrap.php';
require_once __DIR__ . '/HubSetup.php';
require_once __DIR__ . '/HubTestCase.php';
 
error_reporting(E_ALL);
ini_set('display_errors', 'Off');
