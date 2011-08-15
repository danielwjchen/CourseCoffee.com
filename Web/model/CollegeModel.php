<?php
/**
 * @file
 * Suggest a list of colleges
 *
 * @to-do
 * In the future, we might do ip/geo based suggest system
 */
class CollegeModel extends Model {

	/**
	 * Access to college record
	 */
	private $institution_dao;

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->institution_dao = new InstitutionDAO($this->db);
	}

	/**
	 * Get a list of college 
	 *
	 * @to-do:
	 *  maybe an IP address could be passed as param to determine the default 
	 *  option
	 *
	 * @return array
	 *  On success:
	 *   - success:
	 *   - content:
	 *  On failure:
	 *   - error:
	 *   - meessage:
	 */
	public function getCollegeOption() {
		$this->institution_dao->read(array('all' => true));
		$records = $this->institution_dao->list;
		$list = array();

		// if there is only one school
		if (isset($records['id'])) {
			return array(
				$records['id'] => $records['name']
			);
		}

		foreach ($list as $key => $value) {
			$list[$value['id']] = $value['name'];
		}

		return $list;
	}
}
