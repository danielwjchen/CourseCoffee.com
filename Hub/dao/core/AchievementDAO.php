<?php

/**
 * Represent a achievement
 */
class AchievementDAO extends DAO implements DAOInterface{

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'name',
			'metric',
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
				!isset($params['metric']) || 
				!isset($params['type'])) 
		{
			throw new Exception('incomplete achievement params');
			return ;

		}

		parent::create("
			INSERT INTO `achievement`
				(`name`, `metric`, `type_id`)
			VALUES (
				:name,
				:metric,
				(SELECT id FROM `achievement_type` WHERE name = :type)
			)",
			array(
				'name' => $params['name'],
				'metric' => $params['metric'],
				'type' => $params['type']
			)
		);
		
	}

	/**
	 * Extend DAO::read()
	 */
	public function read($params) {
		$sql ="
			SELECT 
				l.*, 
				lt.name AS type
			FROM `achievement` l
			INNER JOIN `achievement_type` lt
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
			throw new Exception('unknown achievement identifier');
			return ;

		}

		return $this->updateAttrribute($data);

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `achievement` l SET
				l.name = :name,
				l.metric = :metric,
				l.type_id = (SELECT lt.id FROM achievement_type lt WHERE lt.name = :type)
			WHERE l.id = :id
		";

		parent::update($sql, array(
			'name' => $this->attr['name'],
			'metric' => $this->attr['metric'],
			'type' => $this->attr['type'],
			'id' => $this->attr['id']
		));

	}

	/**
	 * Extend DAO::destroy()
	 */
	public function destroy() {
		$sql = '
			DELETE a, link FROM `achievement` a
			LEFT JOIN `quest_achievement_linkage` link
				ON a.id = link.achievement_id
			WHERE a.id = :id';
		parent::destroy($sql, array('id' => $this->id));

	}


}
