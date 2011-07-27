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
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->file_dao = new FileDAO($this->sys_db);
		// form submission is limite to 5 times
		$this->max_try = 5;
		// form expires in three hours
		$this->expire = 3 * 60 * 60;
	}

	/**
	 * Process the document
	 *
	 * @param string $doc
	 *  
	 */
	public function processDocument($doc, $token) {
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
		exec('pdftotext ' . FILE_PATH . '/' . $doc . ' - ', $output);
		if (empty($output)) {
			Logger::write(self::EVENT_FAIL);
			return array(
				'error' => self::ERROR_FAIL,
			);
		}
		$result = implode("\n", $output);
		return array(
			'content' => $result
		);
	}

}
