<?php
/**
 * @file
 * Handle task creation 
 */
class TaskCreateFormModel extends FormModel {

	/**
	 * @defgroup error_messages
	 * @{
	 * Error message for the user when an error is encountered
	 */
	const ERROR_FAILED_TO_CREATE = 'All servers down!!11!one!!';
	const ERROR_FORM_EMPTY       = 'The email and password fields cannot be empty';
	const ERROR_FORM_EXPIRED     = 'The form hasexpired. Please try again.';
	/**
	 * @} End of error_messages
	 */

	/**
	 * @defgroup even_messages
	 * @{
	 * Log messges to track events
	 */
	const EVENT_FAILED_TO_CREATE = 'Failed create task';
	const EVENT_FORM_EMPTY       = 'An empty task form is submitted. How is this possible?';
	const EVENT_FORM_EXPIRED     = 'Task creation form expired.';

	const ADMIN_USER_ID = 1;

	/**
	 * @} End of even_messages
	 */

	/**
	 * Access to task record
	 */
	private $task_dao;

	/**
	 * Extend Model::__construct()
	 */
	function __construct($sub_domain) {
		parent::__construct($sub_domain);
		$this->task_dao = new TaskDAO($this->institution_db);
		$this->form_name = 'task_creation_form';
		// form submission is limite to 5 times
		$this->max_try = 5;
		// form expires in an hour
		$this->expire = 3600;
	}

	public function createTaskFromDoc($user_id, $objective, $timestamp, $section_id ='', $description = '') {
		$task_id = $this->task_dao->create(array(
			'user_id'     => self::ADMIN_USER_ID,
			'objective'   => $objective,
			'due_date'    => $timestamp,
			'description' => $description,
			'section_id'  => $section_id,
		));
		// debug
		// error_log(__METHOD__ . $task_id);
		return $task_id;
	}


	/**
	 * Create task
	 *
	 * @param $user_id
	 * @param $objective
	 * @param $due_date
	 * @oaram $section_id
	 * @param $description
	 *
	 * @return int
	 *  the record id
	 */
	public function createTask($user_id, $objective, $timestamp, $section_id ='', $description = '') {
		$task_id = $this->task_dao->create(array(
			'user_id'     => $user_id,
			'objective'   => $objective,
			'due_date'    => $timestamp,
			'description' => $description,
			'section_id'  => $section_id,
		));

		// debug
		// error_log(__METHOD__ . $task_id);
		return $task_id;

	}


	/**
	 * Process task creation request
	 *
	 * @param $token
	 * @param $user_id
	 * @param $objective
	 * @param $due_date
	 * @oaram $section_id
	 * @param $description
	 *
	 * @return array
	 *  on success:
	 *   - success:
	 *   - quest_id:
	 *   - token:
	 *  on failure:
	 *  - error:
	 *  - message
	 *  - token
	 */
	public function processForm($token, $user_id, $objective, $due_date, $section_id = '', $description ='') {
		// if the form token has expired, this is more a study on user behavior. We 
		// might want to change the expire to a higher value if this happens too
		// often
		if (!$this->validateFormToken($token)) {
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_FORM_EXPIRED, Logger::SEVERITY_LOW);
			/*
			return array(
				'objective' => $objective,
				'due_date' => $due_date,
				'description' => $description,
				'token' => $token,
				'error' => self::ERROR_FORM_EXPIRED
			);
			*/
		}

		$this->unsetFormToken();
		$token = $this->initializeFormToken();
		$record_id = $this->createTask(
			$user_id,
			$objective,
			strtotime($due_date),
			$section_id,
			$description
		);

		if ($record_id != 0) {
			return array(
				'success'  => true,
				'quest_id' => $record_id,
				'token'    => $token,
			);
		} else {
			return array(
				'error'   => true,
				'message' => '',
				'token'   => $token,
			);
		}
	}
}

