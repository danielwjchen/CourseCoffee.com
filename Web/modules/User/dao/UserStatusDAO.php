<?php
/**
 * @file
 * Represent user status record in database
 */
class UserStatusDAO extends DAO implements DAOInterface{

	/**
	 * Implement DAO::defineAttribute().
	 */
	protected function defineAttribute() {
		return array('name', 'id');
	}

	/**
	 * Implement DAOInterface::create().
	 */
	public function create($params) {
		if (!isset($params['name'])) {
			throw new Exception('incomplete user status params- ' . print_r($params, true));
			return ;

		}else{
			return $this->db->insert("
				INSERT INTO `user_status` (`name`) VALUE (:name)",
			array('name' => $params['name'])
			);

		}

	}

	/**
	 * Implement DAOInterface::read().
	 */
	public function read($params) {
		$sql = "SELECT * FROM `user_status` WHERE ";
		
		if (isset($params['id'])) {
			$params = array('id' => $params['id']);
			$sql .= "`id` = :id";

		} elseif (isset($params['name'])) {
			$params = array('name' => $params['name']);
			$sql .= "`name` = :name";

		} else {
			throw new Exception('unknown user status identifier');

		}

		$data = $this->db->fetch($sql, $params);
		return $this->updateAttribute($data);

	}

	/**
	 * Implement DAOInterface::update()
	 */
	public function update() {
		$sql = "
			UPDATE `user_status` SET
				`name` = :name
			WHERE `id` = :id
		";
		$this->db->perform($sql, array(
			'id'       => $this->attr['id'],
			'name'  => $this->attr['name'], 
		));

	}

	/**
	 * Implement DAOInterface::destroy().
	 */
	public function destroy() {
		$sql = "DELETE FROM `user_status` WHERE `id` = :id";
		$this->db->perform($sql, array('id' => $this->attr['id']));

	}
}
