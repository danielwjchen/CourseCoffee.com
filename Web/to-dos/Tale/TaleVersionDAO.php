<?php
/**
 * @file
 * Represent a tale_version
 */
class TaleVersionDAO extends DAO implements DAOInterface{

	/**
	 * Implement DAO::defineAttribute().
	 */
	protected function defineAttribute() {
		return array('id',	'story');
	}

	/**
	 * Implement DAOInterface::create()
	 */
	public function create($params) {
		if (!isset($params['story'])) {
			throw new Exception('incomplete tale_version params - ' . print_r($params, true));
			return false;

		} else {
			return $this->db->insert(
				"INSERT INTO `tale_version` (`story`) VALUE (:story)",
				array('story' => $params['story'])
			);
			
		}
		
	}

	/**
	 * Implement DAOInterface::read()
	 */
	public function read($params) {
		$sql ="SELECT * FROM `tale_version`";

		if (isset($params['id']) && !empty($params['id'])) {
			$sql .= 'WHERE id = :id';
			$data = $this->db->fetch($sql, array('id' => $params['id']));

		} else {
			throw new Exception('unknown tale_version identifier - ' . print_r($params, true));
			return ;

		}
		
		return $this->updateAttribute($data);

	}

	/**
	 * Implement DAOInterface::update()
	 */
	public function update() {

		$this->db->perform(
			"UPDATE `tale_version` SET	story = :story WHERE id = :id",
			array(
				'story' => $this->attr['story'],
				'id'    => $this->attr['id'],
			)
		);

	}

	/**
	 * Implement DAOInterface::destroy()
	 */
	public function destroy() {
		$sql = 'DELETE FROM tale_version WHERE id = :id';
		$this->db->perform($sql, array('id' => $this->attr['id']));

	}


}
