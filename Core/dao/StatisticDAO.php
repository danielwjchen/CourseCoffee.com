<?php

/**
 * Represents a statistic object in database
 */
class StatisticDAO extends DAO{

	/**
	 * Implement DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->db = $db;

		if (!empty($params)) {
			parent::__construct($sql, $params);
		}
	}

	/**
	 * Implement DAO::create().
	 */
	public function create($params) {
		$sql = "
			INSERT INTO `statistic` (`account`, `password`)
			VALUES (:account, :password)
		";

		parent::create($sql, $params);
		$this->data = $this->read($params);
	}

	/**
	 * Implement DAO::read().
	 */
	public function read($params) {
		$sql = "SELECT * FROM `statistic` WHERE ";
		
		if (isset($params['id'])) {
			$sql .= "`id` = :id";

		} elseif (isset($params['account']) && isset($params['password'])) {
			$params = array(
				'account' => $params['account'],
				'password' => $params['password'],
			);
			$sql .= "`account` = :account AND `password` = :password";

		} else {
			throw new Exception('unknown statistic identifier');
		}

		$this->data = parent::read($sql, $params);
	}

	/**
	 * Implement DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `statistic` SET
				`account` = :account,
				`password` = :password
			WHERE `id` = :id
		";
		parent::update($sql);

		$this->data = $this->read();
	}

	/**
	 * Implement DAO::destroy().
	 */
	public function destroy() {
		$sql = "DELETE FROM `statistic` WHERE `id` = :id";

		parent::destroy($sql, array('id' => $this->id));
	}
}
