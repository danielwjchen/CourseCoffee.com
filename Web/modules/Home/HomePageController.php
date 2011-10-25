<?php
/**
 * @file
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
class HomePageController extends InstitutionPageController implements ControllerInterface {

	/**
	 * Override PageController::Route()
	 */
	public static function Route() {
		return array(
			'getHomePage' => array(
				'home',
			),
		);
	}

	/**
	 * Override PageController::action()
	 */
	public function action($callback, array $params = null) {
		$this->redirectUnsupportedDomain();
		$this->redirectUnknownUser();

		call_user_func_array(array($this, $callback), $params);
		echo $this->output->render();
	}

	/**
	 * Get the home output for a user
	 */
	public function getHomePage() {

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
