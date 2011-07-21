<?php
/**
 * @file
 * Manage some default page
 */

class PageController extends Controller implements ControllerInterface {

	private $page_view;

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
			'welcome' => 'getWelcomePage',
			'home' => 'getHomePage',
			'calendar' => 'getCalendarPage',
			'class' => 'getClassPage',
			'page-not-found' => 'get404Page',
			'all-system-down' => 'get500Page',
		);
	}

	/**
	 * Implement ControllerInterface::afterAction()
	 */
	public function afterAction() {
		$this->page_view->setHeader(PageView::HTML_HEADER);
		echo $this->page_view->render();
	}

	/**
	 * Get the welcome page
	 */
	public function getWelcomePage() {
		$block_content = '';
		$block = new WelcomeBlockView($block_content);
		$page_content['body']['block'] = $block->render();
		$login_model = new UserLoginFormModel();
		$page_content['header']['block']['login_token'] = $login_model->initializeFormToken();
		$this->page_view = new WelcomePageView($page_content);
	}

	/**
	 * Get the home page for a user
	 */
	public function getHomePage() {
		$this->page_view = new HomePageView();
	}

	/**
	 * Get the calendar page for a user
	 */
	public function getCalendarPage() {
		$this->page_view = new CalendarPageView();
	}

	/**
	 * Get the class page for a user
	 */
	public function getClassPage() {
		$this->page_view = new ClassPageView();
	}

	/**
	 * Get the 404 page
	 */
	public function get404Page() {
		$this->page_view = new NotFoundPageView();
	}

	/**
	 * Get the 500 page
	 */
	public function get500Page() {
		$this->page_view = new InternalErrorPageView();
	}
}
