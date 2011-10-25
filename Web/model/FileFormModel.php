<?php
/**
 * @file
 * Handle file upload requests
 */
class FileFormModel extends FormModel{

	/**
	 * @defgroup error_messages
	 * @{
	 * Error message for the user when an error is encountered
	 */

	const ERROR_FAILED_TO_LOGIN = 'The email and password do not match';
	const ERROR_FORM_EXPIRED    = 'User login form expired. Please try again.';
	const ERROR_FORM_EMPTY      = 'The email and password fields cannot be empty';
	const ERROR_MAX_TRY         = 'Too many failed login attempts';

	/**
	 * @defgroup even_messages
	 * @{
	 * Log messges to track events
	 */
	
	const EVENT_FORM_EMPTY   = 'An empty document submission is made. How is this possible?';
	const EVENT_FORM_EXPIRED = 'file form expired.';
	const EVENT_NEW_FILE     = 'New file uploaded';

	/**
	 * @} End of even_messages
	 */

	/**
	 * @} End of error_messages
	 */

	/**
	 * Access to file record
	 */
	private $file_dao;


	/**
	 * Extend Model::__construct()
	 */
	function __construct($sub_domain) {
		parent::__construct($sub_domain);
		$this->file_dao = new FileDAO($this->default_db);
		$this->form_name = 'file_upload_form';
		// form submission is limite to 5 times
		$this->max_try = 5;
		// form expires in three hours
		$this->expire = 60 * 60 * 3;
	}

	/**
	 * Process the file upload request
	 *
	 * @param string $user_id
	 * @param string $token
	 * @param string $filename
	 * @oaram string $type
	 *
	 * @return array
	 *  An associative array that has two different results.
	 *  On success:
	 *   - name
	 *   - size
	 *   - path
	 *   - mime
	 *   - timestamp
	 *  On faie:
	 *   - error
	 */
	public function processForm($user_id, $token, $filename, $type) {
		$this->incrementTries();
		// if the form token has expired
		if (!$this->validateFormToken($token)) {
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_FORM_EXPIRED);
			return array(
				'token' => $token,
				'error' => self::ERROR_FORM_EXPIRED
			);
		}

		$this->unsetFormToken();
		$result = File::SaveUpload($filename);
		if (!isset($result['error'])) {
			$result['user_id'] = $user_id;
			$result['type']    = $type;
			$file_id = $this->file_dao->create($result);
			$result['file_id'] = $file_id;
		}

		return $result;

	}
}
