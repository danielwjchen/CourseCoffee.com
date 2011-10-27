<?php
/**
 * @file
 * Manage curriculum related controller logic
 */
class CurriculumController extends CourseCoffeeController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'class'    => 'getClassPage',
			'calendar' => 'getCalendarPage',
			'college-class-add'     => 'createClass',
			'college-class-update'  => 'updateClass',
			'college-class-remove'  => 'removeClass',
			'college-class-info'    => 'getClassDetail',
			'college-class-list'    => 'getClassListBySyllabusStatus',
			'college-class-suggest' => 'suggestClass',
			'college-class-enroll'  => 'enrollClass',
		);
	}

	/**
	 * Create a new class
	 *
	 * @to-do
	 */
	public function createClass() {
	}

	/**
	 * Update class's information
	 *
	 * @to-do
	 */
	public function updateClass() {
	}

	/**
	 * Remove a class
	 */
	public function removeClass() {
		$user_id    = $this->getUserId();
		$section_id = Input::Post('section_id');
		$enroll = new UserEnrollClassModel($this->sub_domain);
		$result = $enroll->removeUserFromClass($user_id, $section_id);
		$class_list = $this->user_session->getUserClassList();
		unset($class_list[$section_id]);
		$this->user_session->setUserClassList($class_list);
		$this->output = new JSONView($result);
	}

	/**
	 * Get detail of a class()
	 */
	public function getClassDetail() {
		$section_id = Input::Post('section_id');
		$class_model = new CurriculumClassModel($this->sub_domain);
		$result = $class_model->getClassById($section_id);
		$this->output = new JSONView($result);

	}

	/**
	 * Get a list of class based on syllabus status
	 */
	public function getClassListBySyllabusStatus() {
		$list_model = new CurriculumClassListModel($this->sub_domain);
		$result = $list_model->getClassListBySyllabusStatus(
			Input::Post('status'), 
			Input::Post('timestamp'), 
			Input::Post('paginate')
		);
		$this->output = new JSONView($result);
	}

	/**
	 * Suggest a list of classes based on user input
	 */
	public function suggestClass() {
		$user_setting = $this->user_session->GetUserSetting();
		if (empty($user_setting)) {
			$institution_id = Input::Post('institution_id');
			$year_id        = Input::Post('year_id');
			$term_id        = Input::Post('term_id');

		} else {
			$institution_id = $user_setting['institution_id'];
			$year_id        = $user_setting['year_id'];
			$term_id        = $user_setting['term_id'];
		}
		$string         = Input::Post('term');

		// debug
		// error_log(__METHOD__ . print_r($user_setting, true));


		$class_suggestion = new CurriculumClassListModel($this->sub_domain);

		$result = $class_suggestion->suggestClassList(
			$institution_id,
			$year_id,
			$term_id,
			$string
		);

		$this->output = new JSONView($result);
	}

	/**
	 * Add a user to a class
	 */
	public function enrollClass() {
		$user_id = $this->getUserId();
		if (empty($user_id)) {
			$this->output = new JSONView(array('redirect' => '/welcome'));

		} else {
			$section_id = Input::Post('section_id');
			$enroll = new UserEnrollClassModel($this->sub_domain);
			$result = $enroll->AddUserToClass($user_id, $section_id);

			// debug
			// error_log(__METHOD__ . ' : result - ' . print_r($result, true));

			// rebuild user_class_list in session
			if (isset($result['success'])) {
				$class_list = $this->user_session->getUserClassList();
				$class_list[$section_id] = $result['section_code'];

				// debug
				// error_log(__METHOD__ . ' : class_list - ' . print_r($class_list, true));

				$this->user_session->setUserClassList($class_list);
			} elseif (isset($result['error'])) {
				switch ($result['error']) {
					case 'exceed_max':
						$result['class_list'] = $this->user_session->getUserClassList();
						break;
					case 'already_enrolled':
						break;
					default:
				}
			}

			$this->output = new JSONView($result);
		}

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
	 * Get the class output for a user
	 *
	 * @param array $params
	 *  optional, but when presnet it expects values to be in the following order
	 *  - subject_abbr
	 *  - course_num
	 *  - section_num
	 */
	public function getClassPage($params = array()) {
		$this->redirectUnknownUser();

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

	/**
	 * Get the calendar output for a user
	 */
	public function getCalendarPage() {
		$this->redirectUnknownUser();

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

}
