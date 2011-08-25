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
		return <<<HTML
<div class="visitor-navigation">
	<div class="login-form">
		<form id="user-login-form" name="user-login" action="user-login" method="post">
			<input type="hidden" name="token" value="{$login_token}" />
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
