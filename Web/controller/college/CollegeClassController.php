<?php
/**
 * @file
 * Manage access to information of a class in college
 */
class CollegeClassController extends Controller implements ControllerInterface {

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
			'college-class/add' => 'createClass',
			'college-class/update'   => 'updateClass',
			'college-class/remove'   => 'removeClass',
			'college-class/search' => 'searchClass',
			'college-class/detail' => 'getClassDetail',
			'college-class/list' => 'getListOfClass',
			'college-class/enroll' => 'addUserToClass',
		);
	}

	/**
	 * Create a new class
	 */
	public function createClass() {
	}

	/**
	 * Update class's information
	 */
	public function updateClass() {
	}

	/**
	 * Remove a class
	 */
	public function removeClass() {
	}

	/**
	 * Search for a class
	 */
	public function searchClass() {
	}


	/**
	 * Get detail of a class()
	 */
	public function getClassDetail() {
	}
	/**
	 * Get a list of class
	 */
	public function getListOfClass() {
	}


	/**
	 * Add a user to a class
	 */
	public function addUserToClass() {
	}

}
