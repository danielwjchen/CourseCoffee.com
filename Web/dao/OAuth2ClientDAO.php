<?php
/**
 * @file
 * Manage OAuth2 code in the database
 */
class OAuth2ClientDAO extends DAO {

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('id', 'secret', 'redirect_uri');
		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create().
	 */
	public function create($params) {
		if (
				!isset($params['id']) || 
				!isset($params['secret']) ||
				!isset($params['redirect_uri'])
		) {
			throw new Exception('incomplete OAuth client params - ' . print_r($params, true));
			return ;

		}else{
			parent::create("
				INSERT INTO client (id, secret, redirect_uri)
				VALUES (:id, :secret, :redirect_uri)",
			array(
				'id' => $params['id'], 
				'secret' => $params['secret'],
				'redirect_uri' => $params['redirect_uri'],
			));

		}

	}

	/**
	 * Extend DAO::read().
	 */
	public function read($params) {
		$sql = "SELECT * FROM client WHERE ";
		
		if (isset($params['id'])) {
			$params = array('id' => $params['id']);
			$sql .= "id = :id";

		} elseif (isset($params['secret'])) {
			$params = array(
				'secret' => $params['secret'],
			);
			$sql .= "secret = :secret";

		} else {
			throw new Exception('unknown OAuth2 client identifier');

		}

		$data = parent::read($sql, $params);
		return $this->updateAttribute($data);

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE client SET
				id = :id,
				secret = :secret,
				redirect_uri = :redirect_id,
			WHERE id = :id
		";
		parent::update($sql);
		$this->read($this->attr);

	}

	/**
	 * Extend DAO::destroy().
	 */
	public function destroy() {
		$sql = "DELETE FROM client WHERE id = :id";
		parent::destroy($sql, array('id' => $this->id));

	}
}
