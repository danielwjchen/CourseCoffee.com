<?php

require_once DAO_PATH . 'core/LocationDAO.php';

/**
 * Represent a college building
 *
 * Attributes:
 * - college: name of the college
 * - college_id: id of the college
 * - id: id of the location
 * - name: name of the location
 * - longitude
 * - latitude
 */
class CollegeBuildingDAO extends LocationDAO{

	private $college;
	private $linkage;

	/**
	 * Implement LocationDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->college = Factory::DAO('College');
		$this->linkage = Factory::DAO('AffiliationLocationLinkage');
		if (!empty($params)) {
			$this->findCollege($params);
			$this->attr['college'] = $this->college->name;
			$this->attr['college_id'] = $this->college->id;
			$params['type'] = 'college_building';

		}

		parent::__construct($db, $params);
		$this->extendAttribute(array('college', 'college_id'));

	}
	
	/**
	 * Find associated college and create linkage
	 *
	 * @param array $params
	 *  an associative array conatin either id or name of the college
	 *  - college: name of the college
	 *  - college_id: id of the college
	 */
	private function findCollege($params) {
		$college_params = array();
		if (isset($params['college_id'])) {
			$college_params['id'] = $params['college_id'];
			
		} elseif (isset($params['college'])) {
			$college_params['name'] = $params['college'];

		} else {
			throw new Exception('incomplete college_building params' . print_r($params, true));
			return ;

		}

		$result = $this->college->read($college_params);
		$this->attr['college_id'] = $this->college->id;
		$this->attr['college'] = $this->college->name;
		return $result;

	}

	/**
	 * Extend LocationDAO::create()
	 *
	 * @param array $params
	 *  - college: name of the college
	 *  - college_id: id of the college
	 *  - name: name of the location
	 *  - longitude
	 *  - latitude
	 */
	public function create($params) {
		$params['type'] = 'college_building';
		$this->db->insert($params);
		$this->findCollege($params);
    $this->linkage->create(array(
      'affiliation_id' => $this->college->id, 
      'location_id' => $this->id
    ));

		return $this->id;

	}

	/**
	 * Extend LocationDAO::read()
	 *
	 * @param array $params
	 *  - college: name of the college
	 *  - college_id: id of the college
	 *  - name: name of the location
	 *  - longitude
	 *  - latitude
	 */
	public function read($params) {
		$result = $this->findCollege($params);
		return ($this->db->fetch($params) && $result);

	}

}
