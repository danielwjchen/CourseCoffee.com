<?php
/**
 * @file
 * Generate the Home page for visiters
 */
class HomePageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('home');

		// get date-time selector
		$this->addJQueryUI();
		$this->addJQueryUIPlugin('datetime');

		$this->addJS('model/logout.js');
		$this->addJS('model/task.js');
		$this->addJS('model/doc.js');
		$this->addJS('controller/home.js');
		$this->addJS('controller/navigation.js');
		$this->addCSS('dialog.css');
		$this->addCSS('home.css');
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
<div class="home container">
	<div class="container-inner">
		<div class="header">
			<div class="header-inner">
				<ul id="navigation-menu">
					<li class="home active">
						<a class="home button" href="/home">Home</a>
					</li>
					<li class="calendar">
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
		<div class="body">
			<div class="body-inner">
				<div class="content">
					<div class="panel-top">
						<div class="panel-inner">
							<div class="profile">
								<img src="images/default-profile.png" />
								<div class="name">Joe Smith</div>
								<div class="school">Michigan State University</div>
								<div class="achievement">
									<div><span class="karma">+100 </span><strong>karma</strong></div>
								</div>
							</div>
							<div class="upload-form">
								<form class="hidden" id="doc-upload-form-skeleton" enctype="multipart/form-data" name="doc-upload" action="?q=doc-upload" method="post">
									<input type="hidden" name="token" value="{$file_token}" />
									<input type="file" name="document" />
									<div class="error hidden"></div>
									<a class="button submit" href="#">submit</a>
								</form>
								<a class="button upload" href="#">upload</a>
							</div>
							<div class="task-create-form-wrapper">
								<form id="new-task-form" class="task-create-form" action="task/create" method="post">
									<fieldset class="required">
										<legend>NEW to-do</legend>
										<input type="hidden" name="token" value="{$form['task_token']}" />
										<div class="required">
											<div class="row">
												<input type="text" name="objective" class="objective" value="reading due in the morning, etc" maxlength="72"/>
											</div>
										</div>
										<div class="additional hidden">
											<div class="row">
												<label for="due_date" class="title">Due: </label>
												<input type="text" name="due_date" id="time-picker" class="due_date" />
												<label for="course-section" class="title">Class: </label>
												<input type="text" name="course-section" class="course-section"/>
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
						</div>
					</div>
					<div class="panel-01 panel-class">
						<h1>To-dos</h1>
						<form id="user-list-task-option" action="user/list-task" method="post">
						</form>
						<div class="panel-inner">
						</div>
					</div>
					<div class="panel-02">
						<h1>Class Feed</h1>
						<div class="panel-inner">
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
