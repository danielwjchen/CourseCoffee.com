<?php
/**
 * @file
 * Parse book lists and populate the database.
 *
 * This script is written specially for lists in XML format and it also 
 * attempts to complete class list
 */
require_once __DIR__ . '/../Web/includes/bootstrap.php';
require_once __DIR__ . '/../Web/config.php';

/**
 * Load item XML and convert entry into database record
 *
 */
class BookLoaderXML {

    private $db;
    private $xml;

    /**
     * Default constructor
     *
     * @param string $xml_file
     *  name of the xml file
     * @param array $config_db
     *  an associative array that defines database connection
     */
    function __construct($xml_file, $config_db) {
        //$this->db = new DB($config_db);
        $this->xml = new DOMDocument();
        $this->xml->load(__DIR__ . "/Bookstore/XML/" . $xml_file . ".xml");
    }

    /**
     * Check and update curriculum record
     * 
     * This method checks if a curriculum section exists first and creates 
     * record when there isn't one.
     *
     * @param array $subject
     *  - abbr
     *  - title
     * @param array $course
     *  - num
     *  - title
     * @param array $section
     *  - num
     *
     * @return section_id
     */
    protected function checkAndUpdateCurriculumRecord(array $subject, array $course, array $section) {
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
                'section_num'  => $section['num'],
                'course_num'   => $course['num'],
                'subject_abbr' => $subject['abbr'],
        ));
        if (!empty($section_record['id'])) {
            return $section_record['id'];

        } else {
            $subject_record = $db->fetch(
                "SELECT * FROM subject WHERE abbr = :abbr", 
                array('abbr' => $subject['abbr'])
            );
            $course_record  = $db->fetch(
                "SELECT * FROM course WHERE num = :num", 
                array('num' => $course['num'])
            );
            if (empty($subject_record['id'])) {
                $subject_id = $db->insert(
                    "INSERT INTO subject (abbr) VALUE (:abbr)",
                    array('abbr' => $subject['abbr'])
                );
            }
            if (empty($course_record['id'])) {
                $course_id = $db->insert(
                    "INSERT INTO course (subject_id, num) VALUE (:subject_id, :num)",
                    array(
                        'subject_id' => $subject_record['id'],
                        'num' => $course['num']
                    )
                );
            }

            return  $db->insert(
                "INSERT INTO section (course_id, num) VALUE (:course_id, :num)",
                array(
                    'course_id' => $course_record['id'],
                    'num' => $section['num']
                )
            );
        }
    }

    /**
     * Check and update book record
     * 
     * This method checks if a curriculum section exists first and creates 
     * record when there isn't one.
     *
     * @param int $section_id
     * @param array $item
     *  - title
     *  - isbn
     */
    protected function checkAndUpdateItemRecord($section_id, array $item) {
        $item_record = $db->fetch(
            'SELECT * FROM `book` WHERE `isbn` = :isbn OR `title` = :title', 
            array('isbn' => $item['isbn'], 'title' => $item['title'])
        );
        $item_id = $item_record['id'];
		if (empty($item_id)) {
            $item_id = $db->insert(
                'INSERT INTO `book` (`isbn`, `title`) VALUES (:isbn, :title)', 
                array('isbn' => $item['isbn'], 'title' => $item['title'])
			);
		} elseif (empty($item_record['isbn'])) {
            $item_id = $db->insert(
                'UPDATE `book` SET `isbn` = :isbn WHERE `id` = :id', 
                array('id' => $item_record['id'], 'isbn' => $item['isbn'])
            );
		} elseif (empty($item_record['title'])) {
            $item_id = $db->insert(
                'UPDATE `book` SET `title` = :title WHERE `id` = :id', 
                array('id' => $item_record['id'], 'title' => $item['title'])
            );
		}

        $linkage = $db->fetch("
            SELECT * FROM `book_section_linkage`
            WHERE `book_id` = :book_id 
            AND `section_id` = :section_id",
            array(
                'book_id' => $item_id,
                'section_id' => $section_id
            )
         );

        if (!empty($linkage)) {
            return ;
        }

		$db->perform("
			INSERT INTO `book_section_linkage` 
			(`book_id`, `section_id`) VALUES (:book_id, :section_id)
			", array(
				'book_id' => $item_id,
				'section_id' => $section_id,
			)
		);
    }

    /**
     * Process XML and update database with entries
     */
    public function process() {
        $item_elements = $this->xml->getElementsByTagName('item');
        foreach ($item_elements as $element) {
            $subject = array(
                'abbr' => $element->getAttribute('sub_abbr'),
            );
            $course = array(
                'num' => $element->getAttribute('crs'),
            );
            $section = array(
                'num' => $element->getAttribute('sec'),
            );
            $item = array(
                'isbn'     => $element->getAttribute('isbn'),
                'required' => $element->getAttribute('required'),
                'title'    => $element->getAttribute('title'),
            );
            $section_id = $this->checkAndUpdateCurriculumRecord($subject, $course, $section);
            $this->checkAndUpdateItemRecord($section_id, $item);
        }
    }
}

print_r($config->db);
$loader = new BookLoaderXML('CMU_Neebo', $config->db['institution']['cmu']);
$loader->process();
