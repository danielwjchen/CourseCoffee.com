<?php
require_once 'bootstrap.php';

$db = new DB($config->db);

$result = $db->fetch('SELECT * FROM node limit 5');

print_r($result);
