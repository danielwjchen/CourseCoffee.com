<?php
/**
 * @file
 * Generate 401 error HTML
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
class UnauthorizedPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data = null) {
		parent::__construct($data);
		$this->setPageTitle('401 Unauthorized');
		$this->addJQuery();
		$this->addCSS('Page/forbidden');
	}

	/**
	 * Override View::getHeader()
	 */
	protected function getHeader() {
    header(self::UNAUTHORIZED);
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		return array(
			'header' => array(
				'callback' => 'LogoHeaderBlockView',
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
<div class="container">
	<div class="container-inner">
		<div class="header">
			<div class="header-inner">
				{$header}
			</div>
		</div>
		<div class="unauthrozied body">
			<div class="body-inner">
				<h1>401 Unauthorized</h1>	
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
