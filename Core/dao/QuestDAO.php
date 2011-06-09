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
      'type',
      'type_id',
			'user_id',
			'objective',
			'description',
		);

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Implement DAO::create()
	 */
	public function create($params) {
		if (!isset($params['objective']) || 
				!isset($params['user_id']) ||
        !isset($params['type']) ||
				!isset($params['description'])) 
		{
			throw new Exception('incomplete quest params - ' . print_r($params, true));
			return ;

		} else {
      $this->attr['type'] = $params['type'];
			$this->attr['user_id'] = $params['user_id'];
      $this->attr['objective'] = $params['objective'];
      $this->attr['description'] = $params['description'];

			parent::create("
			INSERT INTO `quest`
				(`objective`, `description`, `user_id`, `type_id`)
			VALUES (
				:objective,
				:description,
				:user_id,
				(SELECT `id` FROM `quest_type` lt WHERE lt.name = :type)",
				array(
					'type' => $params['type'],
					'user_id' => $params['user_id'],
					'objective' => $params['objective'],
					'description' => $params['description'],
				)
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

		} elseif (isset($params['user_id'])) {
			$sql .= 'WHERE q.user_id = :user_id';
			$data = parent::read($sql, array('user_id' => $params['user_id']));

		} elseif (isset($params['objective'])) {
			$sql .= 'WHERE q.objective LIKE %:objective%';
			$data = parent::read($sql, array('objective' => $params['objective']));

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
				q.user_id = :user_id,
				q.description = :description,
				q.objective = :objective,
				q.type_id = (SELECT qt.id FROM quest_type qt WHERE qt.name = :type)
			WHERE q.id = :id
		";

		parent::update($sql, array(
			'description' => $this->attr['description'],
			'objective' => $this->attr['objective'],
			'type' => $this->attr['type'],
			'user_id' => $this->attr['user_id'],
			'id' => $this->attr['id']
		));

		$this->read($this->attr);

	}

	/**
	 * Implement DAO::destroy()
	 */
	public function destroy() {
		$sql = '
      DELETE q, qa FROM `quest` q
			INNER JOIN `quest_attribute` qa
				ON q.id = qa.quest_id
			WHERE q.id = :id';
		parent::destroy($sql, array('id' => $this->id));

	}


}
