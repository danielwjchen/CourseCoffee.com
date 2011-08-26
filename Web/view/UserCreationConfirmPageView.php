<?php
/**
 * @file
 * Generate the InternalError page for visiters
 */
class UserCreationConfirmPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data = null) {
		parent::__construct($data);
		$this->addCSS('account-created.css');
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
				<a href="/home"><img src="images/logo.png" class="logo" /></a>
			</div>
		</div>
		<div class="account-created body">
			<div class="body-inner">
				<div class="content">
					<div class="panel-top">
						<h1>Hi {$first_name}!</h1>
						<p>Welcome to CourseCoffee.com! Your account is <strong>{$account}</strong>.</p>
					</div>
					<div class="panel-01">
						<div class="panel-inner">
							<h2>Here is what you can do with a CourseCoffee.com account:</h2>
							<ul>
								<li class="calendar">Automatically organize all your class assignments into one calendar</li>
								<li class="text-book">Instantly find the cheapest deals on your textbooks online</li>
								<li class="facebook">Collaborate with your classmates on facebook</li>
						 </ul>
						 	<div class="tutorial">
								 <a href="/how-to-find-syllabus" target="_blank">Help: where do I find my syllabus?</a>
							</div>
						</div>
					</div>
				<div class="panel-02">
					<div class="panel-inner">
						<a class="button" href="/home">home</a>
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
