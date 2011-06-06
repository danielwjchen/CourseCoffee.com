<?php

/**
 * Represent a quest
 */
class QuestDAO extends DAO{

	/**
	 * Implement DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'objective',
      'type',
      'type_id',
			'description',
		);

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Implement DAO::create()
	 */
	public function create($params) {
		if (!isset($params['objective']) || 
        !isset($params['type']) ||
				!isset($params['description'])) 
		{
			throw new Exception('incomplete quest params');
			return ;

		} else {
      $this->attr = array(
        'type' => $params['type'],
        'objective' => $params['objective'],
        'description' => $params['description'],
      );

			parent::create("
			INSERT INTO `quest`
				(`objective`, `description`, `type_id`)
			VALUES (
				:objective,
				:description,
				(SELECT `id` FROM `quest_type` lt WHERE lt.name = :type)",
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
				q.*, 
				qt.name AS type
			FROM `quest` q
			INNER JOIN `quest_type` qt
				ON q.type_id = qt.id
		";

		if (isset($params['id'])) {
			$sql .= 'WHERE q.id = :id';
			$data = parent::read($sql, array('id' => $params['id']));

		} elseif (isset($params['name'])) {
			$sql .= 'WHERE q.objective = :objective';
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
			throw new Exception('unknown quest identifier');
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
			UPDATE `quest` q SET
				q.description = :description,
				q.objective = :objective,
				q.type_id = (SELECT qt.id FROM quest_type qt WHERE qt.name = :type)
			WHERE q.id = :id
		";

		parent::update($sql, array(
			'description' => $this->attr['description'],
			'objective' => $this->attr['objective'],
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
      DELETE 
        q, alink, llink, l, dlink, d, plink, p, ilink, i, mlink, n slink, s 
        FROM `quest` q
			LEFT JOIN `affiliation_quest_linkage` alink
				ON q.id = alink.quest_id
			LEFT JOIN `quest_location_linkage` llink
				ON q.id = llink.quest_id
      LEFT JOIN `location` l
        ON l.id = llink.location_id
			LEFT JOIN `quest_date_linkage` dlink
				ON q.id = dlink.quest_id
      LEFT JOIN `date` d
        ON d.id = dlink.date_id
			LEFT JOIN `quest_person_linkage` plink
        ON q.id = plink.quest_id
      LEFT JOIN `person` p
        ON p.id = plink.person_id
			LEFT JOIN `quest_item_linkage` ilink
				ON q.id = ilink.quest_id
      LEFT JOIN `item` i
        ON i.id = ilink.item_id
			LEFT JOIN `quest_message_linkage` mlink
				ON q.id = mlink.quest_id
      LEFT JOIN  `message` m
        ON m.id = mlink.message_id
			LEFT JOIN `quest_statistic_linkage` slink
				ON q.id = slink.quest_id
      LEFT JOIN `statistic` s
        ON s.id = slink.statistic_id
			WHERE q.id = :id';
		parent::destroy($sql, array('id' => $this->id));

	}


}
