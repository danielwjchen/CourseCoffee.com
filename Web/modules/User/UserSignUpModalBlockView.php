<?php
/**
 * @file
 * Create modal to control user sign-up flow
 */
class UserSignUpModalBlockView extends BlockView implements BlockViewInterface {

	/**
	 * Implement BlockViewInterface::gEtcontentlear().
	 */
	public function getContent() {
		$this->addCSS('User/user-signup-modal');
		$this->addJS('User/signup-model');
		return <<<HTML
<a href="/sign-up" class="button sign-up">Sign Up</a>
HTML;
	}
}
