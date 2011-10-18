<?php
/**
 * @file
 * Handle admin tasks
 */
class AdminAPIController extends APIController implements ControllerInterface {
	
	/**
	 * Implement ControllerInterface::path()
	 */
	public static function definePath() {
		return array(
			'admin-quest-status'    => 'updateQuestStatus',
			'admin-syllabus-status' => 'updateSyllabusStatus',
			'admin-remove-all-quest' => 'removeQuestBelongToSection',
		);
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
		$section_model = new CollegeClassModel($this->sub_domain);
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
