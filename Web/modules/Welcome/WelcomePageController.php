<?php
/**
 * @file
 * Provide welcome page
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
class WelcomePageController extends InstitutionPageController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'getWelcomePage' => array(
				'welcome',
			),
		);
	}

	/**
	 * Get the welcome output
	 *
	 * we redirect if the user is logged in
	 */
	public function getWelcomePage() {
		if ($this->isUserLoggedIn()) {
			header('Location: ' . self::PAGE_HOME);
		}
		$this->redirectUnsupportedDomain();

		$login_form    = new UserLoginFormModel($this->sub_domain);
		$register_form = new UserRegisterFormModel($this->sub_domain);
		$file_form     = new FileFormModel($this->sub_domain);

		$this->output = new WelcomePageView(array(
			'login_token'    => $login_form->initializeFormToken(),
			'register_token' => $register_form->initializeFormToken(),
			'file_token'     => $file_form->initializeFormToken()
		));
	}

}
