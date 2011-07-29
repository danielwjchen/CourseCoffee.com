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
	 * Fetch a list of task record
	 *
	 * @param string $user)id
	 *
	 * @return array
	 */
	public function fetchList($user_id, $paginate = 5) {
		$this->task_dao->read(array('user_id' => $user_id));
		$record_list = $this->task_dao->list;
		return array(
			'success' => true,
			'list'    => $record_list,
		);
	}
}
