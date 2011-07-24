<?php
/**
 * @file 
 * Generate the user profile block on the home page
 */
class HomeBlockView extends BlockView{
	public function render() {
		extract($this->content);
		return <<<HTML
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
								<input type="text" name="due_date" class="due_date" />
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
		<div class="panel-inner">
		</div>
	</div>
</div>
HTML;
	}
}
