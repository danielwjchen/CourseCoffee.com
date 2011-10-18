<?php
/**
 * @file
 * Represent a institution
 */
class InstitutionYearDAO extends DAO implements DAOInterface{

	/**
	 * Access institution-year linkage table
	 */
	private $linkage_dao;

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db) {
		parent::__construct($db);
		$this->linkage_dao = new InstitutionYearLinkageDAO($db);
	}

	/**
	 * Implement DAO::defineAttribute().
	 */
	protected function defineAttribute() {
		return array(
			'id',
			'institution_id',
			'period',
		);
	}

	/**
	 * Implement DAOInterface::create()
	 */
	public function create($params) {
		if (!isset($params['institution_id']) || !isset($params['period'])) {
			throw new Exception('incomplete institution year params');
			return ;

		}

		$year_id = $this->db->insert(
			"INSERT INTO `institution_year` (`period`) VALUE (:period)",
			array('period' => $params['period'])
		);

		$this->linkage_dao->create(array(
			'institution_id' => $params['institution_id'],
			'year_id'        => $year_id,
		));

		return $year_id;
	}

	/**
	 * Implement DAOInterface::read()
	 *
	 * This differs from other DAOs as it fetches all the records
	 */
	public function read($params) {
		$sql ="
			SELECT 
				iy.*,
				iy_linkage.institution_id
			FROM `institution_year` iy
			INNER JOIN `institution_year_linkage` iy_linkage
				ON iy.id = iy_linkage.year_id
		";
		$data = array();

		if (isset($params['id']) && !empty($params['id'])) {
			$sql .= 'WHERE iy.id = :id';
			$data = $this->db->fetch($sql, array('id' => $params['id']));
			
			return $this->updateAttribute($data);

		} elseif (isset($params['institution_id']) && isset($params['period'])) {

			$sql .= '
				WHERE iy_linkage.institution_id = :institution_id
					AND iy.period = :period
			';
			$data = $this->db->fetch($sql, array(
				'institution_id' => $params['institution_id'],
				'period'         => $params['period'],
			));
			
			// debug
			// error_log('institution_year sql - ' . $sql);
			// error_log('institution_year params - ' . print_r($params, true));
			// error_log('institution_year data - ' . print_r($data, true));
			
			return $this->updateAttribute($data);

		// not looking for specific record
		} else {
			if (isset($params['period'])) {
				$sql .= "WHERE period = :period";
				$this->list = $this->db->fetch($sql, array(
					'period' => $params['period']
				));
			} else {
				throw new Exception('incomplete institution_year parmas - ' . print_r($params, true));
			}

			return !empty($this->list);

		}

	}

	/**
	 * Implement DAOInterface::update()
	 */
	public function update() {
		$sql = "
			UPDATE `institution_year` SET `period` = :period
			WHERE id = :id
		";

		$this->db->perform($sql, array(
			'period' => $this->attr['period'],
			'id' => $this->attr['id']
		));

	}

	/**
	 * Implement DAOInterface::destroy()
	 */
	public function destroy() {
		$sql = 'DELETE FROM `institution_year` WHERE id = :id';
		$this->db->perform($sql, array('id' => $this->attr['id']));

	}


}
