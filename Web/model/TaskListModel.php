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
	 * We don't do pagination for now, because tasks should be limited by the 
	 * date range. However, we do want to limit the result to 100 records since
	 * more will crash the browser.
	 */
	const CALENDAR_TASK_LIMIT = 100;

	const ERROR_NO_TASK = 'No scheduled assignments.';

	/**
	 * Access to task record
	 */
	private $task_list_dao;
	private $task_status;

	/**
	 * Extend Model::__construct()
	 */
	function __construct($sub_domain) {
		parent::__construct($sub_domain);
		$this->task_list_dao = new TaskListDAO($this->institution_db);
		$this->task_status = array(QuestStatusSetting::PENDING, QuestStatusSetting::APPROVED);
	}

	/**
	 * Fetch a list of task record for a user
	 *
	 * @param int $user_id
	 * @param int $begin_date
	 * @param string $filter
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
			'status'  => $this->task_status,
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
			'user_id'    => $user_id,
			'section_id' => $section_id,
			'filter'     => $filter,
			'status'     => $this->task_status,
			'limit' => array(
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
			'status'  => $this->task_status,
			'range'   => array(
				'begin_date' => $begin_date,
				'end_date'   => $end_date,
			),
			/**
			 */
			'limit'   => array(
				'offset' => 0,
				'count'  => TaskListModel::CALENDAR_TASK_LIMIT,
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
