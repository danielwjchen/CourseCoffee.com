<?php
/**
 * @file
 * Represents a file record in database
 */
class FileDAO extends DAO implements DAOInterface{

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'user_id',
			'name',
			'path',
			'mime',
			'size',
			'timestamp',
		);
		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create().
	 */
	public function create($params) {
		if (!isset($params['user_id']) || 
				!isset($params['name']) ||
				!isset($params['path']) ||
				!isset($params['mime']) ||
				!isset($params['size']) ||
				!isset($params['timestamp'])
		) {
			throw new Exception('incomplete file pramas - ' . print_r($params, true));
			return ;

		}else{
			return $this->db->insert("
				INSERT INTO `file` (
					`user_id`,
					`name`,
					`path`,
					`mime`,
					`size`,
					`timestamp`
				) VALUES (
					:user_id,
					:name,
					:path,
					:mime,
					:size,
					:timestamp
				)
			",
			array(
					'user_id'   => $params['user_id'],
					'name'      => $params['name'],
					'path'      => $params['path'],
					'mime'      => $params['mime'],
					'size'      => $params['size'],
					'timestamp' => $params['timestamp'],
			));

		}

	}

	/**
	 * Extend DAO::read().
	 */
	public function read($params) {
		$sql = "SELECT * FROM `file` WHERE ";
		
		if (isset($params['id'])) {
			$params = array('id' => $params['id']);
			$sql .= "`id` = :id";

		} elseif (isset($params['user_id'])) {
			$params = array('user_id' => $params['user_id']);
			$sql .= "`user_id` = :user_id";

		} elseif (isset($params['user_id']) && isset($params['name'])) {
			$params = array(
				'user_id' => $params['user_id'],
				'name' => $params['name'],
			);
			$sql .= "`user_id` = :user_id AND `name` = :name";

		} else {
			throw new Exception('unknown file identifier');

		}

		$data = $this->db->fetch($sql, $params);
		return $this->updateAttrribute($data);

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `file` SET
				`user_id`   = :user_id,
				`name`      = :name,
				`path`      = :path,
				`mime`      = :mime,
				`size`      = :size,
				`timestamp` = :timestamp
			WHERE `id` = :id
		";
		$this->db->perform($sql, array(
			'user_id'   => $this->attr['user_id'],
			'name'      => $this->attr['name'],
			'path'      => $this->attr['path'],
			'mime'      => $this->attr['mime'],
			'size'      => $this->attr['size'],
			'timestamp' => $this->attr['timestamp'],
		));

	}

	/**
	 * Extend DAO::destroy().
	 */
	public function destroy() {
		$sql = "DELETE FROM `file` WHERE `id` = :id";
		$this->db->perform($sql, array('id' => $this->id));

	}
}
