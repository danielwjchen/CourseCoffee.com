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
		$this->setPageTitle('how to find syllabus online');
		$this->addCSS('tale.css');
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		return array(
			'header' => array(
				'LogoHeaderBlockView',
			),
			'footer' => array(
				'FooterBlockView',
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
					<h2>Where do I find find my syllabus?</h2>
					<p>At the beginning of the semester, your professor will give you a syllabus - a document containing the dates of every assignment you will have to complete for the semester. These syllabuses, or syllabi, are what you need to upload to our site to quickly generate your schedule of assignments.</p>
					<h3>Michigan State University</h3>
					<p>For MSU students, they are available at <a href="www.angel.msu.edu">www.angel.msu.edu</a>. After logging in with your MSU username, click on your class links on the left hand side of the page, and look under your "Classes" or "Lessons" tab for the syllabus .doc or .pdf files. Once you have found these documents, download them to your desktop, and then upload them to <a href="coursecoffee.com">coursecoffee.com</a>! Remember to double check the dates to make sure they have been processed correctly!</p>
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
