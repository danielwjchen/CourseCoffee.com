<?php

/**
 * Represents a college semester in database
 *
 * This is actually a composite of several DAOs
 */
class CollegeSemesterDAO extends DAO{

	protected $college;
	protected $quest;
	protected $quest_affiliation_linkage;
	
	// dates
	protected $begin_date;
	protected $end_date;
	protected $quest_date_linkage;

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

		$this->college = Factory::DAO('college');
		$this->quest = Factory::DAO('quest');
		$this->quest_attribute = Factory::DAO('quest_attribute');
		$this->quest_affiliation_linkage = Factory::DAO('quest_affiliation_linkage');

		// dates
		$this->begin_date = Factory::DAO('date');
		$this->end_date = Factory::DAO('date');
		$this->quest_date_linkage = Factory::DAO('quest_date_linkage');

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
	 * Check if the provided parameters are sufficient

	/**
	 * Set the attributes
	 *
	 * This is a helper function.
	 */
	private function setSemesterAttribute() {
		// college
		$this->attr['college'] = $this->college->name;
		$this->attr['college_id'] = $this->college->id;

		// college semester
		$this->attr['id'] = $this->quest->id;
		$this->attr['name'] = $this->quest->objective;
		$this->attr['description'] = $this->quest->description;

		// begin and end dates
		$this->attr['begin_date'] = $this->begin_date->timestamp;
		$this->attr['end_date'] = $this->end_date->timestamp;
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

			$this->college->read($params);

			$this->quest->create(array(
				'objective' => $params['name'],
				'user_id' => 1, //user_id stands for admin
				'type' => 'college_semester',
				'description' => $params['description'],
			));

			// link this semester to the college that offers it
			$this->quest_affiliation_linkage->create(array(
				'affiliation_id' => $this->college->id,
				'quest_id' => $this->quest->id,
			));

			// crea begin and end dates
			$this->begin_date->create(array(
				'timestamp' => $params['begin_date'],
				'type' => 'begin_date',
			));

			$this->end_date->create(array(
				'timestamp' => $params['end_date'],
				'type' => 'end_date',
			));
			// create linkage between begin date and quest
			$thie->quest_date_linkage->create(array(
				'quest_id' => $this->quest->id,
				'date_id' => $this->begin_date->id,
			));

			// create linkage between end date and quest
			$this->quest_date_linkage->create(array(
				'quest_id' => $this->quest->id,
				'date_id' => $this->end_date->id,
			));
			
			$this->setSemesterAttribute();

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

			$this->college->read($params);

			$params['id'] = isset($params['id']) ? $params['id'] : null;

			$this->quest->read(array(
				'id' => $params['id'],
				'objective' => $params['title'],
				'type' => 'college_semester',
				'description' => $params['description'],
			));

			$this->quest_date_linkage->read(array(
				'quest_id' => $this->quest->id,
			));

			$this->begin_date->read(array(
				'quest_id' => $this->quest_date_linkage->date_id,
				'type' => 'begin_date',
			));

			$this->end_date->read(array(
				'quest_id' => $this->quest_date_linkage->date_id,
				'type' => 'end_date',
			));

			$this->setSemesterAttribute();

		}

	}

	/**
	 * Override DAO::update()
	 */
	public function update() {
		$this->college->read($params);

		$this->quest_affiliation_linkage->update(array(
			'affiliation_id' => $this->attr['college_id'],
			'quest_id' => $this->attr['id'],
		));

		$this->quest_linkage->update(array(
			'parent_id' => $this->college_subject->id,
			'child_id' => $this->quest->id,
		));

		$this->quest->update(array(
				'objective' => $this->attr['title'],
				'user_id' => 1, //user_id stands for admin
				'type' => 'college_semester',
				'description' => $this->attr['description'],
		));

		$this->quest_attribute->update(array(
			'quest_id' => $this->attr['id'],
			'value' => $this->attr['num'],
		));

	}

	/**
	 * Overrid DAO::destroy()
	 */
	public function destroy() {
		$this->quest_affiliation_linkage->destroy();
		$this->quest_linkage->destroy();
		$this->quest->destroy();

	}

}
