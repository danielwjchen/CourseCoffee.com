<?php
/**
 * @file
 * Represent a user cookie record in database
 */
class UserCookieDAO extends DAO implements DAOInterface{

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('user_id', 'signature');
		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create().
	 */
	public function create($params) {
		if (!isset($params['user_id']) || !isset($params['signature'])) {
			throw new Exception('incomplete user_cookie params - ' . print_r($params, true));
			return ;

		}else{
			return $this->db->insert("
				REPLACE INTO `user_cookie` (`user_id`, `signature`)
				VALUES (:user_id, :signature)",
			array(
				'user_id' => $params['user_id'], 
				'signature' => $params['signature']
			));

		}

	}

	/**
	 * Extend DAO::read().
	 */
	public function read($params) {
		$sql = "SELECT * FROM `user_cookie` WHERE ";
		
		if (isset($params['user_id'])) {
			$params = array('user_id' => $params['user_id']);
			$sql .= "`user_id` = :user_id";

		} elseif (isset($params['signature'])) {
			$params = array('signature' => $params['signature']);
			$sql .= "signature = :signature";

		} else {
			throw new Exception('unknown user_cookie identifier');

		}

		$data = $this->db->fetch($sql, $params);
		return $this->updateAttribute($data);

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `user_cookie` SET
				`user_id` = :user_id,
				`signature` = :signature
			WHERE `id` = :id
		";
		$this->db->perform($sql, array(
			'user_id' => $this->attr['user_id'], 
			'signature' => $this->attr['signature']
		));

	}

	/**
	 * Extend DAO::destroy().
	 */
	public function destroy() {
		$sql = "DELETE FROM `user_cookie` WHERE `user_id` = :user_id";
		$this->db->perform($sql, array('user_id' => $this->user_id));

	}
}
