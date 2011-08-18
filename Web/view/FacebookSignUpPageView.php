<?php
/**
 * @file
 * Generate the registration page for visiters
 */
class FacebookSignUpPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('sign up with facebook');
		$this->addCSS('fb-signup.css');
	}

	/**
	 * Implement View::getHeader()
	 */
	protected function getHeader() {
    header(self::STATUS_OK);
	}

	/**
	 * Implement PageViewInterface::getContent()
	 */
	public function getContent() {
		extract($this->data);
		return <<<HTML
<div class="fb-sign-up container">
	<div class="container-inner">
		<div class="header">
			<div class="header-inner">
				<ul id="navigation-menu">
					<li class="home active">
						<a class="home button" href="/home">Home</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="body">
			<div class="body-inner">
				<div class="content"> 
				<fb:registration 
					fields="[
						{'name' : 'name'},
						{'name' : 'first_name'},
						{'name' : 'last_name'},
						{'name' : 'school', 
							'description' : 'The current school you are attending', 
							'type' : 'select',
							'options' : {1 : 'Michigan State University', 2: 'University of Michigan'}},
						{'name' : 'email'},
						{'name' : 'password'}
					]" 
					redirect-uri="/user-register-facebook"
					width="530">
				</fb:registration>
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
