<?php
/**
 * @file
 * Generate the Class page for visiters
 */
class ClassPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($content = null) {
		parent::__construct($content);
		$this->setPageTitle('class');

		$this->addJQueryUI();
		$this->addJQueryUIPlugin('datetime');

		$this->buildClassList($content['class_list']);

		$this->addJS('model/logout.js');
		$this->addJS('model/task.js');
		$this->addJS('model/doc.js');
		$this->addJS('model/book-suggest.js');
		$this->addJS('model/class-suggest.js');
		$this->addJS('model/class-info.js');
		$this->addJS('timer.js');
		$this->addJS('controller/class.js');
		$this->addJS('controller/navigation.js');
		$this->addCSS('dialog.css');
		$this->addCSS('class.css');
		$this->addCSS('panel.css');
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
	 * Build Class list
	 *
	 * @param array $class_list
	 */
	private function buildClassList($class_list) {
		$html = "<ul>";
		foreach ($class_list as $id => $class_code) {
			$html .= "<li><a href='#' id='{$id}' class='option'>{$class_code}</a></li>";
		}
		$html .= "</ul>";
		$this->data['panel_menu'] = $html;
	}

	private function getClassOption($class_info) {

		// debug
		//  error_log('class info - ' . print_r($class_info, true));
		if ($class_info['content']) {
			extract($class_info['content']);

			// debug
			// error_log('institution_uri - ' . $institution_uri);

			return <<<HTML
	<input type="hidden" name="institution-uri" value="{$institution_uri}" />
	<input type="hidden" name="institution-id" value="{$institution_id}" />
	<input type="hidden" name="subject-abbr" value="{$subject_abbr}" />
	<input type="hidden" name="course-title" value="{$course_title}" />
	<input type="hidden" name="course-num" value="{$course_num}" />
	<input type="hidden" name="course-description" value="{$course_description}" />
	<input type="hidden" name="section-num" value="{$section_num}" />
	<input type="hidden" name="section-id" value="{$section_id}" />
HTML;
		} else {
			return <<<HTML
	<input type="hidden" name="institution-uri" value="" />
	<input type="hidden" name="institution-id" value="" />
	<input type="hidden" name="subject-abbr" value="" />
	<input type="hidden" name="course-title" value="" />
	<input type="hidden" name="course-num" value="" />
	<input type="hidden" name="course-description" value="" />
	<input type="hidden" name="section-num" value="" />
	<input type="hidden" name="section-id" value="" />
HTML;
		}
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
	
		$class_option = $this->getClassOption($class_info);

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
					<div class="calendar panel-menu">
						<div class="panel-menu-inner">
							<form name="class-option" id="class-option-form">
								{$class_option}
									<input type="hidden" name="paginate" value="0" />
							</form>
							{$panel_menu}
						</div>
					</div>
					<div class="panel-01">
						<div class="panel-inner">
							<div class="class-section-info"></div>
							<hr />
							<div id="class-book-list" class="book-list" ></div>
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
								<form id="class-task-creation-form" class="task-create-form" action="task/create" method="post">
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
							<div id="class-task-list" class="task-list">
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
