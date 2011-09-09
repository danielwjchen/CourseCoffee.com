<?php
/**
 * @file
 * Handle profile image upload requests
 */
class UserProfileImageForm extends FormModel{
	/**
	 * Access to file record
	 */
	private $file_dao;


	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->file_dao = new FileDAO();
		$this->form_name = 'profile_image_upload_form';
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
	 *  On failure:
	 *   - error
	 */
	public function processForm($user_id, $token, $filename) {
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
			// find the old record and remove it
			$this->file_dao->read(array(
				'user_id' => $user_id,
				'type' => FileType::PROFILE_IMAGE,
			));
			$this->file_dao->destroy();

			$result['user_id'] = $user_id;
			$result['type']    = FileType::PROFILE_IMAGE;
			$this->file_dao->create($result);
		}

		return $result;

	}
}
