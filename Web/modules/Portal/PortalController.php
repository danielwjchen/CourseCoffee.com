<?php
/**
 * @file
 * Handle CourseCoffee /portal request
 */
class PortalController extends CourseCoffeeController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'portal' => 'getPortalPage',
		);
	}

	/**
	 * Get portal page
	 *
	 * The portal page serves as the redirect page when there is no requested 
	 * subdomain, or the request does not map to a campus site
	 */
	public function getPortalPage() {
		$institution   = new InstitutionListModel($this->sub_domain);
		$this->output = new PortalPageView(array(
			'institution_list' => $institution->getInstitutionList(),
			'selection_type' => InstitutionSelectionBlockView::TYPE_DOMAIN,
			'domain' => $this->domain,
		));
	}
}
