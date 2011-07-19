<?php

/**
 * Represents a session of a college course
 *
 * This is actually a composite of several DAOs
 * 
 */
class CollegeSessionDAO extends DAO implements DAOInterface{

	protected $college_section;

	protected $quest;
	protected $quest_linkage;
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
			'subject_id',
			'course',
			'course_id',
			'section',
			'section_id',
			'abbr',
			'num',
			'session',
			'type',
			'description',
		);

		$this->college_section = new CollegeSectionDAO($db);
		$this->quest_linkage = new QuestLinkageDAO($db);
		$this->quest = new QuestDAO($db);
		$this->quest_affiliation_linkage = new QuestAffiliationLinkageDAO($db);

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Override DAO::create()
	 */
	public function create($params) {
		if ((!isset($params['college']) && !isset($params['college_id'])) ||
				(!isset($params['subject']) && !isset($params['subject_id']) && !isset($params['abbr'])) ||
				(!isset($params['course']) && !isset($params['course_id']) && !isset($params['num'])) ||
				!isset($params['session']) || !isset($params['type'])
		) {
			throw new Exception('incomplete college session params - ' . print_r($params, true));
			return false;

		} else {

			$this->college_section->read($params);
			$params['description'] = isset($params['description']) ? $params['description'] : null;

			$quest_id = $this->quest->create(array(
				'objective' => $params['session'],
				'user_id' => 1, //user_id stands for admin
				'type' => 'college_session',
				'description' => $params['description'],
			));

			$this->quest_attribute->create(array(
				'quest_id' => $quest_id,
				'value' => $params['type'],
				'type' => 'college_session_type',
			));

			$this->quest_linkage->create(array(
				'parent_id' => $this->college_section->id,
				'child_id' => $quest_id,
			));

			$this->quest_affiliation_linkage->create(array(
				'affiliation_id' => $this->college_section->college_id,
				'quest_id' => $quest_id,
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
			!(isset($params['subject_id']) || isset($params['subject']) || isset($params['abbr'])) &&
			!(isset($params['course_id']) || isset($params['course']) || isset($params['num'])) &&
			!(isset($params['id']) || isset($params['session']))
		) {
			throw new Exception('unknow college session identifier - ' . print_r($params, true));
			return ;

		} else {
			$quest_id = null;
			if (isset($params['id'])) {
				$quest_id = isset($params['id']) ? $params['id'] : null;
				unset($params['id']);

			}

			$section_result = $this->college_section->read($params);
			$quest_result = $this->quest->read(array(
				'id' => $params['id'],
				'objective' => $params['session'],
				'type' => 'college_session',
			));
			$attribute_result = $this->quest_attribute->read(array(
				'quest_id' => $this->quest->id,
				'type' => 'college_session_type',
			));

			if ($section_result && $quest_result && $attribute_result) {
				$this->attr['college'] = $this->college_section->college;
				$this->attr['college_id'] = $this->college_section->college_id;
				$this->attr['subject'] = $this->college_section->subject;
				$this->attr['subject_id'] = $this->college_section->subject_id;
				$this->attr['course'] = $this->college_section->course;
				$this->attr['course_id'] = $this->college_section->course_id;
				$this->attr['section'] = $this->college_section->section;
				$this->attr['section_id'] = $this->college_section->id;
				$this->attr['abbr'] = $this->college_section->abbr;
				$this->attr['num'] = $this->college_section->num;
				$this->attr['id'] = $this->quest->id;
				$this->attr['session'] = $this->quest->objective;
				$this->attr['description'] = $this->quest->description;
				$this->attr['type'] = $this->quest_attribute->value;
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
		$this->quest->objective = $this->attr['section'];
		$this->quest->user_id = 1;
		$this->quest->description = $this->attr['description'];
		$this->quest->update();

		$this->quest_attribute->value = $this->attr['type'];
		$this->quest_attribute->update();

	}

	/**
	 * Overrid DAO::destroy()
	 */
	public function destroy() {
		$this->quest_affiliation_linkage->destroy();
		$this->quest_linkage->destroy();
		$this->quest_attribute->destroy();
		$this->quest->destroy();

	}

}
