<?php
/**
 * @file
 * Parse book lists and populate the database.
 */
require_once __DIR__ . '/../Web/includes/bootstrap.php';
require_once __DIR__ . '/../Web/config.php';

function load($file) {
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
			WHERE sec.num LIKE :section_num
				AND c.num LIKE :course_num
				AND sub.abbr LIKE :subject_abbr
			)
		)
	";
	global $config;
	$db = new DB($config->db['institution']['umich']);

	$ouput = '';
	exec('catdoc ' . __DIR__ . '/' . $file, $output);
	for ($i = 0; $i < count($output); $i++) {
		echo $output[$i] . "\n";
		$book_info = explode(' ', $output[$i]);
		$book_id = $db->insert($book_sql, array('isbn' => $book_info[3]));
		$db->perform($linkage_sql, array(
			'isbn'         => $book_info[3],
			'section_num'  => '%' . $book_info[2],
			'course_num'   => '%' . $book_info[1],
			'subject_abbr' => '%' . $book_info[0],
		));
	}
}

load('um_book');
