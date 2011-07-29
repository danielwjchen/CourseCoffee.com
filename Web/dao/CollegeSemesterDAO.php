<?php
/**
 * @file
 * Represents a college semester in database
 *
 * This is actually a composite of several DAOs
 */
class CollegeSemesterDAO extends DAO implements DAOInterface{

	protected $college;
	protected $quest;
	protected $quest_affiliation_linkage;
	protected $quest_date_range;

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'college',
			'college_id',
			'name',
			'description',
			'begin_date',
			'end_date',
		);

		$this->college  = new CollegeDAO($db);
		$this->quest = new QuestDAO($db);
		$this->quest_affiliation_linkage = new QuestAffiliationLinkageDAO($db);
		$this->quest_date_range = new QuestDateRangeDAO($db);

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Check if the provided parameters are sufficinet to identify a semester
	 *
	 * @param array $params
	 *  an associative array of params
	 */
	public function paramsCanIdentifySemester($params) {
		return (
			isset($params['id']) ||
			isset($params['name']) || 
			isset($params['description']) ||
			(
				(isset($params['college']) || isset($params['college_id'])) && 
				(isset($params['begin_date']) && isset($params['end_date']))
			)
		);
	}

	/**
	 * Override DAO::create()
	 */
	public function create($params) {
		if ((!isset($params['college']) && !isset($params['college_id'])) ||
				!isset($params['name']) ||
				!isset($params['description'])||
				!isset($params['begin_date'])||
				!isset($params['end_date'])
			) 
		{
			throw new Exception('incomplete college semester params - ' . print_r($params, true));
			return ;

		} else {

			$college_id = isset($params['college_id']) ? $params['college_id'] : null;
			$this->college->read(array(
				'id' => $college_id,
				'name' => $params['college']
			));

			$quest_id = $this->quest->create(array(
				'objective' => $params['name'],
				'user_id' => 1, //user_id stands for admin
				'type' => 'college_semester',
				'description' => $params['description'],
			));

			// link this semester to the college that offers it
			$this->quest_affiliation_linkage->create(array(
				'affiliation_id' => $this->college->id,
				'quest_id' => $quest_id,
			));

			$this->quest_date_range->create(array(
				'quest_id' => $quest_id,
				'begin_date' => $params['begin_date'],
				'end_date' => $params['end_date'],
			));

			return $quest_id;

		}
		
	}

	/**
	 * Override DAO::read()
	 */
	public function read($params) {
		if (!$this->paramsCanIdentifySemester($params)) 
		{
			throw new Exception('unknow college semester identifier - ' . print_r($params, true));
			return ;

		} else {
			$quest_id = null;
			if (isset($params['id'])) {
				$quest_id = isset($params['id']) ? $params['id'] : null;
				unset($params['id']);

			}

			$college_result = $this->college->read(array(
				'id' => $params['college_id'],
				'name' => $params['college']
			));

			$quest_result = $this->quest->read(array(
				'id' => $params['id'],
				'objective' => $params['name'],
				'type' => 'college_semester',
			));

			$this->quest_date_range->read(array(
				'quest_id' => $this->quest->id,
			));

			if ($quest_result && $college_result) { 
				$this->attr['college'] = $this->college->name;
				$this->attr['college_id'] = $this->college->id;
				$this->attr['id'] = $this->quest->id;
				$this->attr['name'] = $this->quest->objective;
				$this->attr['description'] = $this->quest->description;
				$this->attr['begin_date'] = $this->quest_date_range->begin_date;
				$this->attr['end_date'] = $this->quest_date_range->end_date;
				return true;

			} else {
				return false;

			}

		}

	}

	/**
	 * Override DAO::update()
	 *
	 * Only the dates, name, nad description can be updated!
	 */
	public function update() {
		$this->quest->objective = $this->attr['name'];
		$this->quest->description = $this->attr['description'];
		$this->quest->update();

		$this->quest_date_range->begin_date = $this->attr['begin_date'];
		$this->quest_date_range->end_date = $this->attr['end_date'];
		$this->quest_date_range->update();
	}

	/**
	 * Overrid DAO::destroy()
	 */
	public function destroy() {
		$this->quest_affiliation_linkage->destroy();
		$this->quest->destroy();
		$this->quest_date_range->destroy();

	}

}
