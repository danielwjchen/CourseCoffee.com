<?php

require_once 'bootstrap.php';

class DBATestCase extends UnitTestCase{
}

$config = new Config(array(
	'db' => array(
		'name' => 'qilin_test_core',
		'driver' => 'mysql',
		'host' => 'localhost',
		'user' => 'root',
		'password' => 'bTaEaFaTl2aBl',
		'port' => '3306',
	),
));

$db = new DB($config->db);
DBA::Request($db, 'item_attribute', DBA_PATH . 'item_attribute.php');
DBA::Request($db, 'item_attribute_type', DBA_PATH . 'item_attribute_type.php');

/*

$dbas = array();

if ($handle = opendir(DBA_PATH)) {
	while (($file = readdir($handle)) !== false) {
		if (strpos($file, '.php') !== false) {
			$dbas[] = str_replace('.php', '', $file);
		}
	}
}

for ($i = 0; $i < count($dbas); $i++) {
	DBA::Request($db, $dbas[$i], DBA_PATH . $dbas[$i] . '.php');
}

$schema_new = array(
	'name' => 'test',
	'description' => 'Create a test table',
	'column' => array(
		'id' => array(
			'type' => 'int',
			'unsigned' => TRUE,
			'not null' => TRUE,
			'default' => 0,
			'description' => 'The primary key',
		),
		'vid' => array(
			'type' => 'char',
			'length' => 64,
			'not null' => TRUE,
			'default' => 'sa',
			'description' => 'The revision number',
		),
		'subject_id' => array(
			'type' => 'int',
			'size' => 'normal',
			'not null' => TRUE,
			'default' => 0,
			'description' => 'The primary key that identifies the subject which this course belongs.',
		),
		'num' => array(
			'type' => 'char',
			'length' => 8,
			'not null' => TRUE,
			'description' => 'The number which this course is assigned',
		),
	),
	'primary' => array('num', 'id', 'vid'),
	'unique' => array(
		'vid' => array('vid')
	),
	'index' => array(
		'nid' => array('nid'),
		'subject_id' => array('subject_id'),
		'num' => array('num'),
	),
);

$schema_old = array(
	'name' => 'test',
	'description' => 'Create a test table',
	'column' => array(
		'nid' => array(
			'type' => 'int',
			'unsigned' => TRUE,
			'not null' => TRUE,
			'default' => 0,
			'description' => 'The primary key',
		),
		'vid' => array(
			'type' => 'int',
			'unsigned' => TRUE,
			'not null' => TRUE,
			'default' => 0,
			'description' => 'The revision number',
		),
		'subject_id' => array(
			'type' => 'int',
			'size' => 'normal',
			'not null' => TRUE,
			'default' => 0,
			'description' => 'The primary key that identifies the subject which this course belongs.',
		),
		'num' => array(
			'type' => 'char',
			'length' => 8,
			'not null' => TRUE,
			'description' => 'The number which this course is assigned',
		),
	),
	'primary' => array('nid', 'vid'),
	'unique' => array(
		'vid' => array('vid')
	),
	'index' => array(
		'nid' => array('nid'),
		'subject_id' => array('subject_id'),
		'num' => array('num'),
	),
);

DBA::Alter($db, $schema_new, $schema_old);
*/

 DBA::Remove($db, 'test');
