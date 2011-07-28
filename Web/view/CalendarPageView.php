<?php
/**
 * @file
 * Generate the Calendar page for visiters
 */
class CalendarPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($content = null) {
		parent::__construct($content);
		$this->setPageTitle('calendar');
		$this->addJS('model/logout.js');
		$this->addJS('controller/calendar.js');
		$this->addJS('controller/navigation.js');
		$this->addCSS('calendar.css');
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
		$this->setHeader(self::HTML_HEADER);
		extract($this->data);
		return <<<HTML
	<div class="calendar container">
		<div class="container-inner">
			<div class="header">
				<div class="header-inner">
					<ul id="navigation-menu">
						<li class="home">
							<a class="home button" href="/home">Home</a>
						</li>
						<li class="calendar active">
							<a class="calendar button" href="/calendar">Calendar</a>
						</li>
						<li class="class">
							<a class="class button" href="/class">Class</a>
						</li>
						<li class="logout">
							<a class="logout button" href="#">logout</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="calendar body">
				<div class="body-inner">
					{$body['block']}
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
