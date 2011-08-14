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

		$this->addJQueryUI();
		$this->addJQueryUIPlugin('datetime');

		$this->addJS('model/logout.js');
		$this->addJS('model/task.js');
		$this->addJS('model/doc.js');
		$this->addJS('model/scrollbar.js');
		$this->addJS('model/book-suggest.js');
		$this->addJS('model/class-suggest.js');
		$this->addJS('timer.js');
		$this->addJS('model/calendar.js');
		$this->addJS('controller/calendar.js');
		$this->addJS('controller/navigation.js');
		$this->addCSS('dialog.css');
		$this->addCSS('panel.css');
		$this->addCSS('calendar.css');
		$this->addCSS('task.css');
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
			<div class="calendar body">
				<div class="body-inner">
					<div class="content">
						<div class="calendar panel-menu">
							<div class="panel-menu-inner">
								<form name="calendar-option-form" id="calendar-option">
									<input type="hidden" name="type" value="month" />
									<input type="hidden" name="timestamp" value="{$timestamp}" />
									<input type="hidden" name="begin" />
									<input type="hidden" name="end" />
									<input type="hidden" name="paginate" value="0" />
								</form>
								<ul>
									<li><a href="#" class="option month active">month</a></li>
									<li><a href="#" class="option week">week</a></li>
									<li><a href="#" class="option customized">4-day</a></li>
									<li><a href="#" class="option today">today</a></li>
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
								<div class="upload-form">
									<form class="hidden" id="doc-upload-form-skeleton" enctype="multipart/form-data" name="doc-upload" action="?q=doc-upload" method="post">
										<input type="hidden" name="token" />
										<input type="file" name="document" />
										<div class="error hidden"></div>
										<a class="button submit" href="#">submit</a>
									</form>
									<a class="button upload" href="#">upload syllabus</a>
								</div>
								<div class="task-create-form-wrapper">
									<form id="calendar-task-creation-form" class="task-create-form" action="task/create" method="post">
										<fieldset class="required">
											<legend>NEW to-do</legend>
											<input type="hidden" name="token" />
											<div class="required">
												<div class="row">
													<input type="text" name="objective" class="objective" value="reading due in the morning, etc" maxlength="72"/>
												</div>
											</div>
											<div class="additional hidden">
												<div class="row">
													<label for="due_date" class="title">Due: </label>
													<input type="text" name="due_date" id="time-picker" class="due_date" />
												</div>
												<div class="row">
													<label for="course-section" class="title">Class: </label>
													<input type="text" name="course-section" class="course-section"/>
													<input type="hidden" name="section_id" />
													<a href="#" class="button show-detail">more detail</a>
												</div>
												<div class="optional hidden">
													<div class="row">
														<label for="location" class="title">Place: </label>
														<input type="text" name="location" class="location"/>
													</div>
													<div class="row">
														<textarea name="description" rows="6" class="description">read chapter 12 to 13, etc</textarea>
													</div>
												</div>
												<a href="#" class="button submit">add</a>
											</div>
										</fieldset>
									</form>
								</div>
								<div id="calendar-task-list" class="task-list">
								</div>
								<a href="#" class="button more">more</a>
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
