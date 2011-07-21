<?php
/**
 * @file
 * Generate the welcoem page for visiters
 */
class WelcomePageView extends PageView implements ViewInterface{
	/**
	 * Implement ViewInterface::doView().
	 */
	public function doView() {
		$this->addJS('welcome.js');
		$this->addCSS('welcome.css');
	}
}
