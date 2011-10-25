<?php
/**
 * @file
 */
class AgreementPageController extends PageController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			 'getTermsOfUsePage' => array(
				'terms-of-use',
			),
			'getPrivacyPage' => array(
				'privacy-policy',
			),
		);
	}

	/**
	 * Get the terms of use page
	 */
	public function getTermsOfUsePage() {
		$this->output = new TermsOfUsePageView(array());
	}

	/**
	 * Get the privacy page
	 */
	public function getPrivacyPage() {
		$this->output = new PrivacyPolicyPageView(array());
	}

}
