<?php
/**
 * @file
 * Provide access to a list of institutions
 */
class InstitutionListDAO extends ListDAO implements ListDAOInterface {

	/**
	 * Implement ListDAOInterface::read()
	 */
	public function read($params) {
		$this->list = $this->db->fetchList("SELECT * FROM `institution`");
		return empty($this->list);
	}
}
