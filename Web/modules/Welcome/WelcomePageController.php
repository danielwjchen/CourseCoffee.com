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
	 * Implement ControllerInterface::action()
	 */
	public function action($callback, array $params = null) {
		$this->redirectUnsupportedDomain();
		call_user_func_array(array($this, $callback), $params);
		echo $this->output->render();
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
