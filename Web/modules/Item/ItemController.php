<?php
/**
 * @file
 * Handle CourseCoffee /welcome request
 */
class ItemController extends CourseCoffeeController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'book-search' => 'getItemSearchPage',
			'college-class-reading' => 'getClassItemList',
		);
	}

	/**
	 * Get the Item page
	 *
	 * @param array $params
	 *  optional, but when presnet it expects values to be in the following order
	 *  - subject_abbr
	 *  - course_num
	 *  - section_num
	 */
	public function getItemSearchPage($params = array()) {
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

		$this->output = new ItemSearchPageView($result);
	}

	/**
	 * Get book list for a class
	 */
	public function getClassItemList() {
		$section_id = Input::Post('section_id');
		$list  = new ItemSuggestModel($this->sub_domain);
		$class = new CurriculumClassModel($this->sub_domain);
		$class_info = $class->getClassById($section_id);
		$result = $list->getItemList($section_id);
		$result['uri'] = $class_info['content']['subject_abbr'] . '/' . 
			$class_info['content']['course_num'] . '/' . 
			$class_info['content']['section_num'];
		$this->output = new JSONView($result);
	}

}
