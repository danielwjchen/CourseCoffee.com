<?php

/**
 * Represents a user object in database
 */
class UserDAO extends DAO{

	/**
	 * Implement DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('account', 'password', 'id');
		parent::__construct($db, $attr, $params);

	}

	/**
	 * Implement DAO::create().
	 */
	public function create($params) {
		if (!isset($params['account']) || !isset($params['password'])) {
			throw new Exception('incomplete user pramas - ' . print_r($params, true));
			return ;

		}else{
			$this->attr = array(
				'account' => $params['account'], 
				'password' => $params['password']
			);

			parent::create("
				INSERT INTO `user` (`account`, `password`)
				VALUES (:account, :password)",
			array(
				'account' => $params['account'], 
				'password' => $params['password']
			));

		}

	}

	/**
	 * Implement DAO::read().
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

		$data = parent::read($sql, $params);
		$this->attr = empty($data) ? $this->attr : $data;

	}

	/**
	 * Implement DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `user` SET
				`account` = :account,
				`password` = :password
			WHERE `id` = :id
		";
		parent::update($sql);
		$this->read($this->attr);

	}

	/**
	 * Implement DAO::destroy().
	 */
	public function destroy() {
		$sql = "DELETE FROM `user` WHERE `id` = :id";
		parent::destroy($sql, array('id' => $this->id));

	}
}
