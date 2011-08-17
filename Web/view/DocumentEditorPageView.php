<?php
/**
 * @file
 * Provide an interactive task editor
 */
class DocumentEditorPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('editor');
		$this->addJQueryUI();
		$this->addJS('model/book-suggest.js');
		$this->addJS('model/class-suggest.js');
		$this->addJS('model/class-edit.js');
		$this->addJS('controller/editor.js');
    $this->addJS('lib/date.js');
    $this->addJQueryUIPlugin('datetime');
		$this->addCSS('book-list.css');
		$this->addCSS('dialog.css');
		$this->addCSS('editor.css');
	}

	/**
	 * Implement PageViewInterface::getContent()
	 */
	public function getContent() {
		extract($this->data);
		$option = '';
		foreach ($college_option as $key => $value) {
			$option .= "<option value='{$key}'>{$value}</option>";
		}
		$school_select = <<<HTML
<select name="institution_id">
	{$option}
</select>
HTML;
		return <<<HTML
<div class="editor container">
	<div class="container-inner">
		<div class="header">
			<div class="header-inner">
				<ul id="navigation-menu">
					<li class="home active">
						<a class="home button" href="/home">Home</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="body">
			<div class="body-inner">
				<div class="content">
					<form id="processor-form" name="document-processor">
						<input type="hidden" name="document" value="{$document}" />
						<input type="hidden" name="mime" value="{$mime}" />
						<input type="hidden" name="token" value="{$processor_token}" />
					</form>
					<form id="class-selection-form-skeleton" class="hidden" name="class-selection">
						<div class="hidden">
							<input type="hidden" id="year-id" name="year_id" />
							<input type="hidden" id="term-id" name="term_id" />
							<input type="hidden" id="section-id" name="section_id" />
						</div>
						<div class="row">
							<label for="institution_id">School:</label>
							{$school_select}
						</div>
						<div class="row">
							<label for="string">Class: </label>
							<input type="text" id="suggest-input" name="string" />
						</div>
						<div class="confirm-row row">
							<a href="#" class="button confirm disabled">confirm</a>
						</div>
					</form>
					<form id="task-creation-form" name="task-creation">
						<input type="hidden" name="process_state" value="{$process_state}" />
          </form>
          <table id='main_container'>
						<tr>
							<td id='table_title_left'>original syllabus</td>
              <td id='table_title_right' >parsed data</td>
            </tr>
            <tr>
              <td class='data_col_left' id='orig_syl'></td>
              <td class='data_col_right'>
								<div id='parsed_data'></div> 
							</td>
            </tr>
					</table>
          <div id="tool_box">
						<table>
              <tr>    
								<td>
									<a class='toolbox_btn' id="undo" href="#">undo</a>
                  <a class='toolbox_btn' id="redo" href="#">redo</a>
                  <a class='toolbox_btn' id="new_assignment" href="#">new assignment</a>
                  <a class='toolbox_btn' id="create-task" href="#">submit</a>
								</td>
              </tr>
            </table>
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


