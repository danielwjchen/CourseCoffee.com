<?php
/**
 * @file 
 * Generate the greeting block on the welcome page
 */
class WelcomeBlockView extends BlockView{
	public function render() {
		return <<<HTML
<div class="dialog horizontal">
	<div class="dialog-inner">
		<div class="search-form">
			<form id="search">
				<input name="search" class="field" type="text" />
				<a class="button enroll" href="#">go</a>
			</form>
		</div>
		<div class="upload-form">
			<form id="upload">
				<div class="description">Or send us your syllabus!</div>
				<a class="button upload" href="#">upload</a>
			</form>
		</div>
	</div>
	<div class="progress">
		<ul>
			<li class="step-1 highlight"><h3>Step 1</h3> find your class</li>
			<li class="step-2"><h3>Step 2</h3> create an account</li>
			<li class="step-3"><h3>Step 3</h3> invite your classmates!</li>
		</ul>
	</div>
</div>
HTML;
	}
}
