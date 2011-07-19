<?php
/**
 * @file
 * Handle task creation and management
 */

class TaskController extends Controller implements ControllerInterface {
	
	/**
	 * Extend Controller::__construct()
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
			'task/add' => 'createTask',
			'task/update'   => 'updateTask',
			'task/remove'   => 'removeTask',
			'task/search' => 'searchTask',
			'task/detail' => 'getTaskDetail',
			'user/list-task'  => 'getTaskBelongToUser',
			'course-section/list-task' => 'getTaskBelongToClass',
			'calendar/list-task' => 'getTaskBelongToDate',
		);
	}

	/**
	 * Create a new task
	 */
	public function createTask() {
	}

	/**
	 * Update task's information
	 */
	public function updateTask() {
	}

	/**
	 * Remove a task
	 */
	public function removeTask() {
	}

	/**
	 * Search for a task and make suggestions
	 */
	public function searchTask() {
	}

	/**
	 * Get the detail of a task
	 */
	public function getTaskDetail() {
	}

	/**
	 * Get tasks belong to a user
	 */
	public function getTaskBelongToUser() {
	}

	/**
	 * Get task belong to a class
	 */
	public function getTaskBelongToClass() {
	}

	/**
	 * Get task belong to a time period
	 */
	public function getTaskkBelongToDate() {
	}

}
