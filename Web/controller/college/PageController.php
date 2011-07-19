<?php
/**
 * @file
 * Manage some default page
 */

class PageController extends Controller implements ControllerInterface {

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
		);
	}

	/**
	 * Get the welcome page
	 */
	public function getWelcomePage() {
	}

	/**
	 * Get the home page for a user
	 */
	public function getHomePage() {
	}

	/**
	 * Get the calendar page for a user
	 */
	public function getCalendarPage() {
	}

	/**
	 * Get the class page for a user
	 */
	public function getClassPage() {
	}

}
