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
		$this->addCSS('footer.css');
		return <<<HTML
<span class="copyright">CourseCoffee.com &copy;</span>
<ul>
</ul>
HTML;
	}
}
