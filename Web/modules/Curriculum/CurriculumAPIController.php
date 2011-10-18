<?php
/**
 * @file
 * Handle curriculum API related controller logics
 */
class CurriculumAPIController extends APIController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'createClass' => array(
				'curriculum/class/add',
			),
			'updateClass' => array(
				'curriculum/class/update',
			),
			'removeClass' => array(
				'curriculum/class/remove',
			),
			'getClassDetail' => array(
				'curriculum/class/info',
			),
			'getClassListBySyllabusStatus' => array(
				'curriculum/class/list',
			),
			'suggestClass' => array(
				'curriculum/class/suggest',
			),
			'enrollClass' => array(
				'curriculum/class/enroll',
			),
			'getClassBookList' => array(
				'curriculum/class/reading',
			),
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
		$class_model = new CollegeClassModel($this->sub_domain);
		$result = $class_model->getClassById($section_id);
		$this->output = new JSONView($result);

	}

	/**
	 * Get a list of class based on syllabus status
	 */
	public function getClassListBySyllabusStatus() {
		$list_model = new CollegeClassListModel($this->sub_domain);
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


		$class_suggestion = new CollegeClassListModel($this->sub_domain);

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
	 * Get book list for a class
	 */
	public function getClassBookList() {
		$section_id = Input::Post('section_id');
		$list  = new BookSuggestModel($this->sub_domain);
		$class = new CollegeClassModel($this->sub_domain);
		$class_info = $class->getClassById($section_id);
		$result = $list->getBookList($section_id);
		$result['uri'] = $class_info['content']['subject_abbr'] . '/' . 
			$class_info['content']['course_num'] . '/' . 
			$class_info['content']['section_num'];
		$this->output = new JSONView($result);
	}

}
