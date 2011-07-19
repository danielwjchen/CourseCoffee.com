<?php

/**
 * Represents a section of a college course
 *
 * This is actually a composite of several DAOs
 * 
 */
class CollegeSectionDAO extends DAO implements DAOInterface{

	protected $college_course;

	protected $quest;
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
			'course',
			'course_id',
			'abbr',
			'num',
			'section',
			'description',
		);

		$this->college_course  = new CollegeCourseDAO($db);
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
				!isset($params['section']) ||
				!isset($params['description'])
		) {
			throw new Exception('incomplete college section params - ' . print_r($params, true));
			return false;

		} else {

			$this->college_course->read($params);
			$params['description'] = isset($params['description']) ? $params['description'] : null;

			$quest_id = $this->quest->create(array(
				'objective' => $params['section'],
				'user_id' => 1, //user_id stands for admin
				'type' => 'college_section',
				'description' => $params['description'],
			));

			// link this course to the subject it studies
			$this->quest_linkage->create(array(
				'parent_id' => $this->college_course->id,
				'child_id' => $quest_id,
			));

			// link this course to the college that offers it
			$this->quest_affiliation_linkage->create(array(
				'affiliation_id' => $this->college_course->college_id,
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
			!(isset($params['id']) || isset($params['section']))
		) {
			throw new Exception('unknow college section identifier - ' . print_r($params, true));
			return false;

		} else {
			$quest_id = null;
			if (isset($params['id'])) {
				$quest_id = isset($params['id']) ? $params['id'] : null;
				unset($params['id']);

			}

			$course_result = $this->college_course->read($params);
			$quest_result = $this->quest->read(array(
				'id' => $quest_id,
				'objective' => $params['section'],
				'type' => 'college_section',
			));

			if ($course_result && $quest_result) {
				$this->attr['college'] = $this->college_course->college;
				$this->attr['college_id'] = $this->college_course->college_id;
				$this->attr['subject_id'] = $this->college_course->subject_id;
				$this->attr['subject'] = $this->college_course->subject;
				$this->attr['course_id'] = $this->college_course->id;
				$this->attr['course'] = $this->college_course->title;
				$this->attr['abbr'] = $this->college_course->abbr;
				$this->attr['num'] = $this->college_course->num;
				$this->attr['id'] = $this->quest->id;
				$this->attr['section'] = $this->quest->objective;
				$this->attr['description'] = $this->quest->description;

				return true;

			} else {
				return false;

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
