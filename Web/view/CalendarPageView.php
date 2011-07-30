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
		$this->addJS('model/calendar.js');
		$this->addJS('controller/calendar.js');
		$this->addJS('controller/navigation.js');
		$this->addCSS('panel.css');
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
					<div class="content">
						<div class="calendar panel-menu">
							<div class="panel-menu-inner">
								<form name="calendar-option" id="calendar-option-menu">
									<input type="hidden" name="type" value="today" />
									<input type="hidden" name="timestamp" value="{$timestamp}" />
								</form>
								<ul>
									<li><a href="#" class="option today active">today</a></li>
									<li><a href="#" class="option customized">4-day</a></li>
									<li><a href="#" class="option week">week</a></li>
									<li><a href="#" class="option month">month</a></li>
								</ul>
							</div>
						</div>
						<div class="panel-01">
							<div class="panel-inner">
								<div class="calendar-display">
								</div>
							</div>
						</div>
						<div class="panel-02">
							<div class="panel-inner">
							</div>
						</div>
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
