<?php
/**
 * @file
 * Represent user setting setting record in database
 */
class UserSettingDAO extends DAO implements DAOInterface{

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'user_id',
			'institution_id',
			'year_id',
			'term_id',
		);
		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create().
	 */
	public function create($params) {
		if (
			!isset($params['user_id']) || 
			!isset($params['institution_id']) ||
			!isset($params['year_id']) ||
			!isset($params['term_id'])
		) {
			throw new Exception('incomplete user setting pramas - ' . print_r($params, true));
			return ;

		}else{
			return $this->db->insert("
				INSERT INTO `user_setting` (
					`user_id`, `institution_id`, `year_id`, `term_id`
				) VALUES (:user_id, :institution_id, :year_id, :term_id)",
			array(
				'user_id'        => $params['user_id'], 
				'institution_id' => $params['institution_id'],
				'year_id'        => $params['year_id'],
				'term_id'        => $params['term_id'],
			));

		}

	}

	/**
	 * Extend DAO::read().
	 */
	public function read($params) {
		$sql = "SELECT * FROM `user_setting` us ";
		$sql_param = array();
		
		if (isset($params['id'])) {
			$sql      .= "WHERE us.`id` = :id";
			$sql_param = array('id' => $params['id']);

		} elseif (isset($params['user_id'])) {
			$sql      .= "WHERE us.`user_id` = :user_id";
			$sql_param = array('user_id' => $params['user_id']);

		} else {
			throw new Exception('unknown user setting identifier');

		}

		$data = $this->db->fetch($sql, $sql_param);
		
		// debug
		// error_log(__METHOD__ . ' - data - ' . print_r($data, true));

		return $this->updateAttribute($data);

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `user_setting` SET
				`institution_id` = :institution_id,
				`year_id` = :year_id,
				`term_id` = :term_id
			WHERE `id` = :id
		";
		$this->db->perform($sql, array(
			'id'             => $this->attr['id'],
			'user_id'        => $this->attr['user_id'], 
			'institution_id' => $this->attr['institution_id'],
			'term_id'        => $this->attr['term_id'],
			'year_id'        => $this->attr['year_id'],
		));

	}

	/**
	 * Extend DAO::destroy().
	 */
	public function destroy() {
		$sql = "DELETE FROM `user_setting` WHERE `id` = :id";
		$this->db->perform($sql, array('id' => $this->attr['id']));

	}
}
