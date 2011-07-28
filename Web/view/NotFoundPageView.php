<?php
/**
 * @file
 * Generate the NotFound page for visiters
 */
class NotFoundPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('404 Page not found.');
		$this->addJS('model/login.js');
		$this->addJS('controller/not-found.js');
		$this->addJS('controller/navigation.js');
		$this->addCSS('not-found.css');
	}

	/**
	 * Implement View::getHeader()
	 */
	protected function getHeader() {
    header(self::NOT_FOUND);
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
				<ul id="navigation-menu">
					<li class="login">
						<div class="error hidden"></div>
						<div class="login-form">
							<form id="user-login-form" name="user-login" action="user/login" method="post">
								<input type="hidden" name="token" value="{$header['block']['login_token']}" />
								<input type="email" name="email" class="input" value="email" />
								<input type="password" name="password" class="input" value="password" />
								<a class="button login" href="#">login</a>
							</form>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<div class="not-found body">
			<div class="body-inner">
				<h1>404 Not Found</h1>	
			</div>
		</div>
	</div>
</div>
<div class="footer">
	<div class="footer-inner">
	</div>
</div>
HTML;
	}
}
