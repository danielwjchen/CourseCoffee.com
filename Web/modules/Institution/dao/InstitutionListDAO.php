<?php
/**
 * Represent a institution
 */
class InstitutionListDAO extends ListDAO implements ListDAOInterface {

	/**
	 * Implement ListDAOInterface::read()
	 */
	public function read(array $params = null) {
		$sql = "SELECT * FROM `institution`";

		if (isset($params['name'])) {
			$sql .= 'WHERE `name` LIKE :name';
			$this->list = $this->db->fetchList($sql, array(
				'name' => '%' . $params['name'] . '%'
			));

		} elseif (isset($params['domain'])) {
			$sql .= "WHERE `domain` = :domain";
			$this->list = $this->db->fetchList($sql, array(
				'domain' => '%' . $params['domain'] . '%'
			));

		} elseif (isset($params['uri'])) {
			$sql .= "WHERE `uri` = :uri";
			$this->list = $this->db->fetchList($sql, array(
				'uri' => '%' . $params['uri'] . '%'
			));

		} else {
			$this->list = $this->db->fetchList($sql);
		}

		return empty($this->list);

	}

}
