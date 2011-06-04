<?php

/**
 * Represents a college course in database
 */
class CollegeCourseDAO extends DAO{

	/**
	 * Implement DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->db = $db;

		if (!empty($params)) {
			parent::__construct($sql, $params);
		}
	}

	/**
	 * Implement DAO::create()
	 */
	public function create($params) {
		$sql = "
			INSERT INTO `quest` (`objective`, `description`)
			VALUES (:objective, :description)
		";

		$this->db->perform($sql, array(
			'objective' => $params['title'],
			'description' => $params['description']
		));

		$linkage_sql = "
			INSERT INO `quest_linkage` link
				(`parent_quest_id`, `child_quest_id`)
			VALUES
				(:subject_id,
				(SELECT q.id FROM `quest` q 
					WHERE `objective` = :title 
						AND `description` = :description)
				)
		";

		$this->db->perform($linkage_sql, array(
			'objective' => $params['title'],
			'description' => $params['description']
		));
			
	}

	/**
	 * Implement DAO::read()
	 */
	public function read() {
	}

	/**
	 * Implement DAO::update()
	 */
	public function update() {
	}

	/**
	 * Implement DAO::destroy()
	 */
	public function destroy() {
	}

}
