<?php
/**
 * @file
 * Generate the book search page
 */
class BookSearchPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('book search');
		$this->addJqueryUI();
		$this->addJS('model/login.js');
		$this->addJS('model/book-suggest.js');
		$this->addJS('model/register.js');
		$this->addJS('model/doc.js');
		$this->addJS('model/class-suggest.js');
		$this->addJS('model/book-search.js');
		$this->addJS('controller/book-search.js');
		$this->addCSS('dialog.css');
		$this->addCSS('book-search.css');
		$this->addCSS('book-list.css');
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		$header = array();
		if ($this->data['is_loggedIn']) {
			$header = array(
				'SimplifiedNavigationBlockView',
			);
		} else {
			$header = array(
				'VisitorNavigationBlockView',
			);
		}
		return array(
			'header' => $header,
			'footer' => array(
				'FooterBlockView',
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
			{$header}
			<div class="body">
				<div class="body-inner">
					<div class="search-block">
						<img src="images/logo.png" class="logo" />
						<div class="class-suggest-inner">
							<form id="class-suggest-form" name="class-suggest" action="college-class-suggest" method="post">
								<input type="hidden" name="term_id" value="1" />
								<input type="hidden" name="year_id" value="1" />
								<input type="hidden" id="section-id" name="section_id" />
								<input type="hidden" id="section-id" name="section_id" />
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
