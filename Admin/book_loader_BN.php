<?php
/**
 * @file
 * Parse book lists and populate the database.
 *
 * This script is written specially for lists generated form Barnes & Nobles, 
 * and it also attempts to complete class list
 */
require_once __DIR__ . '/../Web/includes/bootstrap.php';
require_once __DIR__ . '/../Web/config.php';



/**
 * Grab a piece of given strign based on pattern
 *
 * @param string $pattern
 * @param string $string
 */
function grabPattern($pattern, $string) {
	$match = array();
	preg_match("/$pattern/i", $string, $match);
	return reset($match);
}

function load($file) {
	global $config;
	// $db = new DB($config->db['institution']['wisc']);
	$db = new DB($config->db['institution']['emich']);

	$ouput = '';

	// regular expression patterns
	$subject_abbr_pattern = '[a-z\s\-&]+';
	$course_num_pattern   = '[0-9]+[a-z]?';
	$section_num_pattern  = '[0-9]+[a-z]?';
	$isbn_pattern         = '[0-9]{10}';

	exec('catdoc ' . __DIR__ . '/' . $file, $output);
	// skip first line
	for ($i = 1; $i < count($output); $i++) {
		$subjec_id  = '';
		$course_id  = '';
		$section_id = '';
		$book_id    = '';

		$book_info = explode(' ', $output[$i]);


		// get rid of required book info.... because we don't need it for now
		$output[$i] = preg_replace('/(yes|no)/i', '', $output[$i]);

		$section_code = grabPattern("^{$subject_abbr_pattern}\s{$course_num_pattern}\s{$section_num_pattern}", $output[$i]);

		// grab book info
		$string = str_replace($section_code, '', $output[$i]);
		//$title = trim(str_replace('NULL', '', str_replace($isbn, '', $string)));

		// grab section info
		$subject_abbr = grabPattern('^' . $subject_abbr_pattern, $section_code);
		$course_and_section = trim(str_replace($subject_abbr, '', $section_code));
		$course_num   = grabPattern('^' . $course_num_pattern, $course_and_section);
		$section_num  = grabPattern($section_num_pattern . '$', $section_code);

		$isbn = grabPattern($isbn_pattern, $string);
		$title = trim(preg_replace('/^[0-9a-z\s\-&]+(NULL\sNULL)/i', '', $output[$i]));
		echo "$subject_abbr - $course_num - $section_num : $title. \n";

		$book_id = '';
		$book_record = $db->fetch(
			'SELECT * FROM `book` 
			WHERE `isbn` = :isbn OR `title` = :title', 
			array(
				'isbn'  => $isbn,
				'title' => $title,
			)
		);
		if (!empty($book_record['id'])) {
			$book_id = $book_record['id'];

			if (empty($book_record['title'])) {
				$db->perform(
					'UPDATE `book` SET `title` = :title WHERE `id` = :id', 
					array(
						'title' => $title,
						'id' => $book_id,
					)
				);
			}
			if (empty($book_record['isbn'])) {
				$db->perform(
					'UPDATE `book` SET `isbn` = :isbn WHERE `id` = :id', 
					array(
						'isbn' => $isbn,
						'id' => $book_id,
					)
				);
			}

		} elseif (!empty($isbn) || !empty($title)) {
			$book_id = $db->insert('INSERT INTO `book` (`isbn`, `title`) VALUES (:isbn, :title)', array(
				'isbn'  => $isbn,
				'title' => $title,
			));
		}
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
				'section_num'  => $section_num,
				'course_num'   => $course_num,
				'subject_abbr' => $subject_abbr,
		));
		if (empty($section_record['id'])) {
			$subject = $db->fetch("SELECT * FROM subject WHERE abbr = :abbr", array('abbr' => $subject_abbr));
			$course  = $db->fetch("SELECT * FROM course WHERE num = :num", array('num' => $course_num));
			$section = $db->fetch("SELECT * FROM section WHERE num = :num", array('num' => $section_num));
			if (empty($subject['id'])) {
				$subject_id = $db->insert(
					"INSERT INTO subject (abbr) VALUE (:abbr)",
					array('abbr' => $subject_abbr)
				);
			} else {
				$subject_id = $subject['id'];
			}
			if (empty($course['id'])) {
				$course_id = $db->insert(
					"INSERT INTO course (subject_id, num) VALUE (:subject_id, :num)",
					array(
						'subject_id' => $subject_id,
						'num' => $course_num
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
						'num' => $section_num
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
// list of generated book list. the inactive ones are commented out
// $book_list_file = 'wayne_BN';
// $book_list_file = 'udmercy_BN';
// $book_list_file = 'umflint_BN';
// $book_list_file = 'umd_BN';
// $book_list_file = 'WISC_Neebo';
$book_list_file = 'EMICH_bkstr';

load($book_list_file);
