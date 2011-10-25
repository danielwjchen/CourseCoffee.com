<?php
/**
 * @file
 * Handle Quest Status
 */
class QuestStatusModel extends Model {

	private $quest_dao;
	private $quest_status_dao;

	/**
	 * Extend Model::construct()
	 */
	function __construct($sub_domain) {
		parent::__construct($sub_domain);
		$this->quest_dao = new QuestDAO($this->institution_db);
		$this->quest_status_dao = new QuestStatusDAO($this->institution_db);
	}

	/**
	 * Mark quest status
	 */
	function updateQuestStatus($quest_id, $status) {
		/**
		if (!$this->quest_status_dao->read(array('name', $status))) {
			return array(
				'error'   => true,
				'message' => '',
			);
		}
		**/

		if (!$this->quest_dao->read(array('id' => $quest_id))) {
			return array(
				'error'   => true,
				'message' => '',
			);
		}

		//$this->quest_dao->status_id = $this->status_dao->id;
		$this->quest_dao->status = $status;
		return $this->quest_dao->update();
	}
}
