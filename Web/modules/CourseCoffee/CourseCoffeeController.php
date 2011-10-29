<?php
/**
 * @file
 * Hanlde CourseCoffee specific controller logics
 */
abstract class CourseCoffeeController extends Controller {

	/**
	 * Default home page
	 */
	const PAGE_DEFAULT   = '/portal';
	const PAGE_PORTAL    = '/portal';
	const PAGE_WELCOME   = '/welcome';
	const PAGE_HOME      = '/home';
	const DEFAULT_DOMAIN = 'www';

	function __construct() {
		global $config;
		$this->domain       = $config->domain;
		$this->sub_domain   = $this->getSubDomain();
		$sub_domain = $config->db['institution'];
		$this->supported_domain = array_flip(array_keys($sub_domain));

		if (array_key_exists($this->getRequestedSubDomain(), $this->supported_domain)) {
			$this->user_session = new UserSessionModel($this->sub_domain);
		}

	}
}
