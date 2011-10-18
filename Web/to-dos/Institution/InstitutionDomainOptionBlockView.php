<?php
/**
 * @file
 * Generate a list of institutions and their respective sub-domain for selection
 */
class InstitutionDomainOptionBlockView extends BlockView implements BlockViewInterface {

	/**
	 * Generate an option list from an array
	 *
	 * @param array $institution_list
	 * @param string $domain
	 */
	private generateOption($institution_list, $domain) {
		$html = '';
		foreach ($institution_list as $id => $value) {
			$html += <<<HTML
<option value="{$value['domain']}.{$domain}">{$value['name']}</option>
HTML;
		}
		return $html;
	}

	/**
	 * Implement BlockViewInterface::getContent().
	 */
	public function getContent() {
		$this->addJS('Instition/js/institution-domain-selector.js');
		$this->addCSS('Institution/js/institution-selector.css');
		extract($this->data);
		$options = $this->generateOption($institution_list, $domain);
		return <<<HTML
<form id="institution-domain-form" name="institution-domain">
	<div class="row">
		<select name="institution-domain-options">
			{$options}
		</select>
	</div>
</form>
HTML;
	}
}
