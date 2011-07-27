<?php
/**
 * @file
 * Generate the welcoem page for visiters
 */
class WelcomePageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($content) {
		$this->content['login_token'] = $content['login_token'];
		$this->content['register_token'] = $content['register_token'];
		$this->content['file_token'] = $content['file_token'];
		parent::__construct($content);
		$this->setPageTitle('welcome');
		$this->addJS('model/login.js');
		$this->addJS('model/registration.js');
		$this->addJS('model/doc.js');
		$this->addJS('controller/welcome.js');
		$this->addCSS('welcome.css');
		$this->addCSS('dialog.css');
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
                  <div class="login-form">
                    <form id="user-login-form" name="user-login" action="user/login" method="post">
                      <input type="hidden" name="token" value="{$login_token}" />
                      <input type="email" name="email" class="input" value="email" />
                      <input type="password" name="password" class="input" value="password" />
                      <a class="button login" href="#">login</a>
                    </form>
                  </div>
                </div>
								<div class="login error hidden"></div>
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
                <form class="hidden" id="doc-upload-form-skeleton" enctype="multipart/form-data" name="doc-upload" action="?q=doc-upload" method="post">
									<input type="hidden" name="token" value="{$file_token}" />
									<input type="file" name="document" />
									<div class="error hidden"></div>
									<a class="button submit" href="#">submit</a>
                </form>
								<a class="button upload" href="#">upload</a>
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
	</body>
</html>
HTML;
	}
}
