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
		$sql_params = array();

		if (isset($params['name'])) {
			$sql .= 'WHERE `name` LIKE :name';
			$sql_params = array('name' => '%' . $params['name'] . '%');

		} elseif (isset($params['domain'])) {
			$sql .= "WHERE `domain` = :domain";
			$sql_params = array('domain' => '%' . $params['domain'] . '%');

		} elseif (isset($params['uri'])) {
			$sql .= "WHERE `uri` = :uri";
			$sql_params = array('uri' => '%' . $params['uri'] . '%');

		}

		$sql .= " ORDER BY name";
		$this->list = $this->db->fetchList($sql, $sql_params);

		return empty($this->list);

	}

}
