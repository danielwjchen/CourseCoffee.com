<?php
/**
 * @file
 * Generate the registration page for visiters
 */
class SignUpPageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setPageTitle('sign up');
		$this->addJS('controller/signup.js');
		$this->addJS('model/register.js');
		$this->addCSS('signup.css');
	}

	/**
	 * Implement View::getHeader()
	 */
	protected function getHeader() {
    header(self::STATUS_OK);
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		return array(
			'header' => array(
				'callback' => 'LogoHeaderBlockView',
			),
			'legal' => array(
				'callback' => 'UserAgreementBlockView',
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
		if ($error) {
			$error_message = '<div class="row error">' . $error . '</div>';
		} else {
			$error_message = '<div class="row error hidden"></div>';
		}
		$option = '';
		foreach ($college_option as $key => $value) {
			$option .= "<option value='{$key}'>{$value}</option>";
		}
		$school_select = <<<HTML
<select name="school">
	{$option}
</select>
HTML;
		return <<<HTML
<div class="sign-up container">
	<div class="container-inner">
		<div class="header">
			<div class="header-inner">
				{$header}
			</div>
		</div>
		<div class="body">
			<div class="body-inner">
				<div class="content"> 
					<form id="user-register-form" name="registration" action="user-register" method="post"> 
						<input type="hidden" name="token" value="{$register_token}" /> 
						{$error_message}
						<div class="row"> 
							<div class="title"> 
								<label for="first-name">first name: </label> 
							</div> 
							<div class="field"> 
								<input type="text" name="first-name" /> 
							</div> 
						</div> 
						<div class="row"> 
							<div class="title"> 
								<label for="last-name">last name: </label> 
							</div> 
							<div class="field"> 
								<input type="text" name="last-name" /> 
							</div> 
						</div> 
						<div class="row"> 
							<div class="title"> 
								<label for="user-account">email: </label> 
							</div> 
							<div class="field"> 
								<input type="email" name="email" /> 
							</div> 
						</div> 
						<div class="row">
							<p>*We use this email address to verify your account and such. Please make sure you've got it right.<p>
						</div>
						<div class="row"> 
							<div class="title"> 
								<label for="password">password: </label> 
							</div> 
							<div class="field"> 
								<input type="password" name="password" /> 
							</div> 
						</div> 
						<div class="row"> 
							<div class="title"> 
								<label for="confirm">confirm password: </label> 
							</div> 
							<div class="field"> 
								<input type="password" name="confirm" /> 
							</div> 
						</div> 
						<div class="row">
							<p>*For security reasons, password needs to contain more than 8 characters.<p>
						</div>
						<a href="#" class="button sign-up">Join</a> 
					</form> 
					{$legal}
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
