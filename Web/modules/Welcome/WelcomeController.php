<?php
/**
 * @file
 * Handle CourseCoffee /welcome request
 */
class WelcomeController extends CourseCoffeeController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'welcome' => 'getWelcomePage',
		);
	}

	/**
	 * Get the welcome output
	 *
	 * we redirect if the user is logged in
	 */
	public function getWelcomePage() {
		$this->redirectUnsupportedDomain();
		if ($this->isUserLoggedIn()) {
			$this->redirect(self::PAGE_HOME);
		}

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
