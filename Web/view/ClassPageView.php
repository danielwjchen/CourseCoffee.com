<?php
/**
 * @file
 * Generate the Class page for visiters
 */
class ClassPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->addJQueryUI();
		$this->addJQueryUIPlugin('datetime');

		$this->setPageTitle('class');
		$this->addJS('model/logout.js');
		$this->addJS('model/book-suggest.js');
		$this->addJS('model/class-suggest.js');
		$this->addJS('controller/class.js');
		$this->addJS('controller/navigation.js');
		$this->addCSS('dialog.css');
		$this->addCSS('class.css');
		$this->addCSS('book-list.css');
	}

	/**
	 * Implement View::getHeader()
	 */
	protected function getHeader() {
    header(self::STATUS_OK);
	}

	/**
	 * Implement PageViewInterface::getContent()
	 */
	public function getContent() {
		extract($this->data);
		return <<<HTML
<div class="class container">
	<div class="container-inner">
		<div class="header">
			<div class="header-inner">
				<ul id="navigation-menu">
					<li class="home">
						<a class="home button" href="/home">Home</a>
					</li>
					<li class="calendar">
						<a class="calendar button" href="/calendar">Calendar</a>
					</li>
					<li class="class active">
						<a class="class button" href="/class">Class</a>
					</li>
					<li class="class-suggest">
						<div class="class-suggest-inner">
							<form id="class-suggest-form" name="class-suggest" action="college-class-suggest" method="post">
								<input type="text" name="string" id="suggest-input" value="e.g. CSE231001" />
								<input type="hidden" id="section-id" name="section_id" />
								<a class="button suggest" href="#">add</a>
							</form>
					</li>
					<li class="logout">
						<a class="logout button" href="#">logout</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="class body">
			<div class="body-inner">
				<div class="content">
					{$body['block']}
				</div>
			</div>
		</div>
	</div>
</div>
<div class="footer">
	<div class="footer-inner">
		{$footer['block']}
	</div>
</div>
HTML;
	}
}
