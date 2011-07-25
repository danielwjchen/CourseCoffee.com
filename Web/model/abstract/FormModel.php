<?php
/**
 * @file
 * Handle form validation and creation
 */
require_once INCLUDES_PATH . '/Crypto.php';
require_once INCLUDES_PATH . '/Session.php';

abstract class FormModel extends Model {

	/**
	 * A string to identify the form
	 */
	protected $form_name;

	/**
	 * A numeric value to limit the number of tries a user can have
	 */
	protected $max_try;

	/**
	 * A numeric value of how long in seconds the form expires
	 */
	protected $expire;

	/**
	 * Initialize the token for the form
	 *
	 * @return string $token
	 *  a token generated and will expire in a period of time
	 */
	public function initializeFormToken() {
		error_log(print_r($_SESSION,true));
		$timestamp = time();
		$token = Crypto::Encrypt($this->form_name . $timestamp);
		Session::Set($this->form_name, $token);
		Session::Set($this->form_name . '_expire', $timestamp + $this->expire);
		Session::Set($this->form_name . '_tries', 0);
		return $token;
	}

	/**
	 * Validate the token of the form
	 *
	 * @param string $token
	 *  a string that identifies the token
	 */
	protected function validateFormToken($token) {
		return (
			$token === Session::Get($this->form_name) &&
			time() <= Session::Get($this->form_name . '_expire')
		);
	}

	/**
	 * Increment submission attempt
	 */
	protected function incrementTries() {
		$tries = Session::Get($this->form_name . '_tries');
		$tries++;
		Session::Set($this->form_name . '_tries', $tries);
	}

	/**
	 * Check number of attempted submission
	 */
	protected function hasExceededMaxTries() {
		return Session::Get($this->form_name . '_tries') > $this->max_try;
	}

	/**
	 * Unset the token for the form 
	 *
	 * This function should be called when the form is successfully submitted
	 */
	protected function unsetFormToken() {
		Session::Del($this->form_name);
		Session::Del($this->form_name . '_expire');
		Session::Del($this->form_name . '_tries');
	}

	/**
	 * Get the token for the form
	 *
	 * @return mixed $token
	 *  a string that identifies the form token stored in session, or boolean 
	 *  false when the token expired
	 */
	protected function getFormToken() {
		return Session::Get($this->form_name);
	}

}
