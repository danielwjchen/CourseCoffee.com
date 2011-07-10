<?php

/**
 * Represent a quest_attribute
 */
class QuestAttributeDAO extends DAO{

	/**
	 * Implement DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'value',
			'quest_id',
			'type',
			'type_id',
		);

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Implement DAO::create()
	 */
	public function create($params) {
		if (!isset($params['value']) || 
				!isset($params['quest_id']) || 
				!isset($params['type'])) 
		{
			throw new Exception('incomplete quest_attribute params - '. print_r($params, true));
			return ;

		} else {
      $this->attr = array(
        'value' => $params['value'],
				'quest_id' => $params['quest_id'],
        'type' => $params['type'],
      );

			parent::create("
				INSERT INTO `quest_attribute`
					(`value`, `quest_id`, `type_id`)
				VALUES (
					:value,
					:quest_id,
					(SELECT `id` FROM `quest_attribute_type` WHERE name = :type)",
				array(
					'value' => $params['value'],
					'quest_id' => $params['quest_id'],
					'type' => $params['type'],
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
				qa.*, 
				qat.id AS type_id,
				qat.name AS type
			FROM `quest_attribute` qa
			INNER JOIN `quest_attribute_type` qat
				ON qa.type_id = qat.id
		";

		if (isset($params['id'])) {
			$sql .= 'WHERE qa.id = :id';
			$data = parent::read($sql, array('id' => $params['id']));

		} elseif (isset($params['quest_id']) && isset($params['type_id'])) {
			$sql .= 'WHERE qa.quest_id = :quest_id AND qat.id = :type_id';
			$data = parent::read($sql, array(
				'quest_id' => $params['quest_id'],
				'type_id' => $params['type_id']
			));

		} elseif (isset($params['quest_id']) && isset($params['type'])) {
			$sql .= 'WHERE qa.quest_id = :quest_id AND qat.name = :type';
			$data = parent::read($sql, array(
				'quest_id' => $params['quest_id'],
				'type' => $params['type']
			));

		} elseif (isset($params['quest_id'])) {
			$sql .= 'WHERE qa.quest_id = :quest_id';
			$data = parent::read($sql, array('quest_id' => $params['quest_id']));

		} elseif (isset($params['type_id'])) {
			$sql .= "WHERE qat.id = :type_id";
			$data = parent::read($sql, array(
				'type_id' => $params['type_id']
			));

		} elseif (isset($params['type'])) {
			$sql .= "WHERE qat.name = :type";
			$data = parent::read($sql, array(
				'type' => $params['type']
			));

		} else {
			throw new Exception('unknown quest_attribute identifier - ' . print_r($params, true));
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
			UPDATE `quest_attribute` SET
				value = :value,
				quest_id = :quest_id,
				type_id = (SELECT id FROM quest_attribute_type WHERE name = :type)
			WHERE id = :id
		";

		parent::update($sql, array(
			'value' => $this->attr['value'],
			'type' => $this->attr['type'],
			'quest_id' => $this->attr['quest_id'],
			'id' => $this->attr['id']
		));

		$this->read($this->attr);

	}

	/**
	 * Implement DAO::destroy()
	 */
	public function destroy() {
		$sql = 'DELETE FROM `quest_attribute` WHERE id = :id';
		parent::destroy($sql, array('id' => $this->id));

	}

}
