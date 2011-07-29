<?php
/**
 * @file
 * Represents a college course in database
 *
 * This is actually a composite of several DAOs
 * 
 */
class CollegeCourseDAO extends DAO implements DAOInterface{

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
		);

		$this->college_subject = new CollegeSubjectDAO($db);
		$this->quest_linkage = new QuestLinkageDAO($db);
		$this->quest = new QuestDAO($db);
		$this->quest_attribute = new QuestAttributeDAO($db);
		$this->quest_affiliation_linkage = new QuestAffiliationLinkageDAO($db);

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Override DAO::create()
	 */
	public function create($params) {
		if ((!isset($params['college']) && !isset($params['college_id'])) ||
				(!isset($params['subject']) && !isset($params['subject_id']) && !isset($params['abbr'])) ||
				!isset($params['num']) ||
				!isset($params['title']) ||
				!isset($params['description'])
		) {
			throw new Exception('incomplete college course params - ' . print_r($params, true));
			return ;

		} else {

			$this->college_subject->read($params);

			$quest_id = $this->quest->create(array(
				'objective' => $params['title'],
				'user_id' => 1, //user_id stands for admin
				'type' => 'college_course',
				'description' => $params['description'],
			));

			// link this course to the subject it studies
			$this->quest_linkage->create(array(
				'parent_id' => $this->college_subject->id,
				'child_id' => $quest_id,
			));

			// link this course to the college that offers it
			$this->quest_affiliation_linkage->create(array(
				'affiliation_id' => $this->college_subject->college_id,
				'quest_id' => $quest_id,
			));

			$this->quest_attribute->create(array(
				'quest_id' => $quest_id,
				'value' => $params['num'],
				'type' => 'college_course_num',
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
			!(isset($params['id']) || isset($params['title']) || isset($params['num']))
		) {
			throw new Exception('unknow college course identifier - ' . print_r($params, true));
			return ;

		} else {
			$quest_result = false;
			$attribute_result = false;
			$subject_result = $this->college_subject->read($params);

			if (isset($params['id'])) {
				$quest_result = $this->quest->read(array('id' => $params['id']));
				$attribute_result = $this->quest_attribute->read(array(
					'quest_id' => $this->quest->id,
					'type' => 'college_course_num',
				));

			} elseif (isset($params['num'])) {
				$attribute_result = $this->quest_attribute->read(array(
					'type' => 'college_course_num',
					'value' => $params['num'],
				));
				
				$quest_result = $this->quest->read(array('id' => $this->quest_attribute->quest_id));

			} elseif (isset($params['title'])) {
				$quest_result = $this->quest->read(array(
					'title' => $params['title'],
					'type' => 'college_course',
				));

				$attribute_result = $this->quest_attribute->read(array(
					'quest_id' => $this->quest->id,
					'type' => 'college_course_num',
				));

			}

			if ($quest_result && $attribute_result && $subject_result) {
				$this->attr['college'] = $this->college_subject->college;
				$this->attr['college_id'] = $this->college_subject->college_id;
				$this->attr['subject_id'] = $this->college_subject->id;
				$this->attr['subject'] = $this->college_subject->subject;
				$this->attr['abbr'] = $this->college_subject->abbr;
				$this->attr['id'] = $this->quest->id;
				$this->attr['title'] = $this->quest->objective;
				$this->attr['description'] = $this->quest->description;
				$this->attr['num'] = $this->quest_attribute->value;
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
		$this->quest->objective = $this->attr['title'];
		$this->quest->user_id = 1;
		$this->quest->description = $this->attr['description'];
		$this->quest->update();

		$this->quest_attribute->id = $this->attr['id'];
		$this->quest_attribute->value = $this->attr['num'];
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
