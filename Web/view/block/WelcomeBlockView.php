<?php
/**
 * @file 
 * Generate the greeting block on the welcome page
 */
class WelcomeBlockView extends BlockView{
	public function render() {
		return <<<HTML
<div class="dialog hybrid">
	<div class="content">
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
</div>
HTML;
	}
}
