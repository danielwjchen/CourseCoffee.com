<?php

/**
 * Represent a institution
 */
class CollegeListDAO extends DAO implements DAOInterface{

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'name',
			'uri',
			'domain',
		);

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create()
	 */
	public function create($params) {
		if (!isset($params['name']) || 
				!isset($params['uri']) || 
				!isset($params['domain'])) 
		{
			throw new Exception('incomplete institution params');
			return ;

		}

		return $this->db->insert("
			INSERT INTO `institution` (`name`, `uri`, `domain`)
			VALUES (
				:name,
				:uri,
				:domain
			)",
			array(
				'name' => $params['name'],
				'uri' => $params['uri'],
				'domain' => $params['domain'],
			)
		);
	}

	/**
	 * Extend DAO::read()
	 *
	 * This differs from other DAOs as it fetches all the records
	 */
	public function read($params) {
		$sql ="SELECT * FROM `institution`";

		if (isset($params['id']) && !empty($params['id'])) {
			$sql .= 'WHERE id = :id';
			$data = $this->db->fetch($sql, array('id' => $params['id']));

		} elseif (isset($params['name'])) {
			$sql .= 'WHERE name = :name';
			$data = $this->db->fetch($sql, array('name' => $params['name']));

		} elseif (isset($params['domain'])) {
			$sql .= "WHERE domain = :domain";
			$data = $this->db->fetch($sql, array(
				'domain' => $params['domain']
			));

		} elseif (isset($params['uri'])) {
			$sql .= "WHERE uri = :uri";
			$data = $this->db->fetch($sql, array(
				'uri' => $params['uri']
			));

		} elseif (isset($params['all'])) {
			$this->list = $this->db->fetch($sql);
			return empty($this->list);

		}
		
		return $this->updateAttribute($data);

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `institution` SET
				`name` = :name,
				`uri` = :uri,
				`domain` = domain
			WHERE l.id = :id
		";

		$this->db->perform($sql, array(
			'name' => $this->attr['name'],
			'uri' => $this->attr['uri'],
			'domain' => $this->attr['domain'],
			'id' => $this->attr['id']
		));

	}

	/**
	 * Extend DAO::destroy()
	 */
	public function destroy() {
		$sql = 'DELETE FROM `institution` WHERE id = :id';
		$this->db->perform($sql, array('id' => $this->attr['id']));

	}


}
