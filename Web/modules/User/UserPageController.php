<?php
/**
 * @file
 * Handle user page related controller logics
 */
class UserPageController extends PageController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'getSignUpPage' => array(
				'sign-up',
			),
			'getUserCreatedPage' => array(
				'account-created',
			),
		);
	}

	/**
	 * Get user creation confirmation page
	 */
	public function getUserCreatedPage() {
		$this->redirectUnsupportedDomain();

		if (!$this->getUserId()) {
			$this->redirect(self::PAGE_WELCOME);
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
				$form_fields = $fb_model->generateSignUpForm($this->getRequestedDomain());
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

}
