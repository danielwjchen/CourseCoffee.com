<?php
/**
 * @file
 * Generate the welcoem page for visiters
 */
class InternalErrorPageView extends PageView {
	/**
	 * Implement ViewInterface::doView().
	 */
	public function doView() {
		$this->addJS('welcome.js');
		$this->addCSS('welcome.css');
	}
}
