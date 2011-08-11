<?php
/**
 * @file
 * Represent user record in database
 */
class UserDAO extends DAO implements DAOInterface{

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('account', 'password', 'id');
		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create().
	 */
	public function create($params) {
		if (!isset($params['account']) || !isset($params['password'])) {
			throw new Exception('incomplete user pramas - ' . print_r($params, true));
			return ;

		}else{
			return $this->db->insert("
				INSERT INTO `user` (`account`, `password`)
				VALUES (:account, :password)",
			array(
				'account' => $params['account'], 
				'password' => $params['password']
			));

		}

	}

	/**
	 * Extend DAO::read().
	 */
	public function read($params) {
		$sql = "SELECT * FROM `user` WHERE ";
		
		if (isset($params['id'])) {
			$params = array('id' => $params['id']);
			$sql .= "`id` = :id";

		} elseif (isset($params['account']) && isset($params['password'])) {
			$params = array(
				'account' => $params['account'],
				'password' => $params['password'],
			);
			$sql .= "`account` = :account AND `password` = :password";

		} elseif (isset($params['account'])) {
			$params = array('account' => $params['account']);
			$sql .= "`account` = :account";

		} else {
			throw new Exception('unknown user identifier');

		}

		$data = $this->db->fetch($sql, $params);
		return $this->updateAttribute($data);

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `user` SET
				`account` = :account,
				`password` = :password
			WHERE `id` = :id
		";
		$this->db->perform($sql, array(
			'id'       => $this->attr['id'],
			'account'  => $this->attr['account'], 
			'password' => $this->attr['password'],
		));

	}

	/**
	 * Extend DAO::destroy().
	 */
	public function destroy() {
		$sql = "DELETE FROM `user` WHERE `id` = :id";
		$this->db->perform($sql, array('id' => $this->attr['id']));

	}
}
