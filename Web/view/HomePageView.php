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

		$this->addJS('model/panel.js');
		$this->addJS('model/task.js');
		$this->addJS('model/to-do.js');
		$this->addJS('controller/home.js');
		$this->addJS('timer.js');
		$this->addCSS('dialog.css');
		$this->addCSS('task.css');
		$this->addCSS('home.css');
		$this->addCSS('book-list.css');
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		return array(
			'header' => array(
				'callback' => 'NavigationBlockView',
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
		$sections = '';
		if (isset($class_list) && is_array($class_list)) {
			foreach ($class_list as $section_id => $section_code) {
				$option .= "<option value='{$section_id}'>{$section_code}</option>";
				$readings .= "<input type='hidden' name='section_id' value='{$section_id}' />";
				$sections .= "<input type='hidden' name='section-code' value='{$section_code}' />";
			}
		}
		$class_select = <<<HTML
<select name="section_id" class="class-list">
	<option selected="selected">pick a class</option>
	{$option}
</select>
HTML;

		$profile_image = empty($fb_uid) ? 'images/default-profile.png' : 'https://graph.facebook.com/' . $fb_uid . '/picture?type=large';

		return <<<HTML
<div class="home container">
	<div class="container-inner">
		<div class="header">
			<div class="header-inner">
				{$header}
			</div>
		</div>
		<div class="body">
			<div class="body-inner">
				<div class="content">
					<div class="panel-top">
						<div class="panel-inner">
							<img class="profile-image" src="{$profile_image}" />
							<div class="profile-info">
								<div class="name">{$profile['first_name']} {$profile['last_name']}</div>
								<div class="school">{$profile['institution']}</div>
								<div class="semester">{$profile['term']} {$profile['year']}</div>
							</div>
							{$upload_form}
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
					<div class="panel-01">
						<h1>Book List</h1>
						<form name="class-feed">
							{$readings}
						</form>
						<div class="panel-inner">
							<div id="home-book-list" class="book-list">
							</div>
						</div>
					</div>
					<div class="panel-02 panel-class">
						<h1>Assignments</h1>
						<form id="to-do-option" action="user/list-task" method="post">
							<input type="hidden" name="user_id" value="{$user_id}" />
							<input type="hidden" name="filter" value="pending" />
							<input type="hidden" name="paginate" value="0" />
						</form>
						<div class="panel-inner">
							<div id="task-info-menu">
								<ul>
									<li id="option-pending" class="active">to-do</li>
									<li id="option-finished" >finished</li>
									<li id="option-all">all</li>
								</ul>
							</div>
							<div id="to-do-list" class="task-list">
							</div>
							<a href="#" class="button more">more</a>
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
