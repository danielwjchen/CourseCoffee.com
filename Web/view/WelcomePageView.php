<?php
/**
 * @file
 * Generate the welcoem page for visiters
 */
class WelcomePageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('welcome');
		$this->addJS('model/login.js');
		$this->addJS('model/register.js');
		$this->addJS('model/doc.js');
		$this->addJS('controller/welcome.js');
		$this->addCSS('dialog.css');
		$this->addCSS('welcome.css');
	}

	/**
	 * Implement PageViewInterface::getContent()
	 */
	public function getContent() {
		extract($this->data);
		return <<<HTML
<div class="welcome container">
	<div class="container-inner">
			<div class="body">
				<div class="body-inner">
					<div class="panel-top">
						<div class="panel-inner">
							<img src="images/logo.png" class="logo" />
							<div class="user-login">
								<div class="login-form">
									<form id="user-login-form" name="user-login" action="user-login" method="post">
										<input type="hidden" name="token" value="{$login_token}" />
										<input type="email" name="email" class="input" value="email" />
										<input type="password" name="password" class="input" value="password" />
										<a class="button login" href="#">login</a>
									</form>
								</div>
							</div>
							<div class="login error hidden"></div>
							<p class="slogan"><span class="hidden">School is hectic! <br />Instantly organize!</span></p>
						</div>
					</div>
					<div class="panel-01">
						<div class="panel-inner">
							<h2>Upload your course syllabi to:</h2>
							<ul>
								<li class="calendar">Automatically organize all your class assignments into one calendar</li>
								<li class="text-book">Instantly find the cheapest deals on your textbooks online</li>
								<li class="facebook">Collaborate with your classmates on facebook</li>
						 </ul>
							 <a href="#">Help: where do I access my electronic syllabus?</a>
							 <div class="sign-up-shortcut">
									<p>or... manually</p>
									<a href="/sign-up" class="button sign-up">Sign Up</a>
							 </div>
						</div>
					</div>
				<div class="panel-02">
					<div class="panel-inner">
						<div class="upload-form">
							<form class="hidden" id="doc-upload-form-skeleton" enctype="multipart/form-data" name="doc-upload" action="?q=doc-upload" method="post">
								<input type="hidden" name="token" value="{$file_token}" />
								<input type="file" name="document" />
								<div class="error hidden"></div>
								<a class="button submit" href="#">upload</a>
							</form>
							<a class="button upload" href="#">start here!</a>
						</div>
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
