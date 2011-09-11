<?php
/**
 * @file
 * Parse book lists and populate the database.
 *
 * This script also attempts to complete class list
 */
require_once __DIR__ . '/../Web/includes/bootstrap.php';
require_once __DIR__ . '/../Web/config.php';

function load($file) {
	global $config;
	$db = new DB($config->db['institution']['msu']);

	$ouput = '';
	exec('catdoc ' . __DIR__ . '/' . $file, $output);
	// skip first line
	for ($i = 1; $i < count($output); $i++) {
		$subjec_id  = '';
		$course_id  = '';
		$section_id = '';
		$book_id    = '';

		$book_info = explode(' ', $output[$i]);
		preg_match('/^[a-zA-Z]+\s[0-9]+[a-zA-Z]?\s[0-9]+[a-zA-Z]?/', $output[$i], $match);
		$section_code = $match[0];
		$string = str_replace($section_code, '', $output[$i]);
		preg_match('/[0-9]+[a-zA-Z]?/', $string, $match);
		$isbn = $match[0];
		$title = trim(str_replace('NULL', '', str_replace($isbn, '', $string)));
		$book_record = $db->fetch('SELECT * FROM `book` WHERE `isbn` = :isbn', array('isbn' => $isbn));
		if (!empty($book_record['id'])) {
			$book_record['title'] = $title;
			$db->perform('UPDATE `book` SET `isbn` = :isbn, `title` = :title WHERE `id` = :id', $book_record);
			$book_id = $book_record['id'];
		} else {
			$book_id = $db->insert('INSERT INTO `book` (`isbn`, `title`) VALUES (:isbn, :title)', array(
				'isbn'  => $isbn,
				'title' => $title,
			));
		}
		$section_code_array = explode(' ', $section_code);
		$section_record = $db->fetch("
			SELECT sec.id 
			FROM section sec
			INNER JOIN course c
				ON sec.course_id = c.id
			INNER JOIN subject sub
				ON c.subject_id = sub.id
			WHERE sec.num LIKE :section_num
				AND c.num LIKE :course_num
				AND sub.abbr LIKE :subject_abbr
			", array(
				'section_num'  => $section_code_array[2],
				'course_num'   => $section_code_array[1],
				'subject_abbr' => $section_code_array[0],
		));
		if (empty($section_record['id'])) {
			$subject = $db->fetch("SELECT * FROM subject WHERE abbr = :abbr", array('abbr' => $section_code_array[0]));
			$course  = $db->fetch("SELECT * FROM course WHERE num = :num", array('num' => $section_code_array[1]));
			$section = $db->fetch("SELECT * FROM section WHERE num = :num", array('num' => $section_code_array[2]));
			if (empty($subject['id'])) {
				$subject_id = $db->insert(
					"INSERT INTO subject (abbr) VALUE (:abbr)",
					array('num' => $section_code_array[0])
				);
			} else {
				$subject_id = $subject['id'];
			}
			if (empty($course['id'])) {
				$course_id = $db->insert(
					"INSERT INTO course (subject_id, num) VALUE (:subject_id, :num)",
					array(
						'subject_id' => $subject_id,
						'num' => $section_code_array[1]
					)
				);
			} else {
				$course_id = $course['id'];
			}
			if (empty($section['id'])) {
				$section_id = $db->insert(
					"INSERT INTO section (course_id, num) VALUE (:course_id, :num)",
					array(
						'course_id' => $course_id,
						'num' => $section_code_array[2]
					)
				);
			} else {
				$section_id = $section['id'];
			}
		} else {
			$section_id = $section_record['id'];
		}
		$db->perform("
			INSERT INTO `book_section_linkage` 
			(`book_id`, `section_id`) VALUES (:book_id, :section_id)
			", array(
				'book_id' => $book_id,
				'section_id' => $section_id,
			)
		);
	}
}

load('MSU_book-final');
