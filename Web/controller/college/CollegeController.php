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

	public function test($params = null) {
		echo "Hello World";
		print_r($params);

	}

}
