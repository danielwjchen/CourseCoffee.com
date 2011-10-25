<?php
/**
 * @file
 *
 * Create HTML block that contains form for user to log in
 */
class UserLoginFormBlockView extends BlockView implements BlockViewInterface {

	/**
	 * Implement BlockViewInterface::gEtcontentlear().
	 */
	public function getContent() {
		$this->addJS('User/login');
		return <<<HTML
<div class="user-login">
	<div class="login-form">
		<form id="user-login-form" name="user-login" action="user-login" method="post">
			<input type="hidden" name="token" value="{$login_token}" />
			<input type="email" name="email" class="input" value="email" />
			<input type="password" name="password" class="input" value="password" />
			<a class="button login" href="#">login</a>
		</form>
	</div>
</div>
HTML;
	}
}
