<?php
/**
 * @file
 * Handle institution API related controller logics
 */
class InstitutionAPIController extends APIController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'createInstitution' => array(
				'institution/create',
			),
			'removeInstitution' => array(
			 	'institution/remove',
			 ),
			'listInstitution' => array(
				'institution/list',
			),
		);
	}

	/**
	 * Create a new institution.
	 */
	public function createInstitution() {
	}

	/**
	 * Remove a institution
	 */
	public function removeInstitution() {
	}

	/**
	 * List institution
	 */
	public function listInstitution() {
	}

}
