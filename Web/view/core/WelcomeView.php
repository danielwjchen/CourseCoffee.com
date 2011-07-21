<?php
/**
 * @file
 * Generate the welcoem page for visiters
 */
class WelcomeView extends PageView implements ViewInterface{
	/**
	 * Implement ViewInterface::doView().
	 */
	public function doView() {
		include TEMPLATE_PATH . 'welcome.page.tpl';
	}
}
