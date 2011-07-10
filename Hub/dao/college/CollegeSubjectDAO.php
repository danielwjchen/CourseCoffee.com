<?php

/**
 * Represents a college subject in database
 *
 * This is actually a composite of several DAOs
 */
class CollegeSubjectDAO extends DAO implements DAOInterface{

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

		$this->college = Factory::DAO('College');
		$this->quest = Factory::DAO('Quest');
		$this->quest_attribute = Factory::DAO('QuestAttribute');
		$this->quest_affiliation_linkage = Factory::DAO('QuestAffiliationLinkage');

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

		} else {

			$college_id = isset($params['college_id']) ? $params['college_id'] : null;
			$this->college->read(array(
				'id' => $college_id,
				'name' => $params['college']
			));

			$quest_id = $this->quest->create(array(
				'objective' => $params['subject'],
				'user_id' => 1, //user_id stands for admin
				'type' => 'college_subject',
				'description' => $params['description'],
			));

			$this->quest_affiliation_linkage->create(array(
				'affiliation_id' => $this->college->id,
				'quest_id' => $quest_id,
			));

			$this->quest_attribute->create(array(
				'quest_id' => $quest_id,
				'value' => $params['abbr'],
				'type' => 'college_subject_abbr',
			));

			return $quest_id;
		}
		
	}

	/**
	 * Override DAO::read()
	 */
	public function read($params) {
		if (
			!(isset($params['college_id']) || isset($params['college'])) &&
			!(isset($params['id']) || isset($params['subject']) || isset($params['abbr']))
		) {
			throw new Exception('unknow college subject identifier - ' . print_r($params, true));
			return ;

		} else {
			$college_id = isset($params['college_id']) ? $params['college_id'] : null;
			$quest_result = false;
			$attribute_result = false;

			$this->college->read(array(
				'id' => $college_id,
				'name' => $params['college']
			));
			
			if (isset($params['id'])) {
				$quest_result = $this->quest->read(array('id' => $params['id']));
				$attribute_result = $this->quest_attribute->read(array(
					'quest_id' => $this->quest->id,
					'type' => 'college_subject_abbr',
				));

			} elseif (isset($params['abbr'])) {
				$attribute_result = $this->quest_attribute->read(array(
					'type' => 'college_subject_abbr',
					'value' => $params['abbr']
				));

				$quest_result = $this->quest->read(array('id' => $this->quest_attribute->quest_id));

			} elseif (isset($params['subject'])) {
				$quest_result = $this->quest->read(array(
					'objective' => $params['subject'],
					'type' => 'college_subject',
				));

				$attribute_result = $this->quest_attribute->read(array(
					'type' => 'college_subject_abbr',
					'quest_id' => $this->quest->id,
				));

			}

			if ($quest_result && $attribute_result) {
				$this->attr['id'] = $this->quest->id;
				$this->attr['subject'] = $this->quest->objective;
				$this->attr['description'] = $this->quest->description;
				$this->attr['abbr'] = $this->quest_attribute->value;
				$this->attr['college'] = $this->college->name;
				$this->attr['college_id'] = $this->college->id;
				return true;

			} else {
				return false;

			}

		}

	}

	/**
	 * Override DAO::update()
	 */
	public function update() {
		$this->quest->objective = $this->attr['subject'];
		$this->quest->user_id = 1;
		$this->quest->description = $this->attr['description'];
		$this->quest->update();

		$this->quest_attribute->id = $this->attr['id'];
		$this->quest_attribute->value = $this->attr['abbr'];
		$this->quest_attribute->update();

	}

	/**
	 * Overrid DAO::destroy()
	 */
	public function destroy() {
		$this->quest_affiliation_linkage->destroy();
		$this->quest->destroy();
		$this->quest_attribute->destroy();

	}

}
