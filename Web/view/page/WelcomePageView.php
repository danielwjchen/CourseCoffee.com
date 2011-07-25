<?php
/**
 * @file
 * Generate the welcoem page for visiters
 */
class WelcomePageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($content = null) {
		parent::__construct($content);
		$this->content['body']['css'] = 'welcome';
		$this->setPageTitle('welcome');
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
	<div class="welcome container">
    <div class="container-inner">
        <div class="body">
          <div class="body-inner">
            <div class="panel-top">
              <div class="panel-inner">
                <img src="images/logo.png" class="logo" />
                <div class="user-login">
                  <div class="error hidden"></div>
                  <div class="login-form">
                    <form id="user-login-form" name="user-login" action="user/login" method="post">
                      <input type="hidden" name="token" value="{$header['block']['login_token']}" />
                      <input type="email" name="email" class="input" value="email" />
                      <input type="password" name="password" class="input" value="password" />
                      <a class="button login" href="#">login</a>
                    </form>
                  </div>
                </div>
                <p class="slogan">School is hectic. Instantly organize!</p>
              </div>
            </div>
            <div class="panel-01">
              <div class="panel-inner">
                <h2>Upload your course syllabuses to</h2>
                <ul>
                  <li>Automatically organize all your class assignments into one calendar</li>
                  <li>Instantly find the cheapest deals on your textbooks online</li>
                  <li>Collaborate with your classmates on facebook</li>
               </ul>
                 <a href="#">Help: where do I access my electronic syllabi?</a>
              </div>
            </div>
          <div class="panel-02">
            <div class="panel-inner">
              <div class="upload-form">
                <form id="upload">
                  <a class="button upload" href="#">upload</a>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
		</div>
	</div>
  <div class="footer">
    <div class="footer-inner">
    </div>
  </div>
	</body>
</html>
HTML;
	}
}
