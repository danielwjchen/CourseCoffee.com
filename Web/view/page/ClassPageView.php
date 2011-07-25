<?php
/**
 * @file
 * Generate the Class page for visiters
 */
class ClassPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($content = null) {
		parent::__construct($content);
		$this->setPageTitle('class');
		$this->addJS('model/logout.js');
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
	<div class="class container">
		<div class="container-inner">
			<div class="header">
				<div class="header-inner">
					<ul id="navigation-menu">
						<li class="home">
							<a class="home button" href="/home">Home</a>
						</li>
						<li class="calendar">
							<a class="calendar button" href="/calendar">Calendar</a>
						</li>
						<li class="class active">
							<a class="class button" href="/class">Class</a>
						</li>
						<li class="logout">
							<a class="logout button" href="#">logout</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="class body">
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
