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
		$this->addJS('model/class-remove.js');
		$this->addJS('model/class-enroll.js');
		$this->addJS('model/class-edit.js');
		$this->addJS('model/editor-action.js');
		$this->addJS('model/register.js');
		$this->addJS('controller/editor.js');
    $this->addJS('lib/date.js');
    $this->addJQueryUIPlugin('datetime');
		$this->addCSS('book-list.css');
		$this->addCSS('dialog.css');
		$this->addCSS('editor.css');
		$this->addCSS('navigation.css');
		$this->addCSS('class-remove.css');
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		return array(
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
					<form id="task-creation-form" name="task-creation">
						<input type="hidden" name="section_id" value="{$section_id}" />
						<input type="hidden" name="section_code" value="{$section_code}" />
						<input type="hidden" name="process_state" value="{$process_state}" />
						<input type="hidden" name="file_id" value="{$file_id}"/>
          </form>
					<div class="suggested-reading hidden">
						<div id="enroll-book-list" class="book-list"></div>
					</div>
          <table id='main_container'>
						<tr>
							<td id='table_title_left'>original syllabus</td>
              <td id='table_title_right' >parsed data</td>
            </tr>
            <tr>
              <td class='data_col_left'><div id='orig_syl'></div><div id="info_box_left"></div></td>
              <td class='data_col_right'><div id="info_box_right"></div>
<div id='parsed_data'></div>  
                  <div id="tool_box">
			<table>
                            <tr>    
		                <td>
		                    <a class='toolbox_btn' id="undo" href="#">undo</a>
                                    <a class='toolbox_btn' id="redo" href="#">redo</a>
                                    <a class='toolbox_btn' id="new_assignment" href="#">new assignment</a>
                                    <a class='toolbox_btn' id="create-task" href="#">submit</a>
                                    <span class="hint" id="toolbox_hint"></span>
				</td>
                            </tr>
                        </table>
		    </div>
            </td>
            </tr>
	    </table>
                  
         <div id="msg_board">
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


