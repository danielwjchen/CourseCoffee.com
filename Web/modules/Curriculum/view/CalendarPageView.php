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

		$this->addJS('Quest/task-model');
		$this->addJS('CourseCoffee/panel-model');
		$this->addJS('CourseCoffee/scrollbar-model');
		$this->addJS('CourseCoffee/timer');
		$this->addJS('Curriculum/calendar-model');
		$this->addJS('Curriculum/calendar-controller');

		$this->addCSS('CourseCoffee/dialog');
		$this->addCSS('CourseCoffee/panel');
		$this->addCSS('Curriculum/calendar');
		$this->addCSS('Quest/task');
		$this->addCSS('Item/book-list');
	}

	/**
	 * Implement View::getHeader()
	 */
	protected function getHeader() {
        header(self::STATUS_OK);
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		return array(
			'header' => array(
				'callback' => 'NavigationBlockView',
				'params'   => array('role'),
			),
			'upload_form' => array(
				'callback' => 'UploadFormBlockView',
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
		$option = '';
		if (is_array($class_list)) {
			foreach ($class_list as $section_id => $section_code) {
				$option .= "<option value='{$section_id}'>{$section_code}</option>";
			}
		}
		$class_select = <<<HTML
<select name="section_id" class="class-list">
	<option selected="selected">pick a class</option>
	{$option}
</select>
HTML;

		return <<<HTML
<div class="calendar container">
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
								<input type="hidden" name="user_id" value="{$user_id}" />
								<input type="hidden" name="filter" value="pending" />
								<input type="hidden" name="paginate" value="0" />
							</form>
							<ul>
								<li><a href="#" class="option month active">month</a></li>
								<li><a href="#" class="option week">week</a></li>
								<li><a href="#" class="option customized">4-day</a></li>
								<li><a href="#" class="option today">day</a></li>
							</ul>
						</div>
					</div>
					<div class="panel-01">
						<div class="panel-inner">
							<div class="calendar-display">
							</div>
							<a class="calendar-button button backward" href="#">&lt;</a>
							<a class="calendar-button button forward" href="#">&gt;</a>
						</div>
					</div>
					<div class="panel-02">
						<div class="panel-inner">
						{$upload_form}
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
							<div id="task-info-menu">
								<ul>
									<li id="option-pending" class="active">to-do</li>
									<li id="option-finished" >finished</li>
									<li id="option-all">all</li>
								</ul>
							</div>
							<div id="calendar-task-list" class="task-list">
							</div>
						</div>
					</div>
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
