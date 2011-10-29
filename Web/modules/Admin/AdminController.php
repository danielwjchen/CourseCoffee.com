<?php
/**
 * @file
 * Handle admin tasks
 */
class AdminController extends CourseCoffeeController implements ControllerInterface {
	
	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'admin'           => 'getAdminPage',
			'admin-quest-status'    => 'updateQuestStatus',
			'admin-syllabus-status' => 'updateSyllabusStatus',
			'admin-remove-all-quest' => 'removeQuestBelongToSection',
		);
	}

	/**
	 * Get the admin page for user
	 */
	public function getAdminPage() {
		$this->redirectUnsupportedDomain();
		$this->redirectUnknownUser();
		$this->output = new AdminPageView(array(
			'user_id'    => $this->user_session->getUserId(),
			'role'       => $this->user_session->getUserRole(),
			'profile'    => $profile,
			'class_list' => $class_list,
			'timestamp'  => time(),
		));
	}

	/**
	 * update task status
	 *
	 * This really needs to be authenticated
	 */
	public function updateQuestStatus() {
		$quest_id = Input::Post('quest_id');
		$status   = Input::Post('status');
		$quest_status_model = new QuestStatusModel($this->sub_domain);
		$result = $quest_status_model->updateQuestStatus($quest_id, $status);
		if ($result) {
			$this->output = new JSONView(array(
				'success' => true,
			));
		} else {
			$this->output = new JSONView(array(
				'error' => true,
			));
		}
	}

	/**
	 * Update syllabus status
	 */
	public function updateSyllabusStatus() {
		$section_id  = Input::Post('section_id');
		$status      = Input::Post('status');
		$section_model = new CurriculumClassModel($this->sub_domain);
		$result = $section_model->updateClassSyllabusStatus($section_id, $syllabus_id, $status);
		$this->output = new JSONView(array(
			'success' => true,
			'result' => $result,
		));
	}

	/**
	 * remove all quests belong to section
	 */
	public function removeQuestBelongToSection() {
		$section_id  = Input::Post('section_id');
		$task_list_model = new TaskListModel($this->sub_domain);
		$result = $task_list_model->removeAllQuestBelongToSection($section_id);
		if ($result) {
			$this->output = new JSONView(array(
				'success' => true,
				'result' => $result,
			));
		} else {
			$this->output = new JSONView(array(
				'error' => true,
				'result' => $result,
			));
		}
	}
}
