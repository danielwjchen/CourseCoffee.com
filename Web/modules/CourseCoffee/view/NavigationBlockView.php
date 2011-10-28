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
		$this->addJS('User/logout-model');
		$this->addJS('Curriculum/class-remove-model');
		$this->addJS('Curriculum/class-suggest-model');
		$this->addJS('Curriculum/class-enroll-model');
		$this->addJS('CourseCoffee/navigation-controller');
		$this->addJS('Item/book-suggest-model');

		$this->addCSS('CourseCoffee/navigation');
		$this->addCSS('Curriculum/class-remove');

		switch ($this->data['role']) {
			case 'super_admin':
			case 'admin':
				return <<<HTML
<ul id="navigation-menu">
	<li class="home">
		<a class="button" href="/home">Home</a>
	</li>
	<li class="admin">
		<a class="button" href="/admin">Admin</a>
	</li>
	<li class="calendar">
		<a class="button" href="/calendar">Calendar</a>
	</li>
	<li class="class">
		<a class="button" href="/class">Class</a>
	</li>
	<li class="book-search">
		<a class="button" href="/book-search">Books</a>
	</li>
	<li class="class-suggest">
		<div class="class-suggest-inner">
			<form id="class-suggest-form" name="class-suggest" action="college-class-suggest" method="post">
				<input type="text" name="string" id="suggest-input" value="e.g. CSE231001" />
				<input type="hidden" id="section-id" name="section_id" />
				<a class="button suggest" href="#">add</a>
			</form>
		</div>
	</li>
	<li class="logout">
		<a class="logout button" href="#">logout</a>
	</li>
</ul>
HTML;
			default:
				return <<<HTML
<ul id="navigation-menu">
	<li class="home">
		<a class="button" href="/home">Home</a>
	</li>
	<li class="calendar">
		<a class="button" href="/calendar">Calendar</a>
	</li>
	<li class="class">
		<a class="button" href="/class">Class</a>
	</li>
	<li class="book-search">
		<a class="button" href="/book-search">Books</a>
	</li>
	<li class="class-suggest">
		<div class="class-suggest-inner">
			<form id="class-suggest-form" name="class-suggest" action="college-class-suggest" method="post">
				<input type="text" name="string" id="suggest-input" value="e.g. CSE231001" />
				<input type="hidden" id="section-id" name="section_id" />
				<a class="button suggest" href="#">add</a>
			</form>
		</div>
	</li>
	<li class="logout">
		<a class="logout button" href="#">logout</a>
	</li>
</ul>
HTML;
		}
	}
}
