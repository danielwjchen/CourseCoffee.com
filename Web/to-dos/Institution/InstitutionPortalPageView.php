<?php
/**
 * @file
 * Generate the portal page and redirect visiters to their respective instituion
 */
class InstitutionPortalPageView extends PageView implements PageViewInterface {

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
			'domain_selector' => array(
				'callback' => 'InstitutionDomainOptionBlockView',
				'params'   => array('domain'),
			),
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
						{$domain_selector}
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
