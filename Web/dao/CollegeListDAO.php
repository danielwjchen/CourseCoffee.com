<?php
/**
 * Represent a institution
 */
class CollegeListDAO extends ListDAO implements ListDAOInterface{

	/**
	 * Extend DAO::read()
	 *
	 * This differs from other DAOs as it fetches all the records
	 */
	public function read($params) {
		$sql ="SELECT * FROM `institution`";

		if (isset($params['id']) && !empty($params['id'])) {
			$sql .= 'WHERE id = :id';
			$data = $this->db->fetch($sql, array('id' => $params['id']));

		} elseif (isset($params['name'])) {
			$sql .= 'WHERE name = :name';
			$data = $this->db->fetch($sql, array('name' => $params['name']));

		} elseif (isset($params['domain'])) {
			$sql .= "WHERE domain = :domain";
			$data = $this->db->fetch($sql, array(
				'domain' => $params['domain']
			));

		} elseif (isset($params['uri'])) {
			$sql .= "WHERE uri = :uri";
			$data = $this->db->fetch($sql, array(
				'uri' => $params['uri']
			));

		} else {
			$this->list = $this->db->fetch($sql);
			return empty($this->list);
		}
		

	}



}
