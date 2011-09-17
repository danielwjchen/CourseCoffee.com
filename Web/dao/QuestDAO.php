<?php
/**
 * @file
 * Represent a quest
 */
class QuestDAO extends DAO implements DAOInterface{

	/**
	 * Implement DAO::defineAttribute().
	 */
	protected function defineAttribute() {
		return array(
			'id',
			'status',
			'status_id',
			'type',
			'type_id',
			'user_id',
			'objective',
			'description',
		);
	}

	/**
	 * Implement DAOInterface::create()
	 */
	public function create($params) {
		if (!isset($params['objective']) || 
				!isset($params['user_id']) ||
        !isset($params['type']) ||
				!isset($params['description'])) 
		{
			throw new Exception('incomplete quest params - ' . print_r($params, true));
			return false;

		} else {
			return $this->db->insert("
				INSERT INTO `quest`
					(`objective`, `description`, `user_id`, `type_id`, `status_id`, `created`, `updated`)
				VALUES (
					:objective,
					:description,
					:user_id,
					(SELECT `id` FROM `quest_type` WHERE name = :type),
					(SELECT `id` FROM `quest_status` WHERE name = :status),
					UNIX_TIMESTAMP(),
					UNIX_TIMESTAMP()
				)",
				array(
					'type' => $params['type'],
					'status' => $params['status'],
					'user_id' => $params['user_id'],
					'objective' => $params['objective'],
					'description' => $params['description'],
				)
			);
			
		}
		
	}

	/**
	 * Implement DAOInterface::read()
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

		if (isset($params['id']) && !empty($params['id'])) {
			$sql .= 'WHERE q.id = :id';
			$data = $this->db->fetch($sql, array('id' => $params['id']));

		} elseif (isset($params['user_id'])) {
			$sql .= 'WHERE q.user_id = :user_id';
			$data = $this->db->fetch($sql, array('user_id' => $params['user_id']));

		} elseif (isset($params['objective']) && isset($params['type'])) {
			$sql .= 'WHERE q.objective  = :objective AND qt.name = :type';
			$data = $this->db->fetch($sql, array(
				'objective' => $params['objective'],
				'type' => $params['type']
			));

		} elseif (isset($params['objective'])) {
			$sql .= 'WHERE q.objective = :objective';
			$data = $this->db->fetch($sql, array('objective' => $params['objective']));

		} elseif (isset($params['type_id'])) {
			$sql .= "WHERE qt.id = :type_id";
			$data = $this->db->fetch($sql, array(
				'type_id' => $params['type_id']
			));

		} elseif (isset($params['type'])) {
			$sql .= "WHERE qt.name = :type";
			$data = $this->db->fetch($sql, array(
				'type' => $params['type']
			));

		} else {
			throw new Exception('unknown quest identifier - ' . print_r($params, true));
			return ;

		}
		
		return $this->updateAttribute($data);

	}

	/**
	 * Implement DAOInterface::update()
	 */
	public function update() {
		$sql = "
			UPDATE `quest` q SET
				q.user_id = :user_id,
				q.description = :description,
				q.objective = :objective,
				q.type_id = (SELECT qt.id FROM quest_type qt WHERE qt.name = :type),
				q.updated = UNIX_TIMESTAMP()
			WHERE q.id = :id
		";

		$this->db->perform($sql, array(
			'description' => $this->attr['description'],
			'objective' => $this->attr['objective'],
			'type' => $this->attr['type'],
			'user_id' => $this->attr['user_id'],
			'id' => $this->attr['id']
		));

	}

	/**
	 * Implement DAOInterface::destroy()
	 */
	public function destroy() {
		$sql = '
      DELETE 
				q, qa, qi_linkage, qa_linkage, ql_linkage, qd_linkage, qq_linkage
			FROM `quest` q
			LEFT JOIN `quest_attribute` qa
				ON q.id = qa.quest_id
			LEFT JOIN `quest_item_linkage` qi_linkage
				ON q.id = qi_linkage.quest_id
			LEFT JOIN `quest_affiliation_linkage` qa_linkage
				ON q.id = qa_linkage.quest_id
			LEFT JOIN `quest_location_linkage` ql_linkage
				ON q.id = ql_linkage.quest_id
			LEFT JOIN `quest_date_linkage` qd_linkage
				ON q.id = qd_linkage.quest_id
			LEFT JOIN `quest_linkage` qq_linkage
				ON q.id = qq_linkage.child_id
			WHERE q.id = :id';
		$this->db->perform($sql, array('id' => $this->attr['id']));

	}


}
