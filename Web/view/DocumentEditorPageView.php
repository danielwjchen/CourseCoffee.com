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
		$this->addJS('controller/editor.js');
		$this->addCSS('editor.css');
	}

	/**
	 * Implement PageViewInterface::getContent()
	 */
	public function getContent() {
		extract($this->data);
		return <<<HTML
	<form id="processor-dorm" name="document-processor">
		<input type="hidden" name="document" value="{$document}" />
		<input type="hidden" name="token" value="{$processor_token}" />
	</form>
<div id="parsed_data">parsed_data</div>

<table width="500"  cellpadding="0" cellspacing="0" id="table_syl">
  <tr>
    <td height="30" id="table_title" width="500">Original Text</td>
  </tr>
  <tr>
    <td width="500"><div id="orig_syl"></div></td>
  </tr>
</table>

<div id="tool_box">
  <table width="500" height="30" cellpadding="0" cellspacing="0">
    <tr>
      <td width="49" class="tool_box_btn" id="undo">undo</td>
      <td width="49" class="tool_box_btn" id="redo">redo</td>
      <td width="81" class="tool_box_btn" >new schedule</td>
      <td width="319"></td>
      
    </tr>
  </table>
</div>
HTML;
	}
}


