<?php

require_once 'bootstrap.php';

$db = new DB($config->db);
Factory::Init($config->db);
