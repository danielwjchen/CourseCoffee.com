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
	private $college_list;

	/**
	 * Extend Model::__construct()
	 */
	function __construct($sub_domain) {
		parent::__construct($sub_domain);
		$this->college_list = new CollegeListDAO($this->default_db);
	}

	/**
	 * Get college by id
	 */
	public function getCollege($institution_id) {
		$this->college_list->read(array('id' => $institution_id));

		return $this->college_list->attribute;
	}

	/**
	 * Get college by sub domain
	 *
	 * @param string $domain
	 */
	public function getCollegeByDomain($domain) {
		$institution = new InstitutionDAO($this->default_db);
		$institution->read(array('domain' => $domain));
		return $institution->attribute;
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
		$this->college_list->read(array('all' => true));
		$records = $this->college_list->list;
		$list = array();

		// if there is only one school
		if (isset($records['id'])) {
			return array(
				$records['id'] => $records['name']
			);
		}

		foreach ($records as $key => $value) {
			$list[$value['id']] = $value['name'];
		}

		return $list;
	}
}
