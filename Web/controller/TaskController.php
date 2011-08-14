<?php
/**
 * @file
 * Handle task creation and management
 */

class TaskController extends Controller implements ControllerInterface {

	private $json;
	
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
			'task-init'          => 'issueTaskToken',
			'task-add'           => 'createTask',
			'task-bulk-add'      => 'createMultipleTask',
			'task-update'        => 'updateTask',
			'task-remove'        => 'removeTask',
			'task-search'        => 'searchTask',
			'task-detail'        => 'getTaskDetail',
			'user-list-task'     => 'getTaskBelongToUser',
			'class-list-task'    => 'getTaskBelongToClass',
			'calendar-list-task' => 'getTaskBelongToDate',
		);
	}

	/**
	 * Override Controller::beforeAction()
	 */
	public function beforeAction() {
		//$this->redirectUnknownUser();
	}

	/**
	 * Implement ControllerInterface::afterAction()
	 */
	public function afterAction() {
		$this->json->setHeader(PageView::HTML_HEADER);
		echo $this->json->render();
	}

	/**
	 * Issue a task token
	 */
	public function issueTaskToken() {
		$task = new TaskCreateFormModel();
		$this->json = new JSONView(array(
			'token' => $task->initializeFormToken(),
		));
	}

	/**
	 * Create new task
	 */
	public function createMultipleTask() {
		$task = new TaskCreateFormModel();
		$task_count = Input::Post('task_count');
		$section_id = Input::Post('section_id');
		$user_id    = 1;// super user id
		for ($i = 0; $i < $task_count; $i++) {
			$date      = Input::Post('date_' . $i);
			$objective = Input::Post('objective_' . $i);
			$task->processMultipleForm($user_id, $objective, $date, $section_id);

		}
		$this->json = new JSONView(array(
			'section_id' => $section_id,
			'message'    => 'Congradulation! The syllabus is now uploaded!'
		));
	}

	/**
	 * Create new task
	 */
	public function createTask() {
		$task = new TaskCreateFormModel();

		$user_id     = Session::Get('user_id');
		$token       = Input::Post('token');
		$objective   = Input::Post('objective');
		$due_date    = Input::Post('due_date');
		$description = Input::Post('description');
		$quest_id    = Input::Post('quest_id');

		$result = $task->processForm(
			$token,
			$user_id, 
			$objective, 
			$due_date, 
			$description, 
			$quest_id
		);
		$this->json = new JSONView($result);
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
		$user_id  = $this->getUserId();
		$paginate = Input::Post('paginate');
		$list_model = new TaskListModel();
		$result = $list_model->fetchUserList($user_id, $paginate);
		$this->json = new JSONView($result);
	}

	/**
	 * Get task belong to a class
	 */
	public function getTaskBelongToClass() {
		$user_id    = $this->getUserId();
		$section_id = Input::Post('section_id');
		$paginate   = Input::Post('paginate');
		$list_model = new TaskListModel();
		$result = $list_model->fetchUserClassList($user_id, $section_id, $paginate);
		$this->json = new JSONView($result);
	}

	/**
	 * Get task belong to a time period
	 */
	public function getTaskBelongToDate() {
		$user_id  = $this->getUserId();
		$begin    = Input::Post('begin');
		$end      = Input::Post('end');
		$paginate = Input::Post('paginate');
		$list_model = new TaskListModel();
		$result = $list_model->fetchUserCalendarList($user_id, $begin, $end, $paginate);
		$this->json = new JSONView($result);
	}

}
