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

	/**
	 * Access to file records
	 */
	private $file_dao;

	/**
	 * Access to class records
	 */
	private $class_dao;

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->file_dao = new FileDAO($this->db);
		// form submission is limite to 5 times
		$this->max_try = 5;
		// form expires in three hours
		$this->expire = 3 * 60 * 60;
	}

	/**
	 * Process the document
	 *
	 * @param string $doc
	 * @param string $mime
	 * @param string $token
	 *  
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

		if (empty($output)) {
			Logger::write(self::EVENT_FAIL);
			return array(
				'error' => self::ERROR_FAIL,
			);
		}

		$line_count = count($output);
		$search_range = $line_count > 50 ? 50 : $line_count;
		$params = array();

		/**
		 * Find university information
		 */
		$school = array(
			'/michigan state/i' => 'Michigan State University',
			'/msu/i' => 'Michigan State University',
			'/university of michigan/i' => 'University of Michigan',
			'/um/i' => 'University of Michigan',
		);
		// flag to escape doulbe loop
		$escape = false;

		for ($i = 0; $i < $search_range; $i++) {
			foreach($school as $key => $value) {
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

		$school = new InstitutionDAO($this->db);
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
			$params['like']['course_num'] = $course_num;

			$params['limit']['offset'] = 0;
			$params['limit']['count']  = 10;

			$class_list = new CollegeClassSuggestDAO($this->db);
			$has_no_record = $class_list->read($params);
			if (!$has_no_record) {
				$course_code = $course_code_array[$i];
				$list = $class_list->list;
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
		return array(
			'institution_id' => $params['institution_id'],
			'year_id'        => $params['year_id'],
			'term_id'        => $params['term_id'],
			'section_id'     => $section_id,
			'course_code'    => $course_code,
			'content' => $result
		);
	}

}
