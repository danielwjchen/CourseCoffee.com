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
						{$school_select}
						<input type="hidden" id="year-id" name="year_id" />
						<input type="hidden" id="term-id" name="term_id" />
						<input type="hidden" id="section-id" name="section_id" />
						<input type="text" id="suggest-input" name="string" />
						<a href="#" class="button confirm disabled">confirm</a>
					</form>
					<form id="task-creation-form" name="task-creation">
					</form>
					<div id="parsed_data">parsed_data</div>
						<table width="460"  cellpadding="0" cellspacing="0" id="table_syl">
							<tr>
								<td height="30" id="table_title" width="460">Original Text</td>
							</tr>
							<tr>
								<td width="460"><div id="orig_syl"></div></td>
							</tr>
						</table>
						<div id="tool_box" class="button">
							<table width="460" height="30" cellpadding="0" cellspacing="0">
								<tr>
									<td width="49" class="tool_box_btn" id="undo">undo</td>
									<td width="49" class="tool_box_btn" id="redo">redo</td>
									<td width="81" class="tool_box_btn" id="new_assignment">new schedule</td>
									<td width="80" class="tool_box_btn" >
										<a id="create-task" href="#">submit</a>
									</td>
									<td width="279"></td>
								</tr>
                                                                <tr>
                                                                </tr>
                                                                <tr>
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


