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
	 *  On success:
	 *   - success:
	 *   - list:
	 *  On failure:
	 *   - error
	 *   - message
	 */
	public function fetchUserList($user_id, $paginate) {
		$has_record = $this->task_dao->read(array(
			'user_id' => $user_id,
			'limit'   => array(
				'offset' => $paginate * self::COUNT,
				'count'  => self::COUNT,
			),
		));
		if ($has_record) {
			$record_list = $this->task_dao->list;
			return array(
				'success' => true,
				'list'    => $record_list,
			);
		} else {
			return array(
				'error'   => true,
				'message' => '',
			);
		}
	}

	/**
	 * Fetch a list of task belong to a class section
	 *
	 * @param string $user_id
	 * @param int $section_id
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
	public function fetchUserClassList($user_id, $section_id, $paginate) {
		$has_record = $this->task_dao->read(array(
			'user_id' => $user_id,
			'section_id' => $section_id,
			'limit'   => array(
				'offset' => $paginate * self::COUNT,
				'count'  => self::COUNT,
			),
		));
		if ($has_record) {
			$record_list = $this->task_dao->list;
			return array(
				'success' => true,
				'list'    => $record_list,
			);
		} else {
			return array(
				'error'   => true,
				'message' => '',
			);
		}
	}

	/**
	 * Fetch a list of task record in a date range
	 *
	 * @param string $user_id
	 * @param int $begin_date
	 * @param int $end_date
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
	public function fetchUserCalendarList($user_id, $begin_date, $end_date, $paginate) {
		$has_record = $this->task_dao->read(array(
			'user_id' => $user_id,
			'range'   => array(
				'begin_date' => $begin_date,
				'end_date'   => $end_date,
			),
			/**
			 * We don't do pagination for now, because tasks should be limited by the 
			 * date range
			'limit'   => array(
				'offset' => $paginate * self::COUNT,
				'count'  => self::COUNT,
			),
			*/
		));
		if ($has_record) {
			$record_list = $this->task_dao->list;
			return array(
				'success' => true,
				'list'    => $record_list,
			);
		} else {
			return array(
				'error'   => true,
				'message' => '',
			);
		}
	}
}
