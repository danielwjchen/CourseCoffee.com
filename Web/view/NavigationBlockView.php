<?php
/**
 * @file
 * Define the navigation block
 */
class NavigationBlockView extends BlockView implements BlockViewInterface {

	/**
	 * Implement BlockViewInterface::gEtcontentlear().
	 */
	public function getContent() {
		$this->addJS('model/logout.js');
		$this->addJS('controller/navigation.js');
		$this->addJS('model/book-suggest.js');
		$this->addJS('model/class-suggest.js');
		$this->addJS('model/class-enroll.js');
		return <<<HTML
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
	<li class="class-suggest">
		<div class="class-suggest-inner">
			<form id="class-suggest-form" name="class-suggest" action="college-class-suggest" method="post">
				<input type="text" name="string" id="suggest-input" value="e.g. CSE231001" />
				<input type="hidden" id="section-id" name="section_id" />
				<a class="button suggest" href="#">add</a>
			</form>
	</li>
	<li class="logout">
		<a class="logout button" href="#">logout</a>
	</li>
</ul>
HTML;
	}
}
