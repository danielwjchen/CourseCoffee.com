<?php
/**
 * @file
 * Manage some default output
 */

class PageController extends Controller implements ControllerInterface {

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
			'doc-edit'        => 'getDocumentEditorPage',
			'sign-up'         => 'getSignUpPage',
			// We decided to use /book-search as default page 8/31/2011
			// 'welcome'         => 'getWelcomePage',
			'welcome'         => 'getBookSearchPage',
			'home'            => 'getHomePage',
			'calendar'        => 'getCalendarPage',
			'class'           => 'getClassPage',
			'task'            => 'getTaskPage',
			'account-created' => 'getUserCreatedPage',
			'book-search'     => 'getBookSearchPage',
			'page-not-found'  => 'get404Page',
			'all-system-down' => 'get500Page',
			'terms-of-use'    => 'getTermsOfUsePage',
			'privacy-policy'  => 'getPrivacyPage',
			'how-to-find-syllabus' => 'getTutorialPage',
		);
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

		$login_form    = new UserLoginFormModel($this->sub_domain);
		$register_form = new UserRegisterFormModel($this->sub_domain);
		$file_form     = new FileFormModel($this->sub_domain);

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
		$this->redirectUnsupportedDomain();
		$this->redirectUnknownUser();

		// debug
		// error_log(__METHOD__ . ' - user session - ' . print_r($_SESSION, true));

		$user_session_model = new UserSessionModel($this->sub_domain);
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
		$this->redirectUnsupportedDomain();
		$institution_id = $this->getInstitutionId();

		$section_id = Input::Get('section_id');
		$fb         = Input::Get('fb');
		$fb_uid     = Input::Get('fb_uid');
		$error      = null;

		if (!empty($section_id)) {
			Session::Set('section_id', $section_id);
		}

		if ($fb) {
			$fb_model = new FBModel($this->sub_domain);
			if (!$fb_model->checkFbUid($fb_uid)) {
				$form_fields = $fb_model->generateSignUpForm();
				$this->output = new FBSignUpPageView($form_fields);
				return ;
			} else {
				Logger::Write(FBModel::EVENT_FB_UID_TAKEN);
				$error = FBModel::ERROR_FB_UID_TAKEN;
			}
		}

		$user_register = new UserRegisterFormModel($this->sub_domain);
		$college       = new CollegeModel($this->sub_domain);
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

		$user_session_model = new UserSessionModel($this->sub_domain);
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

		$result['class_list'] = $this->user_session->getUserClassList();

		// debug
		// error_log(__METHOD__ . ' : class_list - ' . print_r($result['class_list'], true));

		// a paticular class is specified to be displayed as default
		if (!empty($params)) {
			$class = new CollegeClassModel($this->sub_domain);
			list($subject_abbr, $course_num, $section_num) = $params;
			$class_info = $class->getClassBySectionCode($subject_abbr, $course_num, $section_num);

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
		$login_form = new UserLoginFormModel($this->sub_domain);
		$this->output = new NotFoundPageView(array(
			'login_token' => $login_form->initializeFormToken(),
		));
	}

	/**
	 * Get the 500 output
	 */
	public function get500Page() {
		$login_form = new UserLoginFormModel($this->sub_domain);
		$this->output = new InternalErrorPageView(array(
			'login_token' => $login_form->initializeFormToken(),
		));
	}

	/**
	 * Get the terms of use page
	 */
	public function getTermsOfUsePage() {
		$this->output = new TermsOfUsePageView(array());
	}

	/**
	 * Get the tutorial page
	 */
	public function getTutorialPage() {
		$this->output = new TutorialPageView(array());
	}


	/**
	 * Get the privacy page
	 */
	public function getPrivacyPage() {
		$this->output = new PrivacyPolicyPageView(array());
	}

	/**
	 * Get the book search page
	 */
	public function getBookSearchPage() {
		if ($this->sub_domain == self::DEFAULT_DOMAIN) {
			$this->output = new WelcomePageView(array(
				'domain'      => $this->domain,
			));
		} else {
			$this->redirectUnsupportedDomain();

			$login_form = new UserLoginFormModel($this->sub_domain);
			$this->output = new BookSearchPageView(array(
				'is_loggedIn' => $this->getUserId(),
				'login_token' => $login_form->initializeFormToken(),
			));
		}
	}

	/**
	 * Get user creation confirmation page
	 */
	public function getUserCreatedPage() {
		$this->redirectUnsupportedDomain();

		if (!$this->getUserId()) {
			$this->redirect(self::PAGE_DEFAULT);
		}

		if (!$this->user_session->isNewlyRegistered()) {
			$this->redirect(self::PAGE_HOME);
		}

		$profile = $this->user_session->getUserProfile();

		$this->output = new UserCreationConfirmPageView(array(
			'first_name' => $profile['first_name'],
			'account'      => $profile['account'],
		));
	}

	/**
	 * Get task page
	 */
	public function getTaskPage() {
		$this->redirectUnsupportedDomain();

		$this->output = new BookSearchPageView(array(
			'is_loggedIn' => $this->getUserId(),
		));
	}

	/**
	 * Provide an interactive task editor
	 */
	public function getDocumentEditorPage() {
		$this->redirectUnsupportedDomain();

		$processor = new DocumentProcessorFormModel($this->sub_domain);
		$process_state = $this->isUserLoggedIn() ? 'redirect' : 'sign-up';
		$college    = new CollegeModel($this->sub_domain);
		$document     = Input::Get('document');
		$file_id      = Input::Get('file_id');
		$mime         = Input::Get('mime');
		$section_id   = Input::Get('section_id');
		$section_code = null;
		if (!empty($section_id)) {
			$college_class = new CollegeClassModel($this->sub_domain);
			$result = $college_class->getClassById($section_id);
			if (isset($result['content'])) {
				$section_code = $result['content']['section_code'];
			}
		}
		$this->output = new DocumentEditorPageView(array(
			'process_state'   => $process_state,
			'document'        => $document,
			'file_id'         => $file_id,
			'section_id'      => $section_id,
			'section_code'    => $section_code,
			'mime'            => $mime,
			'processor_token' => $processor->initializeFormToken(),
		));
	}

}
