<?php

/**
 * Represent a location
 */
class LocationDAO extends DAO implements DAOInterface{

	/**
	 * Extend DAO::__construct().
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
	 * Extend DAO::create()
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

			return $this->db->insert("
				INSERT INTO `location`
					(`name`, `longitude`, `latitude`, `type_id`)
				VALUES (
					:name,
					:longitude,
					:latitude,
					(SELECT id FROM location_type WHERE name = :type)
				)",
				array(
					'name' => $params['name'],
					'longitude' => $params['longitude'],
					'latitude' => $params['latitude'],
					'type' => $params['type'],
				)
			);
		}
		
	}

	/**
	 * Extend DAO::read()
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
			$data = $this->db->fetch($sql, array('id' => $params['id']));

		} elseif (isset($params['name'])) {
			$sql .= 'WHERE l.name = :name';
			$data = $this->db->fetch($sql, array('name' => $params['name']));

		} elseif (isset($params['type_id'])) {
			$sql .= "WHERE lt.id = :type_id";
			$data = $this->db->fetch($sql, array(
				'type_id' => $params['type_id']
			));

		} elseif (isset($params['type'])) {
			$sql .= "WHERE lt.name = :type";
			$data = $this->db->fetch($sql, array(
				'type' => $params['type']
			));

		} else {
			throw new Exception('unknown location identifier - ' . print_r($params, true));
			return ;

		}
		
		return $this->updateAttrribute($data);

	}

	/**
	 * Extend DAO::update()
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

		$this->db->perform($sql, array(
			'name' => $this->attr['name'],
			'latitude' => $this->attr['latitude'],
			'longitude' => $this->attr['longitude'],
			'type' => $this->attr['type'],
			'id' => $this->attr['id']
		));

	}

	/**
	 * Extend DAO::destroy()
	 */
	public function destroy() {
		$sql = '
			DELETE l, link FROM `location` l
			LEFT JOIN `affiliation_location_linkage` link
				ON l.id = link.location_id
			WHERE l.id = :id';
		$this->db->perform($sql, array('id' => $this->attr['id']));

	}

}
