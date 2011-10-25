<?php
/**
 * @file
 * Generate the registration page for visiters
 */
class FBSignUpPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('sign up with facebook');
		$this->addCSS('User/fb-signup');
	}

	/**
	 * Implement View::getHeader()
	 */
	protected function getHeader() {
    header(self::STATUS_OK);
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		return array(
			'header' => array(
				'callback' => 'LogoHeaderBlockView',
			),
			'legal' => array(
				'callback' => 'UserAgreementBlockView',
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
<div class="fb-sign-up container">
	<div class="container-inner">
		<div class="header">
			<div class="header-inner">
				{$header}
			</div>
		</div>
		<div class="body">
			<div class="body-inner">
				<div class="content"> 
					<fb:registration fields="{$fields}" redirect-uri="{$redirect}" width="530">
					</fb:registration>
					{$legal}
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
