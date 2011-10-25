<?php
/**
 * @file
 * Provide an interactive task editor
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
class DocumentEditorPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('editor');

		$this->addJQueryUI();
    $this->addJQueryUIPlugin('datetime');

		$this->addJS('Item/item-suggest');

		$this->addJS('Curriculum/class-suggest');
		$this->addJS('Curriculum/class-remove');
		$this->addJS('Curriculum/class-enroll');
		$this->addJS('Curriculum/class-edit');

    $this->addJS('Document/date');
		$this->addJS('Document/editor-flow');
		$this->addJS('Document/editor-processor');
		$this->addJS('Document/editor');

		$this->addJS('User/signup-model');

		$this->addCSS('Item/book-list.css');
		$this->addCSS('class-remove.css');
		$this->addCSS('Page/modal.css');
		$this->addCSS('Page/navigation.css');
		$this->addCSS('Document/editor.css');
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
				<ul id="editor-action-menu">
          <li class="undo undo_operation">
						<a class="undo button" href="#">undo</a>
          </li>
          <li class="redo redo_operation">
						<a class="redo button" href="#">redo</a>
          </li>
          <li class="add">
						<a class="add button add_new_task" href="#">new task</a>
          </li>
          <li class="submit" id="create-task">
						<a class="submit button" href="#">submit</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="body">
                    <div class="body-inner">
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
                  
        <div class="content-wrap">
            
            
        </div>
        <div class="task-wrap">
                                
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


