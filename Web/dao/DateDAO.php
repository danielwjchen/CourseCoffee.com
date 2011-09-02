<?php
/**
 * @file
 * Represent a date
 */
class DateDAO extends DAO implements DAOInterface{

	/**
	 * Extend DAO::__construct().
	 */
	function __construct() {
		parent::__construct();
		$attr = array(
			'id',
			'timestamp',
			'type',
			'type_id',
		);
		$this->setAttribute($attr);

	}

	/**
	 * Extend DAO::create()
	 */
	public function create($params) {
		if (!isset($params['timestamp']) || 
				empty($params['timestamp']) ||
				!isset($params['type']) ||
				empty($params['type'])) 
		{
			throw new Exception('incomplete date params');
			return ;

		} else {
			return $this->db->insert("
				INSERT INTO `date` 
					(timestamp, type_id)
				VALUES (
					:timestamp,
					(SELECT `id` FROM `date_type` WHERE name = :type)
				)",
				array(
					'timestamp' => $params['timestamp'],
					'type' => $params['type']
				)
			);

		}
		
	}

	/**
	 * Extend DAO::read()
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
			$data = $this->db->fetch($sql, array('id' => $params['id']));

		} elseif (isset($params['timestamp']) && isset($params['type_id'])) {
			$sql .= 'WHERE d.timestamp = :timestamp AND d.type_id = :type_id';
			$data = $this->db->fetch($sql, array(
				'timestamp' => $params['timestamp'],
				'type_id' => $params['type_id']
			));

		} elseif (isset($params['timestamp']) && isset($params['type'])) {
			$sql .= 'WHERE d.timestamp = :timestamp AND dt.name = :type';
			$data = $this->db->fetch($sql, array(
				'timestamp' => $params['timestamp'],
				'type' => $params['type']
			));

		} elseif (isset($params['timestamp'])) {
			$sql .= 'WHERE d.timestamp = :timestamp';
			$data = $this->db->fetch($sql, array('timestamp' => $params['timestamp']));

		} elseif (isset($params['type_id'])) {
			$sql .= "WHERE dt.id = :type_id";
			$data = $this->db->fetch($sql, array(
				'type_id' => $params['type_id']
			));

		} elseif (isset($params['type'])) {
			$sql .= "WHERE dt.name = :type";
			$data = $this->db->fetch($sql, array(
				'type' => $params['type']
			));

		} else {
			throw new Exception('unknown date identifier - ' . print_r($params, true));
			return ;

		}

		return $this->updateAttribute($data);

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `date` d SET
				d.timestamp = :timestamp,
				d.type_id = (SELECT dt.id FROM date_type dt WHERE dt.name = :type)
			WHERE d.id = :id
		";

		$this->db->perform($sql, array(
			'timestamp' => $this->attr['timestamp'],
			'type' => $this->attr['type'],
			'id' => $this->attr['id']
		));

	}

	/**
	 * Extend DAO::destroy()
	 */
	public function destroy() {
		$sql = '
			DELETE d, link FROM `date` d
			LEFT JOIN `quest_date_linkage` link
				ON d.id = link.date_id
			WHERE d.id = :id';
		$this->db->perform($sql, array('id' => $this->id));

	}


}
