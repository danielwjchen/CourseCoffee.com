<?php
/**
 * @file
 */
class AgreementPageController extends PageController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function definePath() {
		return array(
			'terms-of-use'   => 'getTermsOfUsePage',
			'privacy-policy' => 'getPrivacyPage',
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
