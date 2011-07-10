<?php

/**
 * Represents a college course in database
 *
 * This is actually a composite of several DAOs
 */
class CollegeCourseDAO extends DAO{

	protected $college_semester;
	protected $college_subject;

	protected $quest;
	protected $quest_attribute;
	protected $quest_linkage;
	protected $quest_affiliation_linkage;
	
	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'college',
			'college_id',
			'subject',
			'subject_id',
			'abbr',
			'num',
			'title',
			'description',
			'begin_date',
			'end_date',
		);

		$this->college = Factory::DAO('college');
		$this->college_subject = Factory::DAO('college_subject');
		$this->quest_linkage = Factory::DAO('quest_linkage');
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
	 * Get College and subject data.
	 *
	 * This is a helper function.
	 * 
	 * @param array $params
	 *  an associative array of params
	 */
	private function getCollegeAndSubject($params) {
		$this->college->read($params);
		$this->college_subject->read($params);
	}

	/**
	 * Check if the provided parameters are sufficinet to identify a course
	 *
	 * @param array $params
	 *  an associative array of params
	 */
	public function paramsCanIdentifyCourse($params) {
		return (isset($params['id']) ||
			isset($params['num']) || 
			isset($params['title']) ||
			isset($params['description'])
		);
	}

	/**
	 * Check if the provided parameters are sufficient

	/**
	 * Set the attributes
	 *
	 * This is a helper function.
	 */
	private function setCourseAttribute() {
		// college
		$this->attr['college'] = $this->college->name;
		$this->attr['college_id'] = $this->college->id;

		// college subject
		$this->attr['subject_id'] = $this->college_subject->id;
		$this->attr['subject'] = $this->college_subject->subject;
		$this->attr['abbr'] = $this->college_subject->abbr;

		// college course
		$this->attr['id'] = $this->quest->id;
		$this->attr['title'] = $this->quest->objective;
		$this->attr['description'] = $this->quest->description;
		$this->attr['num'] = $this->quest_attribute->value;

		// begin and end dates
		$this->attr['begin_date'] = $this->begin_date->timestamp;
		$this->attr['end_date'] = $this->end_date->timestamp;
	}

	/**
	 * Override DAO::create()
	 */
	public function create($params) {
		if ((!isset($params['college']) && !isset($params['college_id'])) ||
				(!isset($params['subject']) && !isset($params['subject_id']) && !isset($params['abbr'])) ||
				!isset($params['num']) ||
				!isset($params['title']) ||
				!isset($params['description'])||
				!isset($params['begin_date'])||
				!isset($params['end_date'])) 
		{
			throw new Exception('incomplete college course params - ' . print_r($params, true));
			return ;

		} else {

			$this->getCollegeAndSubject($params);

			$this->quest->create(array(
				'objective' => $params['title'],
				'user_id' => 1, //user_id stands for admin
				'type' => 'college_course',
				'description' => $params['description'],
			));

			// link this course to the subject it studies
			$this->quest_linkage->create(array(
				'parent_id' => $this->college_subject->id,
				'child_id' => $this->quest->id,
			));

			// link this course to the college that offers it
			$this->quest_affiliation_linkage->create(array(
				'affiliation_id' => $this->college->id,
				'quest_id' => $this->quest->id,
			));

			$this->quest_attribute->create(array(
				'quest_id' => $this->quest->id,
				'value' => $params['num'],
				'type' => 'college_course_number',
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
			
			$this->setCourseAttribute();

		}
		
	}

	/**
	 * Override DAO::read()
	 */
	public function read($params) {
		if (!$this->college_subject->paramsCanIdentifySubject($params) ||
				!$this->paramsCanIdentifyCourse($params)
			) 
		{
			throw new Exception('unknow college course identifier - ' . print_r($params, true));
			return ;

		} else {

			$this->getCollegeAndSubject($params);
			$params['id'] = isset($params['id']) ? $params['id'] : null;

			$this->quest->read(array(
				'id' => $params['id'],
				'objective' => $params['title'],
				'type' => 'college_course',
				'description' => $params['description'],
			));

			$this->quest_attribute->read(array(
				'quest_id' => $this->quest->id,
				'type' => 'college_course_number',
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

			$this->setCourseAttribute();

		}

	}

	/**
	 * Override DAO::update()
	 */
	public function update() {
		$this->getCollegeAndSubject($this->attr);

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
				'type' => 'college_course',
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
