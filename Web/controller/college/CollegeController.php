<?php

class CollegeController extends Controller implements ControllerInterface{

	protected $college;

	/**
	 * Extend Controller::__construct()
	 */
	function __construct() {
		parent::__construct();
		//$this->college = Factory::Model('College');

	}
	
	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
			'college/create' => 'createCollege',
			'college/remove' => 'removeCollege',
			'college/list'   => 'listCollege',
		);
	}

	/**
	 * Create a new college.
	 */
	public function createCollege() {
	}

	/**
	 * Remove a college
	 */
	public function removeCollege() {
	}

	/**
	 * List college
	 */
	public function listCollege() {
	}

}
