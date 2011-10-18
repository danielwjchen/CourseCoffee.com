<?php
/**
 * @file
 * Handle quest API related controller logics
 */
class QuestAPIController extends APIController implements ControllerInterface {
	
	/**
	 * Implement ControllerInterface::path()
	 */
	public static function definePath() {
		return array(
			'quest-init'          => 'issueQuestToken',
			'quest-add'           => 'handleQuestCreation',
			'quest-add-from-doc'  => 'handleQuestCreationFromDoc',
			'quest-update'        => 'updateQuest',
			'quest-remove'        => 'removeQuest',
			'quest-search'        => 'searchQuest',
			'quest-detail'        => 'getQuestDetail',
			'quest-status-update' => 'updateQuestStatus',
			'user-list-quest'     => 'getQuestBelongToUser',
			'class-list-quest'    => 'getQuestBelongToClass',
			'calendar-list-quest' => 'getQuestBelongToDate',
		);
	}

	/**
	 * Override Controller::beforeAction()
	 */
	public function beforeAction() {
		//$this->redirectUnknownUser();
	}

	/**
	 * Issue a quest token
	 */
	public function issueQuestToken() {
		$quest = new QuestCreateFormModel($this->sub_domain);
		$this->output = new JSONView(array(
			'token' => $quest->initializeFormToken(),
		));
	}

	/**
	 * update quest status
	 *
	 * This really needs to be authenticated
	 */
	public function updateQuestStatus() {
		$user_id = $this->getUserId();
		$quest_id = Input::Post('quest_id');
		$status  = Input::Post('status');
		$quest_updater = new QuestStatusUpdateFormModel($this->sub_domain);
		$result = $quest_updater->processForm($user_id, $quest_id, $status);
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
	 * Create quest from a syllabus document
	 *
	 * This is a part of a procces which is decided by whether the user is in the 
	 * middle of creating an account, enrolling in a class, or uploading syllabus 
	 * for a class.
	 */
	public function handleQuestCreationFromDoc() {
		$quest_model  = new QuestCreateFormModel($this->sub_domain);
		$class_model = new CollegeClassModel($this->sub_domain);

		$quest_count = Input::Post('quest_count');
		$file_id    = Input::Post('file_id');
		$section_id = Input::Post('section_id');
		$user_id    = $this->getUserId();
		$creator_id = ($user_id !== false) ? $user_id : 1;// super user id

		if ($class_model->hasClassSyllabus($section_id)) {
			Logger::Write(CollegeClassModel::EVENT_ALREADY_HAS_SYLLABUS);
			$message = CollegeClassModel::ERROR_ALREADY_HAS_SYLLABUS;
		} else {
			for ($i = 0; $i < $quest_count; $i++) {
				$date      = Input::Post('date_' . $i);
				$objective = trim(preg_replace('/[^(\x20-\x7F)\x0A]*/', '', Input::Post('objective_' . $i)));
				$quest_model->createQuestFromDoc($creator_id, $objective, strtotime($date), $section_id);
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
	 * Create new quest
	 */
	public function handleQuestCreation() {
		$quest = new QuestCreateFormModel($this->sub_domain);

		$user_id     = $this->getUserId();
		$token       = Input::Post('token');
		$objective   = Input::Post('objective');
		$due_date    = Input::Post('due_date');
		$description = Input::Post('description');
		$section_id  = Input::Post('section_id');

		$result = $quest->processForm(
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
	 * Update quest's information
	 */
	public function updateQuest() {
	}

	/**
	 * Remove a quest
	 */
	public function removeQuest() {
	}

	/**
	 * Search for a quest and make suggestions
	 */
	public function searchQuest() {
	}

	/**
	 * Get the detail of a quest
	 */
	public function getQuestDetail() {
	}

	/**
	 * Get quests belong to a user
	 */
	public function getQuestBelongToUser() {
		$user_id  = $this->getUserId();
		$begin    = Input::Post('begin');
		$filter   = Input::Post('filter');
		$paginate = Input::Post('paginate');
		$list_model = new QuestListModel($this->sub_domain);
		$result = $list_model->fetchUserToDoList($user_id, $begin, $filter, $paginate);
		$this->output = new JSONView($result);
	}

	/**
	 * Get quest belong to a class
	 */
	public function getQuestBelongToClass() {
		$user_id    = $this->getUserId();
		$section_id = Input::Post('section_id');
		$filter   = Input::Post('filter');
		$paginate   = Input::Post('paginate');
		$list_model = new QuestListModel($this->sub_domain);
		$result = $list_model->fetchUserClassList($user_id, $section_id, $filter, $paginate);
		$this->output = new JSONView($result);
	}

	/**
	 * Get quest belong to a time period
	 */
	public function getQuestBelongToDate() {
		$user_id  = $this->getUserId();
		$begin    = Input::Post('begin');
		$end      = Input::Post('end');
		$filter   = Input::Post('filter');
		$paginate = Input::Post('paginate');
		$list_model = new QuestListModel($this->sub_domain);
		$result = $list_model->fetchUserCalendarList($user_id, $begin, $end, $filter, $paginate);
		$this->output = new JSONView($result);
	}

}
