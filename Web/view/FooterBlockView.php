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
		$current_year = date('Y');
		return <<<HTML
<span class="copyright">CourseCoffee.com &copy; {$current_year}</span>
<div class="directory">
	<ul>
		<li>&sdot; <a href="/">home</a></li>
		<li>&sdot; <a href="book-search">book search</a></li>
		<li>&sdot; <a href="how-to-find-syllabus">how to</a></li>
		<li>&sdot; <a href="terms-of-use">terms of use</a></li>
		<li>&sdot; <a href="privacy-policy">privacy policy</a></li>
	</ul>
</div>
HTML;
	}
}
