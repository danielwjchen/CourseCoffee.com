<?php
/**
 * @file
 * Generate the welcoem page for visiters
 */
class PortalPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('portal');
		$this->addJS('model/login.js');
		$this->addJS('model/register.js');
		$this->addJS('model/doc.js');
		$this->addJS('controller/portal.js');
		$this->addCSS('dialog.css');
		$this->addCSS('portal.css');
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
<div class="portal container">
	<div class="container-inner">
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
									<option value="oakland.{$domain}">Oakland University</option>
									<option value="osu.{$domain}">Ohio State University</option>
									<option value="umich.{$domain}">University of Michigan</option>
									<option value="umflint.{$domain}">University of Michigan-Flint</option>
									<option value="um-dearborn.{$domain}">University of Michigan-Dearborn</option>
									<option value="udmercy.{$domain}">University of Detroit Mercy</option>
									<option value="wayne.{$domain}">Wayne State University</option>
								</select>
							</div>
						</form>
					</div>
					<h1>College made easy.</h1>
				</div>
			</div>
		</div>
		<div class="clear-fix"></div>
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
