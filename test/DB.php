<?php
require_once '../core/DB.php';

$db = new DB(array(
		'driver' => 'mysql',
		'host' => 'localhost',
		'database' => 'msucoursemash',
		'user' => 'root',
		'password' => 'bTaEaFaTl2aBl',
		'port' => '3306',
	));

$result = $db->fetch('SELECT * FROM node limit 5');

print_r($result);

echo "asdfasdf\n";
