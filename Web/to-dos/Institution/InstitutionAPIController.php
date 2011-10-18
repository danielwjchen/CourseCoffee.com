<?php
/**
 * @file
 * Handle institution API related controller logics
 */
class InstitutionAPIController extends APIController implements ControllerInterface {

	protected $college;

	/**
	 * Extend Controller::__construct()
	 */
	function __construct() {
		parent::__construct();
		//$this->college = Factory::Model('Institution');

	}
	
	/**
	 * Implement ControllerInterface::definePath()
	 */
	public static function definePath() {
		return array(
			'college/create' => 'createInstitution',
			'college/remove' => 'removeInstitution',
			'college/list'   => 'listInstitution',
		);
	}

	/**
	 * Create a new college.
	 */
	public function createInstitution() {
	}

	/**
	 * Remove a college
	 */
	public function removeInstitution() {
	}

	/**
	 * List college
	 */
	public function listInstitution() {
	}

}
