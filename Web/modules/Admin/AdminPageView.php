<?php
/**
 * @file
 * Generate the Admin page for administrators
 */
class AdminPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('home');

		// get date-time selector
		$this->addJQueryUI();
		$this->addJQueryUIPlugin('datetime');

		$this->addJS('model/panel.js');
		$this->addJS('model/task.js');
		$this->addJS('model/admin-user.js');
		$this->addJS('model/admin-syllabus.js');
		$this->addJS('controller/admin.js');
		$this->addJS('timer.js');
		$this->addCSS('dialog.css');
		$this->addCSS('panel.css');
		$this->addCSS('task.css');
		$this->addCSS('admin.css');
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

		return <<<HTML
<div class="admin container">
	<div class="container-inner">
		<div class="header">
			<div class="header-inner">
				{$header}
			</div>
		</div>
		<div class="body">
			<div class="body-inner">
				<div class="content">
					<div class="task-create-form-wrapper-skeleton hidden">
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
					<div class="admin-menu panel-menu">
						<div class="panel-menu-inner">
							<form name="admin-option" id="admin-option-form">
								<input type="hidden" name="type" value="syllabus" />
								<input type="hidden" name="timestamp" value="{$timestamp}" />
								<input type="hidden" name="paginate" value="0" />
							</form>
							<ul>
								<li><a href="#" id="admin-option-sylabus" class="option syllabus active">syllabus</a></li>
								<!--
								<li><a href="#" id="admin-option-user" class="option user">user</a></li>
								-->
							</ul>
						</div>
					</div>
					<div class="panel-01">
						<h1></h1>
						<form name="syllabus-queue-menu" id="syllabus-queue-menu-form" class="queue-menu-form">
							<input type="hidden" name="paginate" value="0" />
							<input type="hidden" name="status" value="has_syllabus" />
							<input type="hidden" name="timestamp" value="{$timestamp}" />
						</form>
						<div class="panel-inner">
							<div id="syllabus-queue-menu" class="queue-menu">
								<ul>
									<li id="option-new" class="active">new</li>
									<!--
									<li id="option-processed" >processed</li>
									<li id="option-all">all</li>
									-->
								</ul>
							</div>
							<div id="syallbus-queue-list" class="queue-list">
							</div>
							<a href="#" class="button more">more</a>
						</div>
					</div>
					<div class="panel-02">
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
