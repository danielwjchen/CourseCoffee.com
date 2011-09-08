<?php
/**
 * @file
 * Handle task creation and management
 */

class TaskController extends Controller implements ControllerInterface {
	
	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
			'task-init'          => 'issueTaskToken',
			'task-add'           => 'handleTaskCreation',
			'task-add-from-doc'  => 'handleTaskCreationFromDoc',
			'task-update'        => 'updateTask',
			'task-remove'        => 'removeTask',
			'task-search'        => 'searchTask',
			'task-detail'        => 'getTaskDetail',
			'task-status-update' => 'updateTaskStatus',
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
	 * Issue a task token
	 */
	public function issueTaskToken() {
		$task = new TaskCreateFormModel($this->sub_domain);
		$this->output = new JSONView(array(
			'token' => $task->initializeFormToken(),
		));
	}

	/**
	 * update task status
	 *
	 * This really needs to be authenticated
	 */
	public function updateTaskStatus() {
		$user_id = $this->getUserId();
		$task_id = Input::Post('task_id');
		$task_updater = new TaskStatusUpdateFormModel($this->sub_domain);
		$result = $task_updater->processForm($user_id, $task_id);
		if ($result) {
			$result['success'] = true;
			$this->output = new JSONView($result);
		} else {
			$this->output = new JSONView(array(
				'error' => true,
				'stats' => $result
			));
		}
	}

	/**
	 * Create task from a syllabus document
	 *
	 * This is a part of a procces which is decided by whether the user is in the 
	 * middle of creating an account, enrolling in a class, or uploading syllabus 
	 * for a class.
	 */
	public function handleTaskCreationFromDoc() {
		$task_model  = new TaskCreateFormModel($this->sub_domain);
		$class_model = new CollegeClassModel($this->sub_domain);

		$task_count = Input::Post('task_count');
		$file_id    = Input::Post('file_id');
		$section_id = Input::Post('section_id');
		$user_id    = $this->getUserId();
		$creator_id = ($user_id !== false) ? $user_id : 1;// super user id

		if ($class_model->hasClassSyllabus($section_id)) {
			Logger::Write(CollegeClassModel::EVENT_ALREADY_HAS_SYLLABUS);
			$message = CollegeClassModel::ERROR_ALREADY_HAS_SYLLABUS;
		} else {
			for ($i = 0; $i < $task_count; $i++) {
				$date      = Input::Post('date_' . $i);
				$objective = trim(preg_replace('/[^(\x20-\x7F)\x0A]*/', '', Input::Post('objective_' . $i)));
				$task_model->createTask($creator_id, $objective, strtotime($date), $section_id);
			}

			$processor = new DocumentProcessorFormModel($this->sub_domain);
			$processor->setSectionSyllabus($section_id, $file_id);
			$message = CollegeClassModel::SYLLABUS_SUCCESS;

		}

		$this->output = new JSONView(array(
			'section_id' => $section_id,
			'message'    => $message,
		));
	}

	/**
	 * Create new task
	 */
	public function handleTaskCreation() {
		$task = new TaskCreateFormModel($this->sub_domain);

		$user_id     = $this->getUserId();
		$token       = Input::Post('token');
		$objective   = Input::Post('objective');
		$due_date    = Input::Post('due_date');
		$description = Input::Post('description');
		$section_id  = Input::Post('section_id');

		$result = $task->processForm(
			$token,
			$user_id, 
			$objective, 
			$due_date, 
			$section_id,
			$description
		);
		$this->output = new JSONView($result);
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
		$begin    = Input::Post('begin');
		$filter   = Input::Post('filter');
		$paginate = Input::Post('paginate');
		$list_model = new TaskListModel($this->sub_domain);
		$result = $list_model->fetchUserToDoList($user_id, $begin, $filter, $paginate);
		$this->output = new JSONView($result);
	}

	/**
	 * Get task belong to a class
	 */
	public function getTaskBelongToClass() {
		$user_id    = $this->getUserId();
		$section_id = Input::Post('section_id');
		$filter   = Input::Post('filter');
		$paginate   = Input::Post('paginate');
		$list_model = new TaskListModel($this->sub_domain);
		$result = $list_model->fetchUserClassList($user_id, $section_id, $filter, $paginate);
		$this->output = new JSONView($result);
	}

	/**
	 * Get task belong to a time period
	 */
	public function getTaskBelongToDate() {
		$user_id  = $this->getUserId();
		$begin    = Input::Post('begin');
		$end      = Input::Post('end');
		$filter   = Input::Post('filter');
		$paginate = Input::Post('paginate');
		$list_model = new TaskListModel($this->sub_domain);
		$result = $list_model->fetchUserCalendarList($user_id, $begin, $end, $filter, $paginate);
		$this->output = new JSONView($result);
	}

}
