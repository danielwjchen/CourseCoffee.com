<?php
/**
 * @file
 * Generate the Calendar page for visiters
 */
class CalendarPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($content = null) {
		parent::__construct($content);
		$this->content['body']['css'] = 'welcome';
		$this->addJS('model/login.js');
		$this->addJS('model/registration.js');
		$this->addJS('controller/welcome.js');
		$this->addJS('controller/navigation.js');
		$this->addCSS('welcome.css');
	}

	/**
	 * Implement PageViewInterface::render()
	 */
	public function render() {
		$this->setHeader(self::HTML_HEADER);
		extract($this->content);
		return <<<HTML
<html>
	<head>
		<title>{$page['title']}</title>
		{$meta}
		{$js}
		{$css}
	</head>
	<body>
	<div class="container">
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
		<div class="{$body['css']} body">
			<div class="body-inner">
				{$body['block']}
			</div>
		</div>
		<div class="footer">
			<div class="footer-inner">
			</div>
		</div>
	</div>
	</body>
</html>
HTML;
	}
}
