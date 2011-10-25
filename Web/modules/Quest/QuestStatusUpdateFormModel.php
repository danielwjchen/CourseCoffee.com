<?php
/**
 * @file
 * Oversee requests to update Task status
 *
 * This is NOT FINISHED!
 */
class TaskStatusUpdateFormModel extends Model {

	const STATUS_TYPE   = 'status';
	const STATUS_DONE   = 'done';
	const STATUS_HIDDEN = 'hidden';

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
	public function processForm($user_id, $task_id, $status = self::STATUS_DONE) {
		return $this->task_status_dao->set($user_id, $task_id, self::STATUS_TYPE, $status);
	}

}
