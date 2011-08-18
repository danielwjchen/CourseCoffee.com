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
		$this->addJS('model/panel.js');
		$this->addJS('model/task.js');
		$this->addJS('model/to-do.js');
		$this->addJS('model/doc.js');
		$this->addJS('model/book-suggest.js');
		$this->addJS('model/class-suggest.js');
		$this->addJS('model/class-enroll.js');
		$this->addJS('controller/home.js');
		$this->addJS('controller/navigation.js');
		$this->addJS('timer.js');
		$this->addCSS('dialog.css');
		$this->addCSS('task.css');
		$this->addCSS('home.css');
		$this->addCSS('book-list.css');
	}

	/**
	 * Implement PageViewInterface::getContent()
	 */
	public function getContent() {
		extract($this->data);
		$option = '';
		foreach ($class_list as $section_id => $section_code) {
			$option .= "<option value='{$section_id}'>{$section_code}</option>";
		}
		$class_select = <<<HTML
<select name="section_id" class="class-list">
	<option selected="selected">pick a class</option>
	{$option}
</select>
HTML;
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
		<div class="body">
			<div class="body-inner">
				<div class="content">
					<div class="panel-top">
						<div class="panel-inner">
							<div class="profile">
								<img src="images/default-profile.png" />
								<div class="name">{$profile['first_name']} {$profile['last_name']}</div>
								<div class="school">{$profile['institution']}</div>
								<div class="semester">{$profile['term']} {$profile['year']}</div>
							</div>
							<div class="upload-form">
								<form class="hidden" id="doc-upload-form-skeleton" enctype="multipart/form-data" name="doc-upload" action="?q=doc-upload" method="post">
									<input type="hidden" name="token" />
									<input type="file" name="document" />
									<div class="error hidden"></div>
									<a class="button submit" href="#">submit</a>
								</form>
								<a class="button upload" href="#">upload</a>
							</div>
							<div class="task-create-form-wrapper">
								<form id="to-do-creation-form" class="task-create-form" action="task/create" method="post">
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
												<label for="section_id" class="title">Class: </label>
												{$class_select}
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
						<form id="to-do-option" action="user/list-task" method="post">
							<input type="hidden" name="paginate" value="0" />
							<input type="hidden" name="begin" value="{$timestamp}" />
						</form>
						<div class="panel-inner">
							<div id="to-do-list" class="task-list">
							</div>
							<a href="#" class="button more">more</a>
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
