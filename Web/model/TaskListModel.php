<?php
/**
 * @file
 * Manage access to task records
 */
class TaskListModel extends Model {
	/**
	 * Access to task record
	 */
	private $task_dao;

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->task_dao = new TaskDAO($this->db);
	}

	/**
	 * Fetch a list of task record for a user
	 *
	 * @param string $user_id
	 * @param int $paginate
	 *
	 * @return array
	 */
	public function fetchUserList($user_id, $paginate = 5) {
		$this->task_dao->read(array('user_id' => $user_id));
		$record_list = $this->task_dao->list;
		return array(
			'success' => true,
			'list'    => $record_list,
		);
	}

	/**
	 * Fetch a list of task record in a date range
	 *
	 * @param string $user_id
	 * @param int $begin_date
	 * @param int $end_date
	 *
	 * @return array
	 */
	public function fetchCalendarList($user_id, $begin_date = null, $end_date = null, $paginate = 5) {
		$this->task_dao->read(array(
			'user_id' => $user_id,
			'range'   => array(
				'begin_date' => $begin_date,
				'end_date'   => $end_date,
			),
		));
		$record_list = $this->task_dao->list;
		return array(
			'success' => true,
			'list'    => $record_list,
		);
	}
}
