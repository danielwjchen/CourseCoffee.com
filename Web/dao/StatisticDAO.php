<?php
/**
 * @file
 * Represents a statistic object in database
 */
class StatisticDAO extends DAO implements DAOInterface{

	/**
	 * Implement DAO::defineAttribute().
	 */
	protected function defineAttribute() {
		return array(
			'id',
			'data',
			'type',
			'type_id',
		);
	}

	/**
	 * Implement DAOInterface::create().
	 */
	public function create($params) {
		if (!isset($params['data']) ||
				!isset($params['type']))
		{
			throw new Exception('incomplete statistic params - ' . print_r($params, true));
			return ;
		}

		$this->db->insert("
			INSERT INTO statistic
				(data, type_id)
			VALUES (
				:data,
				(SELECT id FROM statistic_type WHERE name = :type)
			)",
			array(
				'data' => $params['data'],
				'type' => $params['type']
			)
		);

	}

	/**
	 * Implement DAOInterface::read().
	 */
	public function read($params) {
		$sql = "
			SELECT 
				s.*,
				st.name AS type 
			FROM `statistic` s
			INNER JOIN `statistic_type` st
				ON s.type_id = st.id
		";
		
		if (isset($params['id'])) {
			$sql .= "WHERE s.id = :id";
			$data = $this->db->fetch($sql, array('id' => $params['id']));

		} elseif (isset($params['data']) && isset($params['type_id'])) {
			$sql .= "WHERE s.data = :data AND st.id = :type_id";
			$data = $this->db->fetch($sql, array(
				'data' => $params['data'],
				'type_id' => $params['type_id']
			));

		} elseif (isset($params['data']) && isset($params['type'])) {
			$sql .= "WHERE s.data = :data AND st.name = :type";
			$data = $this->db->fetch($sql, array(
				'data' => $params['data'],
				'type' => $params['type']
			));

		} else {
			throw new Exception('unknown statistic identifier');

		}

		return $this->updateAttribute($data);

	}

	/**
	 * Implement DAOInterface::update()
	 */
	public function update() {
		$sql = "
			UPDATE `statistic` s SET
				s.data = :data,
				s.type_id = (SELECT st.id FROM `statistic_type` st WHERE st.name = :type)
			WHERE s.id = :id
		";

		$this->db->perform($sql, array(
			'data' => $this->attr['data'],
			'type' => $this->attr['type'],
			'id' => $this->attr['id'],
		));

	}

	/**
	 * Implement DAOInterface::destroy().
	 */
	public function destroy() {
		$sql = "
			DELETE s, qs_linkage
				FROM `statistic` s
			LEFT JOIN `quest_statistic_linkage` qs_linkage
				ON s.id = qs_linkage.statistic_id
			WHERE s.`id` = :id
		";

		$this->db->perform($sql, array('id' => $this->id));
	}
}
