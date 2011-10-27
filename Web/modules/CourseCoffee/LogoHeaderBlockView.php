<?php
/**
 * @file
 * Define the logo header block
 */
class LogoHeaderBlockView extends BlockView implements BlockViewInterface {

	/**
	 * Implement BlockViewInterface::gEtcontentlear().
	 */
	public function getContent() {
		$this->addCSS('logo-header.css');
		return <<<HTML
<a href="/welcome"><img src="images/logo.png" class="logo" /></a>
<a class="button back" href="/welcome">back</a>
HTML;
	}
}
