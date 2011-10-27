<?php
/**
 * @file
 * Define the footer information block
 */
class InstitutionSelectionBlockView extends BlockView implements BlockViewInterface {

	const TYPE_DOMAIN = 'domain';
	const TYPE_ID     = 'id';

	/**
	 * Implement BlockViewInterface::gEtcontentlear().
	 */
	public function getContent() {
		extract($this->data);
		$option = '';
		if ($selection_type == InstitutionSelectionBlockView::TYPE_DOMAIN) {
			foreach ($institution_list as $id => $value) {
				$option .= "<option value='{$value['domain']}.{$domain}'>{$value['name']}</option>";
			}
		} elseif ($selection_type == InstitutionSelectionBlockView::TYPE_ID) {
			foreach ($institution_list as $id => $value) {
				$option .= "<option value='{$id}'>{$value['name']}</option>";
			}
		}
		return <<<HTML
<form id="select-school-form" name="select-school">
	<div class="row">
		<select id="school-options">
			<option value="default">Please select school.</option>
			{$option}
		</select>
	</div>
</form>
HTML;
	}
}
