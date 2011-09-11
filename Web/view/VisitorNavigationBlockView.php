<?php
/**
 * @file
 * Define the footer information block
 */
class VisitorNavigationBlockView extends BlockView implements BlockViewInterface {

	/**
	 * Implement BlockViewInterface::gEtcontentlear().
	 */
	public function getContent() {
		$this->addCSS('visitor-header.css');
		$this->addJS('model/login.js');
		$this->addJS('model/register.js');
		$this->addJS('controller/visitor-navigation.js');
		return <<<HTML
<div class="visitor-header">
	<div class="login-form">
		<form id="user-login-form" name="user-login" action="user-login" method="post">
			<input type="hidden" name="token" value="{$this->data['login_token']}" />
			<input type="email" name="email" class="input" value="email" />
			<input type="password" name="password" class="input" value="password" />
			<a class="button login" href="#">login</a>
		</form>
	</div>
	<a href="/sign-up" class="button sign-up">Sign Up</a>
</div>
<div class="login error hidden"></div>
HTML;
	}
}
