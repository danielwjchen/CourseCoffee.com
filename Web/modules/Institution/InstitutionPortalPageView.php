<?php
/**
 * @file
 * Generate the portal page and redirect visiters to their respective instituion
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
class InstitutionPortalPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('portal');
		$this->addJS('Institution/institution-portal');
		$this->addCSS('Institution/institution-portal');
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		return array(
			'domain_selector' => array(
				'callback' => 'InstitutionDomainOptionBlockView',
				'params'   => array('institution_list', 'domain'),
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
