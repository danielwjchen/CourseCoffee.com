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
			'welcome'         => 'getWelcomePage',
			'home'            => 'getHomePage',
			'calendar'        => 'getCalendarPage',
			'class'           => 'getClassPage',
			'page-not-found'  => 'get404Page',
			'all-system-down' => 'get500Page',
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
		$this->page->setHeader(PageView::HTML_HEADER);
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
		$file_form   = new FileFormModel();
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
		$create_form = new TaskCreateFormModel();
		$block_content['form']['task_token'] = $create_form->initializeFormToken();
		$block = new HomeBlockView($block_content);
		$page_content['body']['block'] = $block->render();
		$this->page = new HomePageView($page_content);
	}

	/**
	 * Get the calendar page for a user
	 */
	public function getCalendarPage() {
		$this->redirectUnknownUser();
		$this->page = new CalendarPageView();
	}

	/**
	 * Get the class page for a user
	 */
	public function getClassPage() {
		$this->redirectUnknownUser();
		$this->page = new ClassPageView();
	}

	/**
	 * Get the 404 page
	 */
	public function get404Page() {
		$this->page = new NotFoundPageView();
	}

	/**
	 * Get the 500 page
	 */
	public function get500Page() {
		$this->page = new InternalErrorPageView();
	}
}
