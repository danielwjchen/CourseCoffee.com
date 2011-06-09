<?php

/**
 * Represent a date
 */
class DateDAO extends DAO{

	/**
	 * Implement DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'timestamp',
			'type',
			'type_id',
		);

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Implement DAO::create()
	 */
	public function create($params) {
		if (!isset($params['timestamp']) || 
				!isset($params['type'])) 
		{
			throw new Exception('incomplete date params');
			return ;

		} else {
      $this->attr = array(
        'timestamp' => $params['timestamp'],
        'type' => $params['type'],
      );

			parent::create("
			INSERT INTO `date` AS d
				(d.timestamp, d.type_id)
			VALUES (
				:timestamp,
				(SELECT `id` FROM `date_type` dt WHERE dt.name = :type)",
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
				d.*, 
				dt.name AS type
			FROM `date` d
			INNER JOIN `date_type` dt
				ON d.type_id = dt.id
		";

		if (isset($params['id'])) {
			$sql .= 'WHERE d.id = :id';
			$data = parent::read($sql, array('id' => $params['id']));

		} elseif (isset($params['timestamp']) && isset($params['type_id'])) {
			$sql .= 'WHERE d.timestamp = :timestamp AND d.type_id = :type_id';
			$data = parent::read($sql, array(
				'timestamp' => $params['timestamp'],
				'type_id' => $params['type_id']
			));

		} elseif (isset($params['timestamp']) && isset($params['type'])) {
			$sql .= 'WHERE d.timestamp = :timestamp AND dt.name = :name';
			$data = parent::read($sql, array(
				'timestamp' => $params['timestamp'],
				'name' => $params['type']
			));

		} elseif (isset($params['timestamp'])) {
			$sql .= 'WHERE d.timestamp = :timestamp';
			$data = parent::read($sql, array('timestamp' => $params['timestamp']));

		} elseif (isset($params['type_id'])) {
			$sql .= "WHERE dt.id = :type_id";
			$data = parent::read($sql, array(
				'type_id' => $params['type_id']
			));

		} elseif (isset($params['type'])) {
			$sql .= "WHERE dt.name = :type";
			$data = parent::read($sql, array(
				'type' => $params['type']
			));

		} else {
			throw new Exception('unknown date identifier - ' . print_r($params, true));
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
			UPDATE `date` d SET
				d.timestamp = :timestamp,
				d.type_id = (SELECT dt.id FROM date_type dt WHERE dt.name = :type)
			WHERE d.id = :id
		";

		parent::update($sql, array(
			'timestamp' => $this->attr['timestamp'],
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
			DELETE d, link FROM `date` d
			INNER JOIN `quest_date_linkage` link
				ON d.id = link.date_id
			WHERE d.id = :id';
		parent::destroy($sql, array('id' => $this->id));

	}


}
