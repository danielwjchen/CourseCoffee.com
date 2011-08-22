<?php
/**
 * @file
 * Define the footer information block
 */
class FooterBlockView extends BlockView implements BlockViewInterface {

	/**
	 * Implement BlockViewInterface::gEtcontentlear().
	 */
	public function getContent() {
		return <<<HTML
<span>CourseCoffee.com &copy;</span>
<ul>
</ul>
HTML;
	}
}
