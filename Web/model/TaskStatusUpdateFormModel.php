<?php
/**
 * @file
 * Oversee requests to update Task status
 *
 * This is NOT FINISHED!
 */
class TaskStatusUpdateFormModel extends Model {

	const TASK_TYPE   = 'status';
	const TASK_STATUS = 'done';

	private $task_status_dao;

	/**
	 * Extend Model::__construct()
	 */
	function __construct($sub_domain) {
		parent::__construct($sub_domain);
		$this->task_status_dao = new TaskStatusDAO($this->institution_db);
	}

	/**
	 * Process request
	 *
	 * @param int $user_id
	 * @param int $task_id
	 *
	 * @return array
	 */
	public function processForm($user_id, $task_id) {
		return $this->task_status_dao->set($user_id, $task_id, self::TASK_TYPE, self::TASK_STATUS);
	}

}
