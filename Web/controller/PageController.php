<?php
/**
 * @file
 * Manage some default output
 */

class PageController extends Controller implements ControllerInterface {

	private $output;

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
			'doc-edit'         => 'getDocumentEditorPage',
			'sign-up'          => 'getSignUpPage',
			'welcome'          => 'getWelcomePage',
			'home'             => 'getHomePage',
			'calendar'         => 'getCalendarPage',
			'class'            => 'getClassPage',
			'page-not-found'   => 'get404Page',
			'all-system-down'  => 'get500Page',
			'terms-of-use'     => 'getTermsOfUsePage',
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
		echo $this->output->render();
	}

	/**
	 * Get the welcome output
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

		$this->output = new WelcomePageView(array(
			'login_token'    => $login_form->initializeFormToken(),
			'register_token' => $register_form->initializeFormToken(),
			'file_token'     => $file_form->initializeFormToken()
		));
	}

	/**
	 * Get the home output for a user
	 */
	public function getHomePage() {
		$this->redirectUnknownUser();

		// debug
		// error_log(__METHOD__ . ' - user session - ' . print_r($_SESSION, true));

		$user_session_model = new UserSessionModel();
		$profile = $user_session_model->getUserProfile();
		$class_list = $user_session_model->getUserClassList();
		$this->output = new HomePageView(array(
			'fb_uid'     => $user_session_model->getFbUserId(),
			'user_id'    => $user_session_model->getUserId(),
			'profile'    => $profile,
			'class_list' => $class_list,
			'timestamp'  => time(),
		));
	}

	/**
	 * Get signup output for visiters
	 */
	public function getSignUpPage() {
		$section_id = Input::Get('section_id');
		$fb         = Input::Get('fb');
		$fb_uid     = Input::Get('fb_uid');
		$error      = null;

		if (!empty($section_id)) {
			Session::Set('section_id', $section_id);
		}

		if ($fb) {
			$fb_model = new FBModel();
			if (!$fb_model->checkFbUid($fb_uid)) {
				$form_fields = $fb_model->generateSignUpForm();
				$this->output = new FBSignUpPageView($form_fields);
				return ;
			} else {
				Logger::Write(FBModel::EVENT_FB_UID_TAKEN);
				$error = FBModel::ERROR_FB_UID_TAKEN;
			}
		}

		$user_register = new UserRegisterFormModel();
		$college       = new CollegeModel();
		$this->output = new SignUpPageView(array(
			'error'          => $error,
			'register_token' => $user_register->initializeFormToken(),
			'college_option' => $college->getCollegeOption(),
		));
	}

	/**
	 * Get the calendar output for a user
	 */
	public function getCalendarPage() {
		$this->redirectUnknownUser();

		$user_session_model = new UserSessionModel();
		$class_list    = $user_session_model->getUserClassList();
		$user_profile  = $user_session_model->getUserProfile(); 

		$this->output = new CalendarPageView(array(
			'user_id'    => $user_session_model->getUserId(),
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

		$this->output = new ClassPageView($result);
	}

	/**
	 * Get the 404 output
	 */
	public function get404Page() {
		$login_form = new UserLoginFormModel();
		$this->output = new NotFoundPageView(array(
			'login_token' => $login_form->initializeFormToken(),
		));
	}

	/**
	 * Get the 500 output
	 */
	public function get500Page() {
		$login_form = new UserLoginFormModel();
		$this->output = new InternalErrorPageView(array(
			'login_token' => $login_form->initializeFormToken(),
		));
	}

	/**
	 * Get the terms of use page() {
	 */
	public function getTermsOfUsePage() {
		$this->output = new TermsOfUsePageView(array());
	}

	/**
	 * Provide an interactive task editor
	 */
	public function getDocumentEditorPage() {
		$referrer  = $this->getReferrer();
		$processor = new DocumentProcessorFormModel();
		$college   = new CollegeModel();
		$document = Input::Get('document');
		$file_id  = Input::Get('file_id');
		$mime     = Input::Get('doc-type');
		$this->output = new DocumentEditorPageView(array(
			'process_state'   => $processor->getState($referrer),
			'document'        => $document,
			'file_id'         => $file_id,
			'mime'            => $mime,
			'college_option'  => $college->getCollegeOption(),
			'processor_token' => $processor->initializeFormToken(),
		));
	}

}
