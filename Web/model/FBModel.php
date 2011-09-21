<?php
require_once LIB_PATH . '/facebook/src/facebook.php';
/**
 * @file
 * Handle facebook related tasks
 */
class FBModel extends Model {
	const ERROR_FB_UNKNOWN_ALGORITHM = 'Unknown algorithm. Expected HMAC-SHA256';
	const ERROR_FB_BAD_SIGNATURE     = 'Bad Signed JSON signature!';
	const ERROR_FB_UID_TAKEN         = 'We are sorry, but the facebook profile you wish to register with is already taken.';
	const EVENT_FB_UID_TAKEN         = 'Attempt to regitster with existing facebook profile.';

	const FAIL_REDIRECT = '/facebook-down';
	const REDIRECT = '/home';

	private $college_list_dao;
	private $facebook;
	private $linkage;

	function __construct($sub_domain) {
		parent::__construct($sub_domain);
		$this->fb = new Facebook(array(
			'appId'  => $config->facebook['id'],
			'secret' => $config->facebook['secret'],
		));
		$this->college_list_dao = new CollegeListDAO($this->default_db);
		$this->linkage          = new UserFacebookLinkageDAO($this->default_db);
	}

	private function base64UrlDecode($input) {
		return base64_decode(strtr($input, '-_', '+/'));
	}

	/**
	 * Parse data from facebook
	 */
	private function parseFBSignedRequest($signed_request) {
		global $config;
		$secret = $config->facebook['secret'];
		list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

		// decode the data
		$sig = $this->base64UrlDecode($encoded_sig);
		$data = json_decode($this->base64UrlDecode($payload), true);
		if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
			Logger::Write(self::ERROR_FB_UNKNOWN_ALGORITHM);
			return array(
				'error'    => true,
				'redirect' => self::FAIL_REDIRECT,
				'message'  => self::ERROR_FB_UNKNOWN_ALGORITHM
			);
		}
		// check sig
		$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
		if ($sig !== $expected_sig) {
			Logger::Write(self::ERROR_FB_BAD_SIGNATURE);
			return array(
				'error'    => true,
				'redirect' => self::FAIL_REDIRECT,
				'message'  => self::ERROR_FB_BAD_SIGNATURE,
			);
		}

		$data['success'] = true;
		return $data;
	}

	/**
	 * Check if an account is already registered with the facebook user id
	 *
	 * @param int $fb_uid
	 *
	 * @return bool
	 */
	public function checkFBUid($fb_uid) {
		return $this->linkage->read(array('fb_uid' => $fb_uid));
	}

	/**
	 * Generate fields for the facebook registraion plugin
	 *
	 * @param string $current_domain
	 */
	public function generateSignUpForm($current_domain) {
		return array(
			'fields' => "[
				{'name' : 'name'},
				{'name' : 'first_name'},
				{'name' : 'last_name'},
				{'name' : 'email'},
				{'name' : 'password'}
			]",
			'redirect' => 'http://' . $current_domain . "/user-register-facebook",
		);

	}

	/**
	 * Link user with facebook
	 */
	public function processFBLinkageRequest($fb_uid, $user_id) {
		if ($this->linkage->read(array('fb_uid' => $fb_uid))) {
			Logger::Write(self::EVENT_FB_UID_TAKEN);
			return array(
				'error' => true,
				'message' => self::ERROR_FB_UID_TAKEN,
			);
		}

		return $this->linkage->create(array(
			'user_id' => $user_id,
			'fb_uid'  => $fb_uid,
		));
	}

	/**
	 * Process user registration request from facebook
	 */
	public function processSignUpRequest($request) {
		$parsed_request = $this->parseFBSignedRequest($request);

		// debug
		// error_log(__METHOD__ . 'response - ' . print_r($parsed_request, true));

		if (isset($parsed_request['success']) && isset($parsed_request['registration'])) {
			$result = $parsed_request['registration'];
			$result['fb_uid']  = isset($parsed_request['user_id']) ? $parsed_request['user_id'] : null;
			$result['success'] = true;
			$result['redirect'] = self::REDIRECT;
			return $result;
		}

		return $parsed_request;
	}
}
