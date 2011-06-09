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

			if (isset($params['date'])) {
				$this->attr['date'] = $params['date'];
				$date = factory::DAO('date');
				$linkage = factory::DAO('quest_date_linkage');
				foreach ($params['date'] as $type => $timestamp) {
					$date->create(array('type' => $type, 'timestamp' => $timestamp));
					$linkage->create(array('date_id' => $date->id, 'quest_id' => $quest['id']));
				}
			}

			if (isset($params['location'])) {
				$this->attr['location'] = $params['location'];
				$location = factory::DAO('location');
				$linkage = factory::DAO('quest_location_linkage');
				foreach ($params['location'] as $type => $timestamp) {
					$location->create(array('type' => $type, 'timestamp' => $timestamp));
					$linkage->create(array('location_id' => $location->id, 'quest_id' => $quest['id']));
				}
			}

			if (isset($params['person'])) {
				$this->attr['person'] = $params['person'];
				$person = factory::DAO('person');
				$linkage = factory::DAO('quest_person_linkage');
				foreach ($params['person'] as $type => $timestamp) {
					$person->create(array('type' => $type, 'timestamp' => $timestamp));
					$linkage->create(array('person_id' => $person->id, 'quest_id' => $quest['id']));
				}
			}
			
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
