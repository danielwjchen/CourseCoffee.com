<?php
/**
 * @file
 * Parse book lists and populate the database.
 */
require_once __DIR__ . '/../Web/includes/bootstrap.php';
require_once __DIR__ . '/../Web/config.php';

function load($file, $institution_name) {
	$book_sql = "
		INSERT INTO book (isbn) VALUE (:isbn)
	";
	$linkage_sql = "
		INSERT INTO book_section_linkage (book_id, section_id)
		VALUES (
			(SELECT id FROM book WHERE book.isbn = :isbn),
			(SELECT sec.id 
			FROM section sec
			INNER JOIN course c
				ON sec.course_id = c.id
			INNER JOIN subject sub
				ON c.subject_id = sub.id
			INNER JOIN (
				subject_term_linkage st_linkage, 
				institution_term it,
				institution i
			)
				ON sub.id = st_linkage.subject_id
				AND st_linkage.term_id = it.id
				AND it.institution_id = i.id
			WHERE sec.num = :section_num
				AND c.num = :course_num
				AND sub.abbr = :subject_abbr
				AND i.name = :institution_name
			GROUP BY sec.id)
		)
	";
	global $config;
	$db = new DB($config->db);

	$ouput = '';
	exec('catdoc ' . __DIR__ . '/' . $file, $output);
	for ($i = 0; $i < count($output); $i++) {
		echo $output[$i] . "\n";
		$book_info = explode(' ', $output[$i]);
		$book_id = $db->insert($book_sql, array('isbn' => $book_info[3]));
		$db->perform($linkage_sql, array(
			'isbn'             => $book_info[3],
			'section_num'      => $book_info[2],
			'course_num'       => $book_info[1],
			'subject_abbr'     => $book_info[0],
			'institution_name' => $institution_name,
		));
	}
}

load('msu_book', 'Michigan State University');
