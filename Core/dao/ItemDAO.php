<?php

/**
 * Represent a item
 */
class ItemDAO extends DAO{

	/**
	 * Implement DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
      'type',
      'type_id',
			'name',
		);

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Implement DAO::create()
	 */
	public function create($params) {
		if (!isset($params['name']) || 
				!isset($params['type'])) 
		{
			throw new Exception('incomplete item params - ' . print_r($params, true));
			return ;

		} else {
      $this->attr['type'] = $params['type'];
      $this->attr['name'] = $params['name'];

			parent::create("
			INSERT INTO `item`
				(`name`, `type_id`)
			VALUES (
				:name,
				(SELECT `id` FROM `item_type` lt WHERE lt.name = :type)",
				array(
					'name' => $params['name'],
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
				i.*, 
				it.name AS type
			FROM `item` i
			INNER JOIN `item_type` it 
				ON i.type_id = it.id
		";

		if (isset($params['id'])) {
			$sql .= 'WHERE i.id = :id';
			$data = parent::read($sql, array('id' => $params['id']));

		} elseif (isset($params['name'])) {
			$sql .= strpos('%', $params['name']) ? 
				'WHERE i.name LIKE :name' : 'WHERE i.name = :name';

			$data = parent::read($sql, array('name' => $params['name']));

		} elseif (isset($params['type_id'])) {
			$sql .= "WHERE it.id = :type_id";
			$data = parent::read($sql, array(
				'type_id' => $params['type_id']
			));

		} elseif (isset($params['type'])) {
			$sql .= "WHERE it.name = :type";
			$data = parent::read($sql, array(
				'type' => $params['type']
			));

		} else {
			throw new Exception('unknown item identifier - ' . print_r($params, true));
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
			UPDATE `item` i SET
				i.name = :name,
				i.type_id = (SELECT it.id FROM item_type it WHERE it.name = :type)
			WHERE i.id = :id
		";

		parent::update($sql, array(
			'type' => $this->attr['type'],
			'name' => $this->attr['name'],
			'id' => $this->attr['id']
		));

		$this->read($this->attr);

	}

	/**
	 * Implement DAO::destroy()
	 */
	public function destroy() {
		$sql = '
      DELETE i, ia FROM `item` i
			LEFT JOIN `item_attribute` ia
				ON i.id = ia.item_id
			WHERE i.id = :id';
		parent::destroy($sql, array('id' => $this->id));

	}


}
