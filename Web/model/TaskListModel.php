<?php
/**
 * @file
 * Manage access to task records
 */
class TaskListModel extends Model {
	/**
	 * Number of records to fetch
	 */
	const COUNT = 10;

	const ERROR_NO_TASK = 'No scheduled assignments.';

	/**
	 * Access to task record
	 */
	private $task_list_dao;

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->task_list_dao = new TaskListDAO($this->db);
	}

	/**
	 * Fetch a list of task record for a user
	 *
	 * @param string $user_id
	 * @param int $filter
	 * @param int $paginate
	 *
	 * @return array
	 *  On success:
	 *   - success:
	 *   - list:
	 *  On failure:
	 *   - error
	 *   - message
	 */
	public function fetchUserToDoList($user_id, $begin_date, $filter, $paginate) {
		$has_record = $this->task_list_dao->read(array(
			'user_id' => $user_id,
			'filter'  => $filter,
			'range' => array(
				'begin_date' => $begin_date,
			),
			'limit'   => array(
				'offset' => $paginate * self::COUNT,
				'count'  => self::COUNT,
			),
		));
		if ($has_record) {
			$record_list = $this->task_list_dao->list;
			return array(
				'success' => true,
				'list'    => $record_list,
			);
		} else {
			return array(
				'error'   => true,
				'message' => self::ERROR_NO_TASK,
			);
		}
	}

	/**
	 * Fetch a list of task belong to a class section
	 *
	 * @param string $user_id
	 * @param int $section_id
	 * @param int $filter
	 * @param int $paginate
	 *
	 * @return array
	 *  On success:
	 *   - success:
	 *   - list:
	 *  On failure:
	 *   - error
	 *   - message
	 */
	public function fetchUserClassList($user_id, $section_id, $filter, $paginate) {
		$has_record = $this->task_list_dao->read(array(
			'section_id' => $section_id,
			'filter'  => $filter,
			'limit'   => array(
				'offset' => $paginate * self::COUNT,
				'count'  => self::COUNT,
			),
		));
		if ($has_record) {
			$record_list = $this->task_list_dao->list;
			return array(
				'success' => true,
				'list'    => $record_list,
			);
		} else {
			return array(
				'error'   => true,
				'message' => self::ERROR_NO_TASK,
			);
		}
	}

	/**
	 * Fetch a list of task record in a date range
	 *
	 * @param string $user_id
	 * @param int $begin_date
	 * @param int $end_date
	 * @param int $filter
	 * @param int $paginate
	 *
	 * @return array
	 *  On success:
	 *   - success:
	 *   - list:
	 *  On failure:
	 *   - error
	 *   - message
	 */
	public function fetchUserCalendarList($user_id, $begin_date, $end_date, $filter, $paginate) {
		$has_record = $this->task_list_dao->read(array(
			'user_id' => $user_id,
			'filter'  => $filter,
			'range'   => array(
				'begin_date' => $begin_date,
				'end_date'   => $end_date,
			),
			/**
			 * We don't do pagination for now, because tasks should be limited by the 
			 * date range
			 */
			'limit'   => array(
				'offset' => $paginate * self::COUNT * 10,
				'count'  => self::COUNT * 10,
			),
		));
		if ($has_record) {
			$record_list = $this->task_list_dao->list;
			return array(
				'success' => true,
				'list'    => $record_list,
			);
		} else {
			return array(
				'error'   => true,
				'message' => self::ERROR_NO_TASK,
			);
		}
	}
}
