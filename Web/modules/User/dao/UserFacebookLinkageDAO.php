<?php
/**
 * @file
 * Represent a linkage between a user and a facebook account
 */
class UserFacebookLinkageDAO extends LinkageDAO {

	/**
	 * Implement LinkageDAO::defineColumn().
	 */
	protected function defineColumn() {
		return array(
			'parent_id' => 'user_id', 
			'child_id'  => 'fb_uid',
		);
	}

	/**
	 * Implement LinkageDAO::defineLinkageTable().
	 */
	protected function defineLinkageTable() {
		return 'user_facebook_linkage';
	}

	/**
	 * Override LinkageDAO::read()
	 *
	 * Because this is a one to one mapping, we always return a sinlge record
	 */
	public function read($params) {
		$sql = "SELECT * FROM `{$this->linkage}` WHERE ";

		if (isset($params['id'])) {
			$params = array('id' => $params['id']);
			$sql .= "id = :id";

		} elseif (isset($params[$this->column['parent_id']]) && 
			isset($params[$this->column['child_id']])) 
		{
			$params = array(
				$this->column['parent_id'] => $params[$this->column['parent_id']],
				$this->column['child_id'] => $params[$this->column['child_id']],
			);

			$sql .= "{$this->column['parent_id']} = :{$this->column['parent_id']} 
				AND {$this->column['child_id']} = :{$this->column['child_id']}";

		// return list of record based on parent
		} elseif (isset($params[$this->column['parent_id']])) {
			$params = array($this->column['parent_id'] => $params[$this->column['parent_id']]);
			$sql .= "{$this->column['parent_id']} = :{$this->column['parent_id']}";

		// return list of record based on child
		} elseif (isset($params[$this->column['child_id']])) {
			$params = array($this->column['child_id'] => $params[$this->column['child_id']]);
			$sql .= "{$this->column['child_id']} = :{$this->column['child_id']}";

		} else {
			Logger::Write("unknown {$this->linkage} identifier - " . print_r($params, true));
			return false;

		}

		$data = $this->db->fetch($sql, $params);

		return $this->updateAttribute($data);

	}

}
