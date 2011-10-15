<?php
/**
 * @file
 * Represent a institution term
 */
class InstitutionTermDAO extends DAO implements DAOInterface{

	/**
	 * Implement DAO::defineAttribute().
	 */
	protected function defineAttribute() {
		return array(
			'id',
			`institution_id`,
			'year_id',
			'name',
		);
	}

	/**
	 * Implement DAOInterface::create()
	 */
	public function create($params) {
		if (
				!isset($params['institution_id']) || 
				!isset($params['year_id']) ||
				!isset($params['name'])
		) {
			throw new Exception('incomplete institution term params');
			return ;

		}

		return $this->db->insert(
			"INSERT INTO `institution_term` (
				`institution_id`
				`year_id`,
				`name`
			) VALUE (
				:institution_id,
				:year_id,
				:name
			)",
			array(
				'institution_id' => $params['institution_id'],
				'year_id'        => $params['year_id'],
				'name'           => $parmas['name'],
			)
		);

	}

	/**
	 * Implement DAOInterface::read()
	 *
	 */
	public function read($params) {
		$sql  = "SELECT * FROM `institution_term`";

		if (isset($params['id']) && !empty($params['id'])) {
			$sql .= 'WHERE id = :id';
			$data = $this->db->fetch($sql, array('id' => $params['id']));

			return $this->updateAttribute($data);

		} elseif (
			isset($params['institution_id']) && 
			isset($params['year_id']) &&
			isset($params['name'])
		) {
			$sql .= '
				WHERE institution_id = :institution_id
					AND year_id = :year_id
					AND name = :name
			';
			$data = $this->db->fetch($sql, array(
				'institution_id' => $params['institution_id'],
				'year_id'        => $params['year_id'],
				'name'           => $params['name'],
			));

			return $this->updateAttribute($data);

		// not looking for specific record
		} else {
			if (isset($params['institution_id']) && isset($params['year_id'])) {
				$sql .= '
					WHERE institution_id = :institution_id
						AND year_id = :year_id
				';
				$this->list = $this->db->fetch($sql, array(
					'institution_id' => $params['institution_id'],
					'year_id'           => $params['year_id'],
				));

			} elseif (isset($params['year_id'])) {
				$sql .= "WHERE year_id = :year_id";
				$this->list = $this->db->fetch($sql, array(
					'year_id' => $params['year_id']
				));
			} else {
				throw new Exception('incomplete institution_term params - ' . print_r($params));
			}

			return !empty($this->list);
		}

	}

	/**
	 * Implement DAOInterface::update()
	 */
	public function update() {
		$sql = "
			UPDATE `institution_term` SET 
				`institution_id` = :institution_id,
				`year_id` = :year_id
				`name` = :name
			WHERE id = :id
		";

		$this->db->perform($sql, array(
			'institution_id' => $this->attr['institution_id'],
			'year_id'        => $this->attr['year_id'],
			'id'             => $this->attr['id']
		));

	}

	/**
	 * Implement DAOInterface::destroy()
	 */
	public function destroy() {
		$sql = 'DELETE FROM `institution_term` WHERE id = :id';
		$this->db->perform($sql, array('id' => $this->attr['id']));

	}


}
