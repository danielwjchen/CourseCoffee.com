<?php
/**
 * @file
 * Generate tutorial page
 */
class TutorialPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data = null) {
		parent::__construct($data);
		$this->setPageTitle('tutorial');
		$this->addCSS('tale.css');
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		return array(
			'header' => array(
				'callback' => 'LogoHeaderBlockView',
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
<div class="container">
	<div class="container-inner">
		<div class="header">
			<div class="header-inner">
				{$header}
			</div>
		</div>
		<div class="tale body">
			<div class="body-inner">
				<div class="content">
					<h2>How do I find my syllabus?</h2>
					<p>At the beginning of the semester, your professor will give you a syllabus -- a document that outlines every assignment you will have to complete throughout the semester. By uploading your syllabus to CourseCoffee.com, you can quickly generate a calendar that keeps track of your upcoming assignments, readings, quizzes, and exams.</p>
					<h3>Michigan State University</h3>
					<p>MSU students can find their course syllabus at <a href="www.angel.msu.edu">www.angel.msu.edu</a>.</p>
					<ul style="margin-left:90px">
						<li>After logging on with your MSU user ID and password, click on the class you want to download the syllabus from on the left-hand column.</li>
						<li>Click on the “Resources” or “Lessons” tab at the top of the class page to find the syllabus.</li>
						<li>Download the syllabus to your computer, then upload it to CourseCoffee.com to create your calendar.</li>
					</ul>
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
