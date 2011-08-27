<?php
/**
 * @file
 * Define the navigation block
 */
class SimplifiedNavigationBlockView extends BlockView implements BlockViewInterface {

	/**
	 * Implement BlockViewInterface::gEtcontentlear().
	 */
	public function getContent() {
		$this->addCSS('navigation.css');
		return <<<HTML
<div class="simplified-navigation">
	<div class="navigation-inner">
		<ul id="navigation-menu">
			<li class="home">
				<a class="home button" href="/home">Home</a>
			</li>
			<li class="calendar">
				<a class="calendar button" href="/calendar">Calendar</a>
			</li>
			<li class="class">
				<a class="class button" href="/class">Class</a>
			</li>
		</ul>
	</div>
</div>
HTML;
	}
}
