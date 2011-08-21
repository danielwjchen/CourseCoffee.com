<?php
/**
 * @file
 * Manage access to information of a class in college
 */
class CollegeClassController extends Controller implements ControllerInterface {

	private $json;

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
			'college-class-add'     => 'createClass',
			'college-class-update'  => 'updateClass',
			'college-class-remove'  => 'removeClass',
			'college-class-info'  => 'getClassDetail',
			'college-class-list'    => 'getListOfClass',
			'college-class-suggest' => 'suggestClass',
			'college-class-enroll'  => 'enrollClass',
			'college-class-reading' => 'getClassBookList',
		);
	}

	/**
	 * Implement ControllerInterface::afterAction()
	 */
	public function afterAction() {
		$this->json->setHeader(PageView::HTML_HEADER);
		echo $this->json->render();
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
	 *
	 * @to-do
	 */
	public function removeClass() {
	}

	/**
	 * Get detail of a class()
	 */
	public function getClassDetail() {
		$section_id = Input::Post('section_id');
		$class_model = new CollegeClassModel();
		$result = $class_model->getClassById($section_id);
		$this->json = new JSONView($result);

	}

	/**
	 * Get a list of class
	 */
	public function getListOfClass() {
	}

	/**
	 * Suggest a list of classes based on user input
	 */
	public function suggestClass() {
		/*
		$institution_id = Input::Post('institution_id');
		$year_id        = Input::Post('year_id');
		$term_id        = Input::Post('term_id');
		*/
		$institution_id = 1;
		$year_id        = 1;
		$term_id        = 1;
		$string         = Input::Post('term');

		$class_suggestion = new CollegeClassListModel();

		$result = $class_suggestion->suggestClassList(
			$institution_id,
			$year_id,
			$term_id,
			$string
		);

		$this->json = new JSONView($result);
	}

	/**
	 * Add a user to a class
	 */
	public function enrollClass() {
		$user_session_model = new UserSessionModel();
		$user_id = $user_session_model->getUserId();
		if (empty($user_id)) {
			$this->json = new JSONView(array('redirect' => '/welcome'));

		} else {
			$section_id = Input::Post('section_id');
			$enroll = new UserEnrollClassModel();
			$result = $enroll->AddUserToClass($user_id, $section_id);

			// debug
			// error_log(__METHOD__ . ' : result - ' . print_r($result, true));

			// rebuild user_class_list in session
			if (isset($result['success'])) {
				$class_list = $user_session_model->getUserClassList();
				$class_list[$section_id] = $result['section_code'];

				// debug
				// error_log(__METHOD__ . ' : class_list - ' . print_r($class_list, true));

				$user_session_model->setUserClassList($class_list);
			}

			$this->json = new JSONView($result);
		}

	}

	/**
	 * Get book list for a class
	 */
	public function getClassBookList() {
		$section_id = Input::Post('section_id');
		$list = new BookSuggestModel();
		$result = $list->getBookList($section_id);
		$this->json = new JSONView($result);
	}

}
