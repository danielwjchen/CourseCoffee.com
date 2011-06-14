<?php

/**
 * Represent a list of books for a college course section
 */
class CollegeCourseSectionBookListDAO extends DAO{

	/**
	 * Implement DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'name',
			'longitude',
			'latitude',
			'type',
			'type_id',
		);

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Implement DAO::create()
	 */
	public function create($params) {
		if (!isset($params['name']) || 
				!isset($params['longitude']) || 
				!isset($params['latitude']) || 
				!isset($params['type'])) 
		{
			throw new Exception('incomplete location params');
			return ;

		} else {
      $this->attr = array(
        'name' => $params['name'],
        'longitude' => $params['longitude'],
        'latitude' => $params['latitude'],
        'type' => $params['type'],
      );

			parent::create("
			INSERT INTO `location`
				(`name`, `longitude`, `latitude`, `type_id`)
			VALUES (
				:name,
				:longitude,
				:latitude,
				(SELECT `id` FROM `location_type` lt WHERE lt.name = :type)",
				$this->attr
			);
		}
		
	}

	/**
	 * Implement DAO::read()
	 */
	public function read($params) {
		$sql ="
			SELECT 
				l.*, 
				lt.name AS type
			FROM `location` l
			INNER JOIN `location_type` lt
				ON l.type_id = lt.id
		";

		if (isset($params['id'])) {
			$sql .= 'WHERE l.id = :id';
			$data = parent::read($sql, array('id' => $params['id']));

		} elseif (isset($params['name'])) {
			$sql .= 'WHERE l.name = :name';
			$data = parent::read($sql, array('name' => $params['name']));

		} elseif (isset($params['type_id'])) {
			$sql .= "WHERE lt.id = :type_id";
			$data = parent::read($sql, array(
				'type_id' => $params['type_id']
			));

		} elseif (isset($params['type'])) {
			$sql .= "WHERE lt.name = :type";
			$data = parent::read($sql, array(
				'type' => $params['type']
			));

		} else {
			throw new Exception('unknown location identifier - ' . print_r($params, true));
			return ;

		}
		
		if (!empty($data)) {
			foreach ($this->attr as $key => $value) {
				$this->attr[$key] = isset($data[$key]) ? $data[$key] : null;

			}

		}

	}

	/**
	 * Implement DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `location` l SET
				l.name = :name,
				l.latitude = :latitude,
				l.longitude = :longitude,
				l.type_id = (SELECT lt.id FROM location_type lt WHERE lt.name = :type)
			WHERE l.id = :id
		";

		parent::update($sql, array(
			'name' => $this->attr['name'],
			'latitude' => $this->attr['latitude'],
			'longitude' => $this->attr['longitude'],
			'type' => $this->attr['type'],
			'id' => $this->attr['id']
		));
		$this->read($this->attr);

	}

	/**
	 * Implement DAO::destroy()
	 */
	public function destroy() {
		$sql = '
			DELETE l, link FROM `location` l
			INNER JOIN `affiliation_location_linkage` link
				ON l.id = link.location_id
			WHERE l.id = :id';
		parent::destroy($sql, array('id' => $this->id));

	}


}
