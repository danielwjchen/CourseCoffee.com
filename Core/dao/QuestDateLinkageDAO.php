<?php

require_once __DIR__ . '/LinkageDAO.php';

/**
 * Represent a linkage between a quest and a date
 */
class QuestDateLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('quest_id', 'date_id', 'id');
		$this->linkage = 'quest_date_linkage';
		parent::__construct($db, $attr, $params);
	}

	/**
	 * Override LinkageDAO::read().
	 */
	public function read($params) {
		$sql = "SELECT link.* FROM `quest_date_linkage` link";

		if (isset($params['id'])) {
			$params = array('id' => $params['id']);
			$sql .= 'WHERE link.id = :id';

		} elseif (
				isset($params['quest_id']) && 
				isset($params['type']) && 
				isset($params['begin_timestamp']) &&
				isset($params['end_timestamp'])
			)
		{
			$sql .= "
				INNER JOIN `date` d
					ON link.date_id = d.id
				INNER JOIN `date_type` dt
					ON	d.type_id = dt.id
				WHERE link.quest_id = :quest_id
					AND d.timestamp >= :begin_timestamp
					AND d.timestamp <= :end_timestamp
					AND dt.name = :type
			";

			$params = array(
				'quest_id' => $params['quest_id'],
				'type' => $params['type'],
				'begin_timestamp' => $params['begin_timestamp'],
				'end_timestamp' => $params['end_timestamp'],
			);
				
		} elseif (
				isset($params['date_id']) && 
				isset($params['type']) && 
				isset($params['begin_timestamp']) &&
				isset($params['end_timestamp'])
			)
		{
			$sql .= "
				INNER JOIN `date` d
					ON link.date_id = d.id
				INNER JOIN `date_type` dt
					ON	d.type_id = dt.id
				WHERE link.date_id = :date_id
					AND d.timestamp >= :begin_timestamp
					AND d.timestamp <= :end_timestamp
					AND dt.name = :type
			";

			$params = array(
				'date_id' => $params['date_id'],
				'type' => $params['type'],
				'begin_timestamp' => $params['begin_timestamp'],
				'end_timestamp' => $params['end_timestamp'],
			);

		} else {
			throw new Exception("unknown quest_date_linkage identifier - " . print_r($params, true));

		}

		$data = $this->db->fetch($sql, $this->rewriteParams($params));
		$this->attr = empty($data) ? $this->attr : $data;

	}

}
