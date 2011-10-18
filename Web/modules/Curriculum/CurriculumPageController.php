<?php
/**
 * @file
 * Handle curriculum related requests
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
class CurriculumPageController extends InstitutionPageController implements ControllerInterface {

	/**
	 * Override PageController::Route()
	 */
	public static function Route() {
		return array(
			'getCalendarPage' => array(
				'calendar',
			),
			'getClassPage' => array(
				'class',
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
	 * Get Class info
	 *
	 * @param string $sub_abbr
	 * @param string $crs_num
	 * @param string $sec_num
	 */
	protected function getClassInfo($sub_abbr, $crs_num, $sec_num) {
		$class_model = new CurriculumClassModel($this->sub_domain);
		return $class_model->getClassBySectionCode($sub_abbr, $crs_num, $sec_num);
	}

	/**
	 * Get the calendar output for a user
	 */
	public function getCalendarPage() {
		$class_list   = $this->user_session->getUserClassList();
		$user_profile = $this->user_session->getUserProfile(); 

		$this->output = new CalendarPageView(array(
			'user_id'    => $this->user_session->getUserId(),
			'role'       => $this->user_session->getUserRole(),
			'timestamp' => time(),
			'class_list' => $class_list,
			'institution_uri' => $user_profile['institution_uri'],
			'year' => $user_profile['year'],
			'term' => $user_profile['term'],
		));
	}

	/**
	 * Get the class output for a user
	 *
	 * @param array $params
	 *  optional, but when presnet it expects values to be in the following order
	 *  - subject_abbr
	 *  - course_num
	 *  - section_num
	 */
	public function getClassPage($params = array()) {

		$result['class_list'] = $this->user_session->getUserClassList();

		// debug
		// error_log(__METHOD__ . ' : class_list - ' . print_r($result['class_list'], true));

		// a paticular class is specified to be displayed as default
		if (!empty($params)) {
			list($subject_abbr, $course_num, $section_num) = $params;
			$result['default_class'] = $this->getClassInfo($subject_abbr, $course_num, $section_num);
		}

		$result['role'] = $this->user_session->getUserRole();

		$this->output = new ClassPageView($result);
	}
}
