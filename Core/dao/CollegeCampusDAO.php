<?php

require_once DAO_PATH . 'LocationDAO.php';

/**
 * Represent a college campus 
 */
class CollegeCampusDAO extends LocationDAO{

	/**
	 * Implement LocationDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		if (!empty($params)) {
			$college = $this->linkCollege($params);
			$this->attr['college'] = $college->name;
			$this->attr['college_id'] = $college->id;
			$params['type'] = 'college_campus';

		}

		parent::__construct($db, $params);
		$this->extendAttribute(array('college', 'college_id'));

	}
	
	/**
	 * Find associated college and create linkage
	 *
	 * @param array $params
	 *  an associative array conatin either id or name of the college
	 */
	private function linkCollege($params) {
		if (isset($params['college_id'])) {
			$college_params['id'] = $params['college_id'];
			
		} elseif (isset($params['college'])) {
			$college_params['name'] = $params['college'];

		} else {
			throw new Exception('incomplete college_campus params' . print_r($params, true));
			return ;

		}

		$college = Factory::DAO('college', $college_params);
		$college->read($college_params);
		$this->attr['college_id'] = $college->id;
		$this->attr['college'] = $college->name;
		return $college;
	}

	/**
	 * Implement LocationDAO::create()
	 */
	public function create($params) {
		$params['type'] = 'college_campus';
		parent::create($params);
		$college = $this->linkCollege($params);
		$linkage = Factory::DAO('affiliation_location_linkage');
    $linkage->create(array(
      'affiliation_id' => $college->id, 
      'location_id' => $this->id
    ));

	}

	/**
	 * Implement LocationDAO::read()
	 */
	public function read($params) {
		$params['type'] = 'college_campus';
		parent::read($params);
		$college = $this->linkCollege($params);

	}

	/**
	 * Implement LocationDAO::update()
	 */
	public function update() {
		parent::update();

	}

	/**
	 * Implement LocationDAO::destroy()
	 */
	public function destroy() {
		parent::destroy();

	}

}
