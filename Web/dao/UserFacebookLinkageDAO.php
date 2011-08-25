<?php
/**
 * @file
 * Represent a linkage between a user and a facebook account
 */
class UserFacebookLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('user_id', 'fb_uid', 'id');
		$this->linkage = 'user_facebook_linkage';
		parent::__construct($db, $attr, $params);
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

		} elseif (isset($params[$this->column[0]]) && 
			isset($params[$this->column[1]])) 
		{
			$params = array(
				$this->column[0] => $params[$this->column[0]],
				$this->column[1] => $params[$this->column[1]],
			);

			$sql .= "{$this->column[0]} = :{$this->column[0]} 
				AND {$this->column[1]} = :{$this->column[1]}";

		// return list of record based on parent
		} elseif (isset($params[$this->column[0]])) {
			$params = array($this->column[0] => $params[$this->column[0]]);
			$sql .= "{$this->column[0]} = :{$this->column[0]}";

		// return list of record based on child
		} elseif (isset($params[$this->column[1]])) {
			$params = array($this->column[1] => $params[$this->column[1]]);
			$sql .= "{$this->column[1]} = :{$this->column[1]}";

		} else {
			Logger::Write("unknown {$this->linkage} identifier - " . print_r($params, true));
			return false;

		}

		$data = $this->db->fetch($sql, $params);

		return $this->updateAttribute($data);

	}

}
