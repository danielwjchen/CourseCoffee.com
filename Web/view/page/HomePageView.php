<?php
/**
 * @file
 * Generate the Home page for visiters
 */
class HomePageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($content = null) {
		parent::__construct($content);
		$this->setPageTitle('home');
		$this->addJS('model/logout.js');
		$this->addJS('model/task.js');
		$this->addJS('controller/home.js');
		$this->addJS('controller/navigation.js');
		$this->addCSS('home.css');
	}

	/**
	 * Implement PageViewInterface::render()
	 */
	public function render() {
		extract($this->content);
		return <<<HTML
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>{$page['title']}</title>
		{$meta}
		{$js}
		{$css}
	</head>
	<body>
	<div class="home container">
		<div class="container-inner">
			<div class="header">
				<div class="header-inner">
					<ul id="navigation-menu">
						<li class="home active">
							<a class="home button" href="/home">Home</a>
						</li>
						<li class="calendar">
							<a class="calendar button" href="/calendar">Calendar</a>
						</li>
						<li class="class">
							<a class="class button" href="/class">Class</a>
						</li>
						<li class="logout">
							<a class="logout button" href="#">logout</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="body">
				<div class="body-inner">
					{$body['block']}
				</div>
			</div>
		</div>
		<div class="footer">
			<div class="footer-inner">
				{$footer['block']}
			</div>
		</div>
	</body>
</html>
HTML;
	}
}
