<?php
/**
 * @file
 */
class AdminPageController extends PageController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function definePath() {
		return array(
			'admin'           => 'getAdminPage',
		);
	}

	/**
	 * Get the admin page for user
	 */
	public function getAdminPage() {
		$this->redirectUnsupportedDomain();
		$this->redirectUnknownUser();
		$this->output = new AdminPageView(array(
			'user_id'    => $this->user_session->getUserId(),
			'role'       => $this->user_session->getUserRole(),
			'profile'    => $profile,
			'class_list' => $class_list,
			'timestamp'  => time(),
		));
	}
}
