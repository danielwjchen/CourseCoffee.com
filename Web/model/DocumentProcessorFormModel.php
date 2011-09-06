<?php
/**
 * @file
 * Process documents
 */
class DocumentProcessorFormModel extends FormModel{
	/**
	 * @defgroup event_messages
	 * @{
	 * Error message for the user when an event is encountered
	 */

	const EVENT_FAIL      = 'The document could not be processed.';
	const EVENT_MAX_TIME  = 'The document took too long to process.';
	const EVENT_MAX_TRY   = 'User exceede max document submission attempts';
	const EVENT_FORM_EXPIRED = 'document process form expired.';

	/**
	 * @} End of event_messages
	 */


	/**
	 * @defgroup error_messages
	 * @{
	 * Error message for the user when an error is encountered
	 */

	const ERROR_FAIL         = 'The document could not be processed.';
	const ERROR_MAX_TIME     = 'The document took too long to process.';
	const ERROR_MAX_TRY      = 'User exceede max document submission attempts';
	const ERROR_FORM_EXPIRED = 'document process form expired.';

	/**
	 * @} End of error_messages
	 */

	const HAS_SYLLABUS = 'has_syllabus';

	/**
	 * Access to file records
	 */
	private $file_dao;

	/**
	 * Access to section records
	 */
	private $section_dao;

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->file_dao    = new FileDAO($this->default_db);
		$this->section_dao = new SectionDAO($this->institution_db);
		// form submission is limite to 5 times
		$this->max_try = 5;
		// form expires in three hours
		$this->expire = 3 * 60 * 60;
	}

	/**
	 * Read document and return a content in arrays of text
	 *
	 * @return array
	 */
	private function readDocument($doc, $mime) {
		$doc = escapeshellcmd($doc);
		$doc = escapeshellarg($doc);

		$output = null;
		if (strpos($mime, 'pdf')) {
			exec('pdftotext ' . FILE_PATH . '/' . $doc . ' -  -layout', $output);

		} elseif (strpos($mime, 'word')) {
			exec('catdoc ' . FILE_PATH . '/' . $doc, $output);

		} elseif (strpos($mime, 'html')) {
			exec('html2text ' . FILE_PATH . '/' . $doc, $output);

		} elseif (strpos($mime, 'plain')) {
			exec('cat ' . FILE_PATH . '/' . $doc, $output);

		}

		// debug
		// error_log(__METHOD__ . ' : output - ' . print_r($output, true));

		if (empty($output)) {
			Logger::write(self::EVENT_FAIL);
			return array(
				'error' => self::ERROR_FAIL,
			);
		}

		return $output;
	}

	/**
	 * Process the document
	 *
	 * This method goes through the document and try to tag information of the 
	 * class
	 *
	 * @param string $doc
	 * @param string $mime
	 * @param string $token
	 *  
	 * @return array
	 *  On success:
	 *   - success:
	 *   - institution_id: optional
	 *   - year_id: optional
	 *   - section_id: optional
	 *   - course_code: optional
	 *   - content:
	 *  On failure:
	 *   - error:
	 *   - message:
	 */
	public function processDocument($doc, $mime, $token) {
		$this->incrementTries();
		// if the form token has expired
		if (!$this->validateFormToken($token)) {
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_FORM_EXPIRED);
			return array(
				'token'    => $token,
				'error'    => self::ERROR_FORM_EXPIRED
			);
		}

		// if user retried to many times..
		if ($this->hasExceededMaxTries()) {
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_MAX_TRY);
			return array(
				'token'    => $token,
				'error'    => self::ERROR_MAX_TRY
			);
		}

		$output = $this->readDocument($doc, $mime);

		if (isset($output['error'])) {
			return $output;
		}

		$line_count = count($output);
		$search_range = $line_count > 50 ? 50 : $line_count;
		$params = array();

		/**
		 * List possible institution name string patterns 
		 *
		 * This should be improved in the future and utilize records in the 
		 * institution_alias table.
		 */
		$institution_list = array(
			'/michigan state/i' => 'Michigan State University',
			'/msu/i' => 'Michigan State University',
			'/university of michigan/i' => 'University of Michigan',
			'/um/i' => 'University of Michigan',
		);
		// flag to escape doulbe loop
		$escape = false;

		for ($i = 0; $i < $search_range; $i++) {
			foreach($institution_list as $key => $value) {
				preg_match($key, $output[$i], $matches);
				if (!empty($matches)) {
					$school_string = $value;
					$escape = true;
					break;
				}
			}
			if ($escape) {
				break;
			}
		}

		$school = new InstitutionDAO();
		$school->read(array('name' => $school_string));
		$params['institution_id'] = $school->id;

		/**
		 * @to-do
		 * we hardcode year and term id to 1 because there is only one semester
		 */
		$params['year_id'] = 1;
		$params['term_id'] = 1;

		/**
		 * Find first 5 unique matches as possible course code candidate
		 */
		$course_code_array = array();
		for ($i = 0; $i < $search_range; $i++) {
			$matches = array();
			preg_match('/[a-z]{2,12}.[0-9]{3,4}[a-z]?/i', $output[$i], $matches);
			if (!empty($matches)) {
				$course_code_array = array_unique(array_merge($course_code_array, $matches));
			}

			if (count($course_code_array) == 5) {
				break ;
			}
		}

		/**
		 * Look for section number
		 * 
		 * This will look for the section keyword and try to break apart into numeric 
		 * values
		 */
		$section_match = array();
		for ($i = 0; $i < $search_range; $i++) {
			preg_match('/section.?[0-9]?[a-z]{0,2}.?[0-9]?[a-z]{0,2}/i', $output[$i], $matches);
			if (!empty($matches)) {
				$section_match = array_unique(array_merge($section_match, $matches));
			}
		}
		
		// get the numeric value
		$section_array = array();
		for ($i = 0; $i < count($section_match); $i++) {
			$section_array = array_unique(array_merge($section_array, preg_split('/[^0-9]?/', preg_replace('/section/i', '', $section_match[$i]))));
		}

		/**
		 * Compare string with record
		 */
		$course_code = '';
		$section_id = '';
		$list = array();
		for ($i = 0; $i < count($course_code_array); $i++) {
			preg_match('/[a-z]{1,12}/i', $course_code_array[$i], $matches);
			$subject_abbr = $matches[0];
			$params['like']['subject_abbr'] = $subject_abbr;
			
			$course_num = str_replace($subject_abbr, '', $course_code_array[$i]);
			$params['like']['course_num'] = trim($course_num);

			$params['limit']['offset'] = 0;
			$params['limit']['count']  = 10;

			$class_list = new CollegeClassSuggestDAO();
			$has_no_record = $class_list->read($params);
			if (!$has_no_record) {
				$course_code = strtoupper($course_code_array[$i]);
				$list = $class_list->list;
				if (isset($list['section_num'])) {
					if (in_array($list[$i]['section_num'], $section_array)) {
						$section_id = $list[$i]['section_id'];
					}
					break;
				}
				for ($i = 0; $i < count($list); $i++) {
					if (in_array($list[$i]['section_num'], $section_array)) {
						$section_id = $list[$i]['section_id'];
						break;
					}
				}
				break;
			}
		}


		$result = implode("\n", $output);
		$result = htmlentities($result, ENT_QUOTES, 'UTF-8');

		// This broke the covernsion!! 
		// $result = str_replace("\n","[NEWLINE]", $result);
		// $result = utf8_encode(preg_replace('/[^(\x20-\x7F)\x0A]*/', '', $result));
		// $result = str_replace("[NEWLINE]", "\n", $result);
		// $result = iconv("UTF-8","UTF-8//IGNORE", $result);

		// debug
		// error_log('document procesor section_id - ' . $section_id);


		return array(
			'success'        => true,
			'institution_id' => $params['institution_id'],
			'year_id'        => $params['year_id'],
			'term_id'        => $params['term_id'],
			'section_id'     => $section_id,
			'course_code'    => $course_code,
			'file_id'        => $this->file_dao->id,
			'content'        => $result
		);
	}

	/**
	 * Create linkage among uploaded syllabus file and class section
	 */
	public function setSectionSyllabus($section_id, $file_id) {
		$this->section_dao->read(array('id' => $section_id));
		$this->section_dao->syllabus_status = self::HAS_SYLLABUS;
		$this->section_dao->syllabus_id = $file_id;
		$this->section_dao->update();

		// debug
		// error_log(__METHOD__ . ' : document procesor section - ' . print_r($this->section_dao->attribute, true));
	}

	/**
	 * Update the syllabus status of given section
	 */
	public function updateSectionSyllabusStatus($section_id, $status) {

		// debug
		// error_log('document procesor section_id - ' . $section_id);

		$this->section_dao->read(array('id' => $section_id));
		$this->section_dao->syllabus_status = $status;
		$this->section_dao->update();

		// debug
		// error_log(__METHOD__ . ' : document procesor section - ' . print_r($this->section_dao->attribute, true));

	}

}
