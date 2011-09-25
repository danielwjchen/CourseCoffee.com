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
	 * A value used to delimit pages
	 */
	const LINE_PER_PAGE = 50;

	/**
	 * Access to section records
	 */
	private $section_dao;

	/**
	 * Extend Model::__construct()
	 */
	function __construct($sub_domain) {
		parent::__construct($sub_domain);
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
		if (strpos($mime, 'pdf') !== false) {
			exec('pdftotext ' . FILE_PATH . '/' . $doc . ' -  -layout', $output);

		} elseif (strpos($mime, 'word') !== false) {
			exec('catdoc ' . FILE_PATH . '/' . $doc, $output);

		} elseif (strpos($mime, 'html') !== false) {
			exec('html2text ' . FILE_PATH . '/' . $doc, $output);

		} elseif (strpos($mime, 'plain') !== false) {
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
	 * Clean up output to a browser safe format
	 */
	private function cleanUpOutput($output) {
		$output = htmlentities($output, ENT_QUOTES, 'UTF-8');

		$output = str_replace("\n","[NEWLINE]", $output);
		$output = utf8_encode(preg_replace('/[^(\x20-\x7F)\x0A]*/', '', $output));
		$output = str_replace("[NEWLINE]", "\n", $output);
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

		$paged_output = array_chunk($output, self::LINE_PER_PAGE);
		$page_count = count($paged_output);
		$result = array();

		for($i = 0; $i < $page_count; $i++) {
			$result[] = $this->cleanUpOutput(implode("\n", $paged_output[$i]));
		}

		$content = implode('PAGE_BREAK', $result);


		// debug
		// error_log('document procesor section_id - ' . $section_id);


		return array(
			'success'    => true,
			'page_count' => $page_count,
			'content'    => $content,
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
