<?php
/**
 * @file
 * Generate a list of colleges for enrollment
 */
class CollegeEnrollOptionBlockView extends BlockView implements BlockViewInterface {
	/**
	 * Implement BlockViewInterface::gEtcontentlear().
	 */
	public function getContent() {
		extract($this->data);
		return <<<HTML
<form id="college-domain-form" name="college-domain">
	<div class="row">
		<select name="college-domain-options">
			<option value="default">Please select school.</option>
			<option value="msu.{$domain}">Michigan State University</option>
			<option value="umich.{$domain}">University of Michigan</option>
			<option value="wayne.{$domain}">Wayne State University</option>
		</select>
	</div>
</form>
HTML;
	}
}
