<?php
/**
 * @file
 * Generate the welcoem page for visiters
 */
class WelcomePageView extends PageView {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($content = null) {
		parent::__construct($content);
		$this->content['body']['css'] = 'welcome';
		$this->addJS('model/login.js');
		$this->addJS('model/registration.js');
		$this->addJS('controller/welcome.js');
		$this->addJS('controller/navigation.js');
		$this->addCSS('welcome.css');
	}
}
