<?php

/**
 * Represent a affiliation
 */
class AffiliationDAO extends DAO implements DAOInterface{

	/**
	 * Extend DAO::__construct().
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
	 * Extend DAO::create()
	 */
	public function create($params) {
		if (!isset($params['name']) || 
				!isset($params['url']) || 
				!isset($params['type'])) 
		{
			throw new Exception('incomplete affiliation params');
			return ;

		}

		return parent::create("
			INSERT INTO `affiliation` (`name`, `url`, `type_id`)
			VALUES (
				:name,
				:url,
				(SELECT `id` FROM `affiliation_type` lt WHERE lt.name = :type)
			)",
			array(
				'name' => $params['name'],
				'url' => $params['url'],
				'type' => $params['type'],
			)
		);
	}

	/**
	 * Extend DAO::read()
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

		if (isset($params['id']) && !empty($params['id'])) {
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
		
		return $this->updateAttrribute($data);

	}

	/**
	 * Extend DAO::update()
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

	}

	/**
	 * Extend DAO::destroy()
	 */
	public function destroy() {
		$sql = '
			DELETE l, link FROM `affiliation` l
			LEFT JOIN `quest_affiliation_linkage` link
				ON l.id = link.affiliation_id
			WHERE l.id = :id';
		parent::destroy($sql, array('id' => $this->id));

	}


}
