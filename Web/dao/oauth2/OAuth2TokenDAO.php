<?php

/**
 * Manage OAuth2 token in the database
 */
class OAuth2TokenDAO extends DAO {

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('id', 'client_id', 'expires', 'scope');
		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create().
	 */
	public function create($params) {
		if (
				!isset($params['id']) || 
				!isset($params['client_id']) ||
				!isset($params['expires']) 
		) {
			throw new Exception('incomplete OAuth token params - ' . print_r($params, true));
			return ;

		}else{
			parent::create("
				INSERT INTO token (id, client_id, expires, scope)
				VALUES (:id, :client_id, :expires, :scope)",
			array(
				'id' => $params['id'], 
				'client_id' => $params['client_id'],
				'expires' => $params['expires'],
				'scope' => $params['scope'],
			));

		}

	}

	/**
	 * Extend DAO::read().
	 */
	public function read($params) {
		$sql = "SELECT * FROM `token` WHERE ";
		
		if (isset($params['id'])) {
			$params = array('id' => $params['id']);
			$sql .= "`id` = :id";

		} elseif (isset($params['client_id'])) {
			$params = array(
				'client_id' => $params['client_id'],
			);
			$sql .= "client_id = :client_id";

		} else {
			throw new Exception('unknown OAuth2 token identifier');

		}

		$data = parent::read($sql, $params);
		return $this->updateAttrribute($data);

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE token SET
				id = :id,
				client_id = :client_id,
				expires = :expires,
				scope = :scope
			WHERE id = :id
		";
		parent::update($sql);
		$this->read($this->attr);

	}

	/**
	 * Extend DAO::destroy().
	 */
	public function destroy() {
		$sql = "DELETE FROM token WHERE id = :id";
		parent::destroy($sql, array('id' => $this->id));

	}

}
