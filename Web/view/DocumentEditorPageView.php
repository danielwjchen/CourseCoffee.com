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
		$this->addJS('model/register.js');
		$this->addJS('controller/editor.js');
                $this->addJS('lib/date.js');
                
                $this->addJQueryUIPlugin('datetime');
                
                $this->addCSS('book-list.css');
		$this->addCSS('dialog.css');
                $this->addCSS('navigation.css');
		$this->addCSS('editor.css');

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
            <li class="cancel">
                <a class="cancel button" href="#" >cancel</a>
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
						<input type="hidden" name="file_id" value="{$file_id}"/>
                                        </form>
        <!--Cheng's code starts from here-->

        <table id='main_container'>
        <div class='content-wrap'>
            <div class='content'></div>
        </div>
        </table>
        
        
                  
        <!--Cheng's code ends-->

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


