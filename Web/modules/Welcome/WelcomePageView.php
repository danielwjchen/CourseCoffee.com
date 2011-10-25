<?php
/**
 * @file
 * Generate the welcoem page for visiters
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
class WelcomePageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('welcome');

		$this->addJS('File/doc');

		$this->addJS('Welcome/slide-show');
		$this->addJS('Welcome/welcome');

		$this->addCSS('Welcome/slide-show');
		$this->addCSS('Welcome/welcome');
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		return array(
			'userLoginForm' => array(
				'callback' => 'UserLoginFormBlockView',
			),
			'userSignUpAction' => array(
				'callback' => 'UserSignUpModalBlockView',
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
<div class="welcome container">
	<div class="container-inner">
			<div class="body">
				<div class="body-inner">
					<div class="panel-top">
						<div class="panel-inner">
							<img src="images/logo.png" class="logo" />
							<div class="user-action">
								{$userLoginForm}
								{$userSignUpAction}
							</div>
							<div class="login error hidden"></div>
						</div>
					</div>
					<div class="panel-01">
						<div class="panel-inner">
							<div class="slide-show">
								<ul>
									<li class="show">
										<img src="/images/slides/book.png" rel="<li class='text-book'>Find the best book deals on the internet</li>" />
									</li>
									<li>
										<img src="/images/slides/calendar.png" rel="<li class='calendar'>Instantly organize your assignments into one calendar</li>" />
									</li>
									<li>
										<img src="/images/slides/class.png" rel="<li class='facebook'>Collaborate with classmates</li>" />
									</li>
									<li>
										<img src="/images/slides/editor.png" rel="<li class='calendar'>Instantly organize your assignments into one calendar</li>" />
									</li>
								</ul>
								<div class="caption">
									<ul class="caption-content"></ul>
								</div>
							</div>
							<div class="social-plugins">
								<strong>Share this with your friends</strong>
								<ul>
									<li>
										<div class="fb-like" data-href="http://www.coursecoffee.com" data-send="false" data-layout="box_count" data-width="50" data-show-faces="true"></div>
									</li>
									<li>
										<g:plusone size="tall"></g:plusone>
									</li>
									<li>
										<a href="https://twitter.com/share" class="twitter-share-button" data-url="coursecoffee.com" data-text="College made easy." data-count="vertical" data-via="CourseCoffee">Tweet</a>
										<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
									</li>
								</ul>
							</div>
						</div>
					</div>
				<div class="panel-02">
					<div class="tagline">
						<h1>College made easy.</h1>
					</div>
					<div class="panel-inner">
						<div class="upload-form">
							<form class="hidden" id="doc-upload-form-skeleton" enctype="multipart/form-data" name="doc-upload" action="?q=doc-upload" method="post">
								<input type="hidden" name="token" />
								<input type="file" name="document" />
								<div class="error hidden"></div>
								<a class="button submit" href="#">upload</a>
							</form>
							<a class="button upload" href="#">upload now!</a>
						</div>
						<div class="links">
							 <a href="/book-search">Or, just want books? Click here.</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clear-fix"></div>
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
