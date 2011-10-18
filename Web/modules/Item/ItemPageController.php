<?php
/**
 * @file
 * Handle quest API related controller logics
 */
class ItemPageController extends InstitutionPageController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'getBookSearchPage' => array(
				'book-search',
			),
		);
	}

	/**
	 * Override PageController::action()
	 */
	public function action($callback, array $params = null) {
		$this->redirectUnsupportedDomain();

		call_user_func_array(array($this, $callback), $params);
		echo $this->output->render();
	}

	/**
	 * Get the book search page
	 *
	 * @param array $params
	 *  optional, but when presnet it expects values to be in the following order
	 *  - subject_abbr
	 *  - course_num
	 *  - section_num
	 */
	public function getBookSearchPage($params = array()) {
		global $config;
		$this->redirectUnsupportedDomain();
		$login_form = new UserLoginFormModel($this->sub_domain);

		$result = array(
			'base_url'   => 'http://' . $config->domain,
			'role'       => $this->user_session->getUserRole(),
			'is_loggedIn' => $this->getUserId(),
			'login_token' => $login_form->initializeFormToken(),
			'section_id' => '',
		);

		if (!empty($params)) {
			list($subject_abbr, $course_num, $section_num) = $params;
			$class_info = $this->getClassInfo($subject_abbr, $course_num, $section_num);
			$result['section_id'] = $class_info['content']['section_id'];
		}

		$this->output = new BookSearchPageView($result);
	}
}
