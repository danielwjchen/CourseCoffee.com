<?php
/**
 * @file
 * Define user agreement block
 */
class UserAgreementBlockView extends BlockView implements BlockViewInterface {

	/**
	 * Implement BlockViewInterface::gEtcontentlear().
	 */
	public function getContent() {
		$this->addCSS('User/user-agreement');
		return <<<HTML
			<div class="user-agreement">
				<p>By clicking Join, you agree to CourseCoffee.com <a href="/terms-of-use" target="_blank">terms of use</a> and <a href="privacy-policy">privacy policy</a>.</p>
			</div>
HTML;
	}
}
