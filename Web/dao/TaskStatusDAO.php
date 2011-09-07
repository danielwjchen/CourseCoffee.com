<?php
/**
 * @file
 * Represents a task (sub-quest) with in a Quest.
 *
 * This DAO differs from others and does not implement DAOInterface.
 */
class TaskStatusDAO {

	/**
	 * Database connection object
	 */
	protected $db;

	/**
	 * Load a the data for child classes
	 */
	function __construct($db) {
		$this->db = $db;
	}

	public function set($user_id, $task_id, $type, $value) {
		$sql = "
		INSERT INTO `quest_attribute` (
			`quest_id`,
			`user_id`,
			`type_id`,
			`value`
		) VALUES (
			:task_id,
			:user_id,
			(SELECT `id` FROM `quest_attribute_type` WHERE `name` = :type),
			:value
		)
		";

		$this->db->insert($sql, array(
			'user_id' => $user_id,
			'task_id' => $task_id,
			'value' => $value,
			'type' => $type
		));

		$sql = "
			SELECT COUNT(qa.id) AS stats FROM `quest_attribute` qa
			INNER JOIN `quest_attribute_type` qat
				ON qa.type_id = qat.id
			WHERE qa.quest_id = :task_id
				AND qa.value = :value
				AND qat.name = :type
		";

		return $this->db->fetch($sql, array(
			'task_id' => $task_id,
			'value' => $value,
			'type' => $type,
		));
	}
}
