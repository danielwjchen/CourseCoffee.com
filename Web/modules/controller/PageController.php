<?php
/**
 * @file
 * Manage some default output
 */
class PageController extends CourseCoffeeController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'page-not-found'  => 'get404Page',
			'all-system-down' => 'get500Page',
			'terms-of-use'    => 'getTermsOfUsePage',
			'privacy-policy'  => 'getPrivacyPage',
			'how-to-find-syllabus' => 'getTutorialPage',
		);
	}

	/**
	 * Get the 404 output
	 */
	public function get404Page() {
		$login_form = new UserLoginFormModel($this->sub_domain);
		$this->output = new NotFoundPageView(array(
			'login_token' => $login_form->initializeFormToken(),
		));
	}

	/**
	 * Get the 500 output
	 */
	public function get500Page() {
		$login_form = new UserLoginFormModel($this->sub_domain);
		$this->output = new InternalErrorPageView(array(
			'login_token' => $login_form->initializeFormToken(),
		));
	}

	/**
	 * Get the terms of use page
	 */
	public function getTermsOfUsePage() {
		$this->output = new TermsOfUsePageView(array());
	}

	/**
	 * Get the tutorial page
	 */
	public function getTutorialPage() {
		$this->output = new TutorialPageView(array());
	}


	/**
	 * Get the privacy page
	 */
	public function getPrivacyPage() {
		$this->output = new PrivacyPolicyPageView(array());
	}
}
