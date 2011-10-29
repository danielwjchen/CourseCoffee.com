<?php
/**
 * @file
 * Generate the book search page
 */
class ItemSearchPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('item search');
		$this->addJqueryUI();

		$this->addJS('Item/book-suggest-model');
		$this->addJS('Document/doc-model');
		$this->addJS('Item/book-search-model');
		$this->addJS('Item/book-search-controller');
		$this->addCSS('CourseCoffee/dialog');
		$this->addCSS('Item/book-search');
		$this->addCSS('Item/book-list');
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		$header = array();
		if ($this->data['is_loggedIn']) {
			$header = array(
				'callback' => 'NavigationBlockView',
				'params'   => array('role'),
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
<div class="book-search container">
	<div class="container-inner">
		<div class="header">
			<div class="header-inner">
				{$header}
			</div>
		</div>
		<div class="system-message hidden">
			<div class="system-message-inner">
			</div>
		</div>
			<div class="body">
				<div class="body-inner">
					<div class="search-block">
						<img src="/images/logo.png" class="logo" />
						<div class="class-suggest-inner">
							<form id="book-suggest-form" name="book-suggest">
								<input type="hidden" name="section_id" value="{$section_id}" />
								<div class="row">
									<input type="text" name="string" id="suggest-input" value="enter you class, e.g. CSE 231 001" />
								</div>
								<div class="row">
									<a class="button suggest" href="#">book search</a>
								</div>
							</form>
						</div>
					</div>
					<div class="content hidden">
						<div id="book-suggest-list" class="book-list"></div>
					</div>
					<div class="search-message">
						<h1>We do more than finding book deals. <span class="underline"><a href="#" class="upload">Try the syllabus organizer</a></span>.</h1>
					</div>
					<div class="social">
						<div href="{$base_url}" class="fb-like" data-send="false" data-width="450" data-show-faces="false"></div>
						<g:plusone size="medium"></g:plusone>
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
