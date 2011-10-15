<?php
/**
 * @file
 * Represent user role record in database
 */
class UserRoleDAO extends DAO implements DAOInterface{

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
			throw new Exception('incomplete user role params- ' . print_r($params, true));
			return ;

		}else{
			return $this->db->insert("
				INSERT INTO `user_role` (`name`) VALUE (:name)",
			array('name' => $params['name'])
			);

		}

	}

	/**
	 * Implement DAOInterface::read().
	 */
	public function read($params) {
		$sql = "SELECT * FROM `user_role` WHERE ";
		
		if (isset($params['id'])) {
			$params = array('id' => $params['id']);
			$sql .= "`id` = :id";

		} elseif (isset($params['name'])) {
			$params = array('name' => $params['name']);
			$sql .= "`name` = :name";

		} else {
			throw new Exception('unknown user role identifier');

		}

		$data = $this->db->fetch($sql, $params);
		return $this->updateAttribute($data);

	}

	/**
	 * Implement DAOInterface::update()
	 */
	public function update() {
		$sql = "
			UPDATE `user_role` SET
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
		$sql = "DELETE FROM `user_role` WHERE `id` = :id";
		$this->db->perform($sql, array('id' => $this->attr['id']));

	}
}
