<?php
/**
 * @file
 * Handle curriculum related requests
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
class AdminPageController extends InstitutionPageController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'getAdminPage' => array(
				'admin',
			),
		);
	}

	/**
	 * Override InstitutionPageController::action()
	 */
	public function action($callback, array $params = null) {
		$this->redirectUnsupportedDomain();
		$this->redirectUnknownUser();
		call_user_func_array(array($this, $callback), $params);
		echo $this->output->render();
	}


	/**
	 * Get the admin page for user
	 */
	public function getAdminPage() {
		$this->output = new AdminPageView(array(
			'user_id'    => $this->user_session->getUserId(),
			'role'       => $this->user_session->getUserRole(),
			'profile'    => $profile,
			'class_list' => $class_list,
			'timestamp'  => time(),
		));
	}
}
