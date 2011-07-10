<?php

require_once LIB_PATH . 'oauth2-php/lib/OAuth2.inc';
require_once INCLUDES_PATH . 'Crypto.php';

/**
 * OAuth2 Implementation.
 */
class OAuth2Model extends OAuth2 {

  private $token_dao;
	private $code_dao;
	private $client_dao;

  /**
   * Extend OAuth2::__construct().
   */
  public function __construct() {
    parent::__construct(array(
			'display_error' => true,
		));

		$this->token_dao = Factory::DAO('OAuth2Token');
		$this->code_dao = Factory::DAO('OAuth2Code');
		$this->client_dao = Factory::DAO('OAuth2Client');

  }


  /**
   * Handle PDO exceptional cases.
   */
  private function handleException($e) {
    echo "Database error: " . $e->getMessage();
    exit;
  }

  /**
   * Little helper function to add a new client to the database.
   *
   * @param $client_id
   *   Client identifier to be stored.
   * @param $client_secret
   *   Client secret to be stored.
   * @param $redirect_uri
   *   Redirect URI to be stored.
   */
  public function addClient($client_id, $client_secret, $redirect_uri) {
		$client_id = Crypto::Encrypt($client_id);
		$client_secret = Crypto::Encrypt($client_secret);
		$redirect_uri = Crypto::Encrypt($redirect_uri);
		$this->client_dao->create(array(
			'id' => $client_id,
			'secret' => $client_secret,
			'redirect_uri' => $redirect_uri
		));
  }

	/**
	 * Override OAuth2::getSupportedGrantTypes()
	 */
	protected function getSupportedGrantTypes() {
		return array(
			OAUTH2_GRANT_TYPE_AUTH_CODE,
			OAUTH2_GRANT_TYPE_USER_CREDENTIALS,
		);
	}

  /**
   * Implement OAuth2::checkClientCredentials().
   *
   * Do NOT use this in production! This sample code stores the secret
   * in plaintext!
   */
  protected function checkClientCredentials($client_id, $client_secret = NULL) {
		$client_id = Crypto::Decrypt($client_id);
		$client_secret = Crypto::Decrypt($client_secret);
		return $this->client_dao->read(array(
			'id' => $client_id,
			'secret' => $client_secret,
		));
  }

  /**
   * Implement OAuth2::getRedirectUri().
   */
  protected function getRedirectUri($client_id) {
		$client_id = Crypto::Decrypt($client_id);
		if ($this->client->read(array('id' => $client_id))) {
			return $this->client_dao->redirect_uri;

		} else {
			return false;

		}

  }

  /**
   * Implement OAuth2::getAccessToken().
   */
  protected function getAccessToken($oauth_token) {
		$oauth_token = Crypto::Decrypt($oauth_token);
		if ($this->token_dao->read(array('token' => $oauth_token))) {
			return array(
				'client_id' => $this->token_dao->client_id,
				'expires' => $this->token_dao->expires,
				'scope' => $this->token_dao->scope,
			);

		} else {
			return null;

		}
  }

  /**
   * Implement OAuth2::setAccessToken().
   */
  protected function setAccessToken($oauth_token, $client_id, $expires, $scope = NULL) {
		$oauth_token = Crypto::Encrypt($oauth_token);
		$client_id = Crypto::Encrypt($client_id);
		$this->token_dao->create(array(
			'id' => $oauth_token,
			'client_id' => $client_id,
			'expires' => $expires,
			'scope' => $scope,
		));
  }

  /**
   * Override OAuth2::getAuthCode().
   */
  protected function getAuthCode($code) {
		$code = Crypto::Encrypt($code);

		if ($this->code_dao->read(array('id' => $code))) {
			return array(
				'client_id' => $this->code_dao->client_id,
				'redirect_uri' => $this->code_dao->redirect_uri,
				'expires' => $this->code_dao->expires,
			);

		} else {
			return false;

		}
  }

  /**
   * Override OAuth2::setAuthCode().
   */
  protected function setAuthCode($code, $client_id, $redirect_uri, $expires, $scope = NULL) {
		$code = Crypto::Encrypt($code);
		$client_id = Crypto::Encrypt($client_id);
		$this->code_dao->create(array(
			'id' => $code,
			'client_id' => $client_id,
			'expires' => $expires,
			'redirect_uri' => $redirect_uri,
			'scope' => $scope,
		));

  }
}
