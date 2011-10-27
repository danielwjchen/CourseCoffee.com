<?php
/**
 * @file
 * Handle CourseCoffee /home request
 */
class HomeController extends CourseCoffeeController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'home' => 'getHomePage',
		);
	}

	/**
	 * Get the home output for a user
	 */
	public function getHomePage() {
		$this->redirectUnsupportedDomain();
		$this->redirectUnknownUser();

		// debug
		// error_log(__METHOD__ . ' - user session - ' . print_r($_SESSION, true));

		$profile    = $this->user_session->getUserProfile();
		$class_list = $this->user_session->getUserClassList();
		$this->output = new HomePageView(array(
			'fb_uid'     => $this->user_session->getFbUserId(),
			'user_id'    => $this->user_session->getUserId(),
			'role'       => $this->user_session->getUserRole(),
			'profile'    => $profile,
			'class_list' => $class_list,
			'timestamp'  => time(),
		));
	}
}
