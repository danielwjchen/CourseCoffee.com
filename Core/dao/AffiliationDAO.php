<?php

/**
 * Represent a affiliation
 */
class AffiliationDAO extends DAO{

	/**
	 * Implement DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'name',
			'url',
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
				!isset($params['url']) || 
				!isset($params['type'])) 
		{
			throw new Exception('incomplete affiliation params');
			return ;

		}

		$sql = "
			INSERT INTO `affiliation` (`name`, `url`, `type_id`)
			VALUES (
				:name,
				:url,
				(SELECT `id` FROM `affiliation_type` lt WHERE lt.name = :type)
			)
		";
		
		parent::create($sql, $params);
		$this->read($params);
	}

	/**
	 * Implement DAO::read()
	 */
	public function read($params) {
		$sql ="
			SELECT 
				a.*, 
				at.name AS type
			FROM `affiliation` a
			INNER JOIN `affiliation_type` at
				ON a.type_id = at.id
		";

		if (isset($params['id'])) {
			$sql .= 'WHERE a.id = :id';
			$data = parent::read($sql, array('id' => $params['id']));

		} elseif (isset($params['name'])) {
			$sql .= 'WHERE a.name = :name';
			$data = parent::read($sql, array('name' => $params['name']));

		} elseif (isset($params['type_id'])) {
			$sql .= "WHERE at.id = :type_id";
			$data = parent::read($sql, array(
				'type_id' => $params['type_id']
			));

		} elseif (isset($params['type'])) {
			$sql .= "WHERE at.name = :type";
			$data = parent::read($sql, array(
				'type' => $params['type']
			));

		} else {
			throw new Exception('unknown affiliation identifier');
			return ;

		}
		
		$this->attr = empty($data) ? $this->attr : $data;

	}

	/**
	 * Implement DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `affiliation` l SET
				l.name = :name,
				l.url = :url,
				l.type_id = (SELECT lt.id FROM affiliation_type lt WHERE lt.name = :type)
			WHERE l.id = :id
		";

		parent::update($sql, array(
			'name' => $this->attr['name'],
			'url' => $this->attr['url'],
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
			DELETE l, link FROM `affiliation` l
			INNER JOIN `quest_affiliation_linkage` link
				ON l.id = link.affiliation_id
			WHERE l.id = :id';
		parent::destroy($sql, array('id' => $this->id));

	}


}
