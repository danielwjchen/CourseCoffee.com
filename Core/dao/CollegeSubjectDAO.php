<?php

/**
 * Represents a college subject in database
 *
 * This is actually a composite of several DAOs
 */
class CollegeSubjectDAO extends DAO{

	protected $college;
	protected $quest;
	protected $quest_attribute;
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
			'description',
			'abbr',
		);

		$this->college = Factory::DAO('college');
		$this->quest = Factory::DAO('quest');
		$this->quest_attribute = Factory::DAO('quest_attribute');
		$this->quest_affiliation_linkage = Factory::DAO('quest_affiliation_linkage');

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Check if the provided parameters are sufficient to identify a college 
	 * subject.
	 *
	 * @param array $params
	 *  an associative array of params
	 */
	public function paramsCanIdentifySubject($params) {
		return (
			isset($params['id']) || 
			isset($params['subject']) || 
			isset($params['abbr']) || 
			isset($params['description'])
		);
	}

	/**
	 * Override DAO::create()
	 */
	public function create($params) {
		if (!isset($params['subject']) ||
				(!isset($params['college']) && !isset($params['college_id'])) ||
				!isset($params['abbr']) ||
				!isset($params['description'])) 
		{
			throw new Exception('incomplete college subject params - ' . print_r($params, true));
			return ;

		} else {

			$this->college->read($params);
			$this->quest->create(array(
				'objective' => $params['subject'],
				'user_id' => 1, //user_id stands for admin
				'type' => 'college_subject',
				'description' => $params['description'],
			));

			$this->quest_affiliation_linkage->create(array(
				'affiliation_id' => $this->college->id,
				'quest_id' => $this->quest->id,
			));

			$this->quest_attribute->create(array(
				'quest_id' => $this->quest->id,
				'value' => $params['abbr'],
				'type' => 'college_subject_abbreviation',
			));

			$this->attr['id'] = $this->quest->id;
			$this->attr['subject'] = $this->quest->objective;
			$this->attr['description'] = $this->quest->description;

			$this->attr['abbr'] = $this->quest_attribute->value;

			$this->attr['college'] = $this->college->name;
			$this->attr['college_id'] = $this->college->id;

			
		}
		
	}

	/**
	 * Override DAO::read()
	 */
	public function read($params) {
		if (!$this->college->paramsCanIdentifyCollege($params) || 
				!$this->paramsCanIdentifySubject($params)
			) 
		{
			throw new Exception('unknow college subject identifier - ' . print_r($params, true));
			return ;

		} else {

			$this->college->read($params);
			$params['id'] = isset($params['id']) ? $params['id'] : null;

			$this->quest->read(array(
				'id' => $params['id'],
				'objective' => $params['subject'],
				'user_id' => 1, //user_id stands for admin
				'type' => 'college_subject',
				'description' => $params['description'],
			));

			$this->quest_attribute->read(array(
				'quest_id' => $this->quest->id,
				'type' => 'college_subject_abbreviation',
			));

			$this->attr['id'] = $this->quest->id;
			$this->attr['subject'] = $this->quest->objective;
			$this->attr['description'] = $this->quest->description;

			$this->attr['abbr'] = $this->quest_attribute->value;

			$this->attr['college'] = $this->college->name;
			$this->attr['college_id'] = $this->college->id;

		}

	}

	/**
	 * Override DAO::update()
	 */
	public function update() {
		$this->quest_affiliation_linkage->update(array(
			'affiliation_id' => $this->attr['college_id'],
			'quest_id' => $this->attr['id'],
		));

		$this->quest->update(array(
				'objective' => $this->attr['subject'],
				'user_id' => 1, //user_id stands for admin
				'type' => 'college_subject',
				'description' => $this->attr['description'],
		));

		$this->quest_attribute->update(array(
			'quest_id' => $this->attr['id'],
			'value' => $this->attr['abbr'],
		));

	}

	/**
	 * Overrid DAO::destroy()
	 */
	public function destroy() {
		$this->quest_affiliation_linkage->destroy();
		$this->quest->destroy();

	}

}
