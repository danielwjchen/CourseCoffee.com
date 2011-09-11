<?php
/**
 * @file
 * Fix missing classes for MTH 132
 */
require_once __DIR__ . '/../Web/includes/bootstrap.php';
require_once __DIR__ . '/../Web/config.php';

$sql = "
	";
$db = new DB($config->db);
$db->perform("DELETE FROM `section` WHERE `course_id` = 2422");
for ($i = 1; $i <=40; $i++) {
	$num = (string)str_pad($i, 3, '0', STR_PAD_LEFT);
	$section_id = $db->insert("
		INSERT INTO `section` (`institution_id`, `course_id`, `num`) 
		VALUES (1, 2422, :num)",
		array('num' => $num)
	);
	$db->insert("
		INSERT INTO `book_section_linkage` (`book_id`, `section_id`)
		VALUES (2482, :section_id)",
		array('section_id' => $section_id)
	);
}

