<?php
/**
 * @file
 * Generate the welcoem page for visiters
 */
class WelcomePageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('welcome');
		$this->addJS('model/login.js');
		$this->addJS('model/register.js');
		$this->addJS('model/doc.js');
		$this->addJS('controller/welcome.js');
		$this->addCSS('dialog.css');
		$this->addCSS('welcome.css');
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		$header = array();
		if ($this->data['is_loggedIn']) {
			$header = array(
				'callback' => 'NavigationBlockView',
			);
		} else {
			$header = array(
				'callback' => 'VisitorNavigationBlockView',
				'params' => array('login_token'),
			);
		}
		return array(
			'header' => $header,
			'footer' => array(
				'callback' => 'FooterBlockView',
			),
		);
	}

	/**
	 * Implement PageViewInterface::getContent()
	 */
	public function getContent() {
		extract($this->data);
		return <<<HTML
<div class="welcome container">
	<div class="container-inner">
		<div class="header">
			<div class="header-inner">
			</div>
		</div>
		<div class="body">
			<div class="body-inner">
				<div class="select-school">
					<img src="/images/logo.png" class="logo" />
					<div class="select-school-inner">
						<form id="select-school-form" name="select-school">
							<div class="row">
								<select id="school-options">
									<option value="default">Please select school.</option>
									<option value="msu.{$domain}">Michigan State University</option>
									<option value="umich.{$domain}">University of Michigan</option>
								</select>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="footer">
	<div class="footer-inner">
		{$footer}
	</div>
</div>
HTML;
	}
}
