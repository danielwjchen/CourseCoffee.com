<?php

/**
 * Represent a item_attribute
 */
class ItemAttributeDAO extends DAO{

	/**
	 * Implement DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'value',
			'item_id',
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
				!isset($params['item_id']) || 
				!isset($params['type'])) 
		{
			throw new Exception('incomplete item_attribute params - '. print_r($params, true));
			return ;

		} else {
      $this->attr = array(
        'value' => $params['value'],
				'item_id' => $params['item_id'],
        'type' => $params['type'],
      );

			parent::create("
				INSERT INTO `item_attribute`
					(`value`, `item_id`, `type_id`)
				VALUES (
					:value,
					:item_id,
					(SELECT `id` FROM `item_attribute_type` WHERE name = :type)",
				array(
					'value' => $params['value'],
					'item_id' => $params['item_id'],
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
			FROM `item_attribute` qa
			INNER JOIN `item_attribute_type` qat
				ON qa.type_id = qat.id
		";

		if (isset($params['id'])) {
			$sql .= 'WHERE qa.id = :id';
			$data = parent::read($sql, array('id' => $params['id']));

		} elseif (isset($params['item_id'])) {
			$sql .= 'WHERE qa.item_id = :item_id';
			$data = parent::read($sql, array('item_id' => $params['item_id']));

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
			throw new Exception('unknown item_attribute identifier - ' . print_r($params, true));
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
			UPDATE `item_attribute` SET
				value = :value,
				item_id = :item_id,
				type_id = (SELECT id FROM item_attribute_type WHERE name = :type)
			WHERE id = :id
		";

		parent::update($sql, array(
			'value' => $this->attr['value'],
			'type' => $this->attr['type'],
			'item_id' => $this->attr['item_id'],
			'id' => $this->attr['id']
		));

		$this->read($this->attr);

	}

	/**
	 * Implement DAO::destroy()
	 */
	public function destroy() {
		$sql = 'DELETE FROM `item_attribute` WHERE id = :id';
		parent::destroy($sql, array('id' => $this->id));

	}

}
