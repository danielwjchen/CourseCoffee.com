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

		$this->addJS('User/login-model');
		$this->addJS('User/register-model');
		$this->addJS('Document/doc-model');
		$this->addJS('Portal/portal-controller');
		$this->addCSS('CourseCoffee/dialog');
		$this->addCSS('Portal/portal');
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		return array(
			'institution_selection' => array(
				'callback' => 'InstitutionSelectionBlockView',
				'params' => array('institution_list', 'domain', 'selection_type'),
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
						{$institution_selection}
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
