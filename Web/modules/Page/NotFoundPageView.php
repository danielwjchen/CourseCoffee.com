<?php
/**
 * @file
 * Generate the NotFound page for visiters
 */
class NotFoundPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data = null) {
		parent::__construct($data);
		$this->setPageTitle('404 Page not found.');
		$this->addJQuery();
		$this->addCSS('Page/not-found');
	}

	/**
	 * Override View::getHeader()
	 */
	protected function getHeader() {
    header(self::NOT_FOUND);
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
		<div class="not-found body">
			<div class="body-inner">
				<h1>404 Not Found</h1>	
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
