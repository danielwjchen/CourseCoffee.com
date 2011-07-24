<?php
/**
 * @file
 * Handle task creation and management
 */

class TaskController extends Controller implements ControllerInterface {

	private $json_view;
	
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
			'task/add'                 => 'createTask',
			'task/update'              => 'updateTask',
			'task/remove'              => 'removeTask',
			'task/search'              => 'searchTask',
			'task/detail'              => 'getTaskDetail',
			'user/list-task'           => 'getTaskBelongToUser',
			'course-section/list-task' => 'getTaskBelongToClass',
			'calendar/list-task'       => 'getTaskBelongToDate',
		);
	}

	/**
	 * Override Controller::beforeAction()
	 */
	public function beforeAction() {
		$this->redirectUnknownUser();
	}

	/**
	 * Implement ControllerInterface::afterAction()
	 */
	public function afterAction() {
		$this->json_view->setHeader(PageView::HTML_HEADER);
		echo $this->json_view->render();
	}

	/**
	 * Create a new task
	 */
	public function createTask() {
		$create_form = new TaskCreateFormModel();

		$user_id     = Session::Get('user_id');
		$token   = Input::Post('token');
		$objective   = Input::Post('objective');
		$due_date    = Input::Post('due_date');
		$description = Input::Post('description');
		$quest_id    = Input::Post('quest_id');

		$result = $create_form->processForm(
			$token,
			$user_id, 
			$objective, 
			$due_date, 
			$description, 
			$quest_id
		);
		$this->json_view = new JSONView($result);
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
		$user_id = Session::Get('user_id');
		$list_model = new TaskListModel();
		$result = $list_model->fetchList($user_id);
		$this->json_view = new JSONView($result);
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
