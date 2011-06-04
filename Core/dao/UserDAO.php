<?php

/**
 * Represents a user object in database
 */
class UserDAO extends DAO{

	/**
	 * Implement DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->setAttribute(array('account', 'password', 'id'));
		parent::__construct($db, $params);

	}

	/**
	 * Implement DAO::create().
	 */
	public function create($params) {
		$sql = "
			INSERT INTO `user` (`account`, `password`)
			VALUES (:account, :password)
		";

		parent::create($sql, $params);
		$this->read($params);

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
