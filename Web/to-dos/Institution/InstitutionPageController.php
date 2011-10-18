<?php
/**
 * @file
 */
class InstitutionPageController extends PageController implements ControllerInterface {

	/**
	 * Override PageController::definePath()
	 */
	public static function definePath() {
		return array(
			'portal' => 'getPortalPage',
		);
	}

	/**
	 * Get portal page
	 *
	 * The portal page serves as the redirect page when there is no requested 
	 * subdomain/campus, or the request does not map to a campus site
	 */
	public function getPortalPage() {
		$model = new InstitutionListModel($this->sub_domain);
		$this->output = new PortalPageView(array(
			'institution_list' => $model->getInstitutionList(),
			'domain' => $this->domain,
		));
	}
}
