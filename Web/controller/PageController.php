<?php
/**
 * @file
 * Manage some default page
 */

class PageController extends Controller implements ControllerInterface {

	private $page;

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
			'doc-edit'         => 'getDocumentEditorPage',
			'sign-up'          => 'getSignUpPage',
			'facebook-sign-up' => 'getFacebookSignUpPage',
			'welcome'          => 'getWelcomePage',
			'home'             => 'getHomePage',
			'calendar'         => 'getCalendarPage',
			'class'            => 'getClassPage',
			'page-not-found'   => 'get404Page',
			'all-system-down'  => 'get500Page',
		);
	}

	/**
	 * Override Controller::beforeAction()
	 */
	public function beforeAction() {

	}

	/**
	 * Implement ControllerInterface::afterAction()
	 */
	public function afterAction() {
		echo $this->page->render();
	}

	/**
	 * Get the welcome page
	 *
	 * we redirect if the user is logged in
	 */
	public function getWelcomePage() {
		if ($this->isUserLoggedIn()) {
			header('Location: ' . self::PAGE_HOME);
		}
		$login_form    = new UserLoginFormModel();
		$register_form = new UserRegisterFormModel();
		$file_form     = new FileFormModel();

		$this->page = new WelcomePageView(array(
			'login_token'    => $login_form->initializeFormToken(),
			'register_token' => $register_form->initializeFormToken(),
			'file_token'     => $file_form->initializeFormToken()
		));
	}

	/**
	 * Get the home page for a user
	 */
	public function getHomePage() {
		$this->redirectUnknownUser();

		// debug
		// error_log(__METHOD__ . ' - user session - ' . print_r($_SESSION, true));

		$user_session_model = new UserSessionModel();
		$profile = $user_session_model->getUserProfile();
		$class_list = $user_session_model->getUserClassList();
		$this->page = new HomePageView(array(
			'profile'    => $profile,
			'class_list' => $class_list,
		));
	}

	/**
	 * Get signup page for visiters
	 */
	public function getSignUpPage() {
		$user_register = new UserRegisterFormModel();
		$college       = new CollegeModel();
		$this->page = new SignUpPageView(array(
			'register_token' => $user_register->initializeFormToken(),
			'college_option' => $college->getCollegeOption(),
		));
	}

	/**
	 * Get facebook signup page for visiters
	 */
	public function getFacebookSignUpPage() {
		$this->page = new FacebookSignUpPageView(array());
	}

	/**
	 * Get the calendar page for a user
	 */
	public function getCalendarPage() {
		$this->redirectUnknownUser();

		$user_session_model = new UserSessionModel();
		$class_list = $user_session_model->getUserClassList();

		$this->page = new CalendarPageView(array(
			'timestamp' => time(),
			'class_list' => $class_list,
		));
	}

	/**
	 * Get the class page for a user
	 *
	 * @param array $params
	 *  optional, but when presnet it expects values to be in the following order
	 *  - institution_uri
	 *  - year
	 *  - term
	 *  - subject_abbr
	 *  - course_num
	 *  - section_num
	 */
	public function getClassPage($params = array()) {
		$this->redirectUnknownUser();

		$user_session_model = new UserSessionModel();
		$result['class_list'] = $user_session_model->getUserClassList();

		// debug
		// error_log(__METHOD__ . ' : class_list - ' . print_r($result['class_list'], true));

		// a paticular class is specified to be displayed as default
		if (!empty($params)) {
			$class = new CollegeClassModel();
			list($instituion_uri, $year, $term, $subject_abbr, $course_num, $section_num) = $params;
			$class_info = $class->getClassByURI($instituion_uri, $year, $term, $subject_abbr, $course_num, $section_num);

			// debug 
			// error_log(__METHOD__ . ' : class_info - ' . print_r($class_info, true));

			$result['default_class'] = $class_info;

		}

		$this->page = new ClassPageView($result);
	}

	/**
	 * Get the 404 page
	 */
	public function get404Page() {
		$login_form = new UserLoginFormModel();
		$this->page = new NotFoundPageView(array(
			'login_token' => $login_form->initializeFormToken(),
		));
	}

	/**
	 * Get the 500 page
	 */
	public function get500Page() {
		$login_form = new UserLoginFormModel();
		$this->page = new InternalErrorPageView(array(
			'login_token' => $login_form->initializeFormToken(),
		));
	}

	/**
	 * Provide an interactive task editor
	 */
	public function getDocumentEditorPage() {
		$processor = new DocumentProcessorFormModel();
		$college   = new CollegeModel();
		$document = Input::Get('document');
		$mime     = Input::Get('doc-type');
		$this->page = new DocumentEditorPageView(array(
			'document'        => $document,
			'mime'            => $mime,
			'college_option' => $college->getCollegeOption(),
			'processor_token' => $processor->initializeFormToken(),
		));
	}

}
