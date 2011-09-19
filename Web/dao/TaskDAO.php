<?php
/**
 * @file
 * Represents a task (sub-quest) with in a Quest.
 */
class TaskDAO extends DAO implements DAOInterface{

	/**
	 * Access to quest record
	 */
	private $quest;

	/**
	 * Access to date record
	 */
	private $date;

	/**
	 * Access to quest_date_linkage
	 */
	private $quest_date_linkage;

	/**
	 * Access to quest_section_linkage
	 */
	private $quest_section_linkage;

	/**
	 * Access to quest_user_linkage
	 */
	private $quest_user_linkage;

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db) {
		parent::__construct($db);
		$this->quest                 = new QuestDAO($db);
		$this->date                  = new DateDAO($db);
		$this->quest_date_linkage    = new QuestDateLinkageDAO($db);
		$this->quest_section_linkage = new QuestSectionLinkageDAO($db);
		$this->quest_user_linkage    = new QuestUserLinkageDAO($db);

	}

	/**
	 * Implement DAO::defineAttribute().
	 */
	protected function defineAttribute() {
		return array(
			'id',
			'type',
			'type_id',
			'creator_id',
			'user_id',
			'due_date',
			'section_id',
			'objective',
			'description',
		);
	}

	/**
	 * Implement DAOInterface::create()
	 *
	 * @param array $params
	 *   - section_id: optional, e.g. an id to identify the class this task 
	 *     belongs to
	 *   - user_id: required
	 *   - due_date: required
	 *   - objective: required
	 *   - description: optional
	 */
	public function create($params) {
		$params['type'] = QuestType::TASK;
		$quest_id = $this->quest->create($params);
		$date_id = $this->date->create(array(
			'timestamp' => $params['due_date'],
			'type'      => 'end_date',
		));
		$this->quest_user_linkage->create(array(
			'quest_id' => $quest_id,
			'user_id'  => $params['user_id'],
		));
		$this->quest_date_linkage->create(array(
			'quest_id' => $quest_id,
			'date_id'  => $date_id,
		));
		if (isset($params['section_id'])) {
			$this->quest_section_linkage->create(array(
				'section_id' => $params['section_id'],
				'quest_id'   => $quest_id,
			));
		}
		return $quest_id;
	}

	/**
	 * Implement DAOInterface::read()
	 */
	public function read($params) {
		if (!isset($params['id'])) {
			throw new Exception("unknown task identifier - " . print_r($params, true));
			return false;
		}

		$sql = "
			SELECT 
				q.id,
				q.user_id AS creator_id,
				qs_linkage.section_id,
				sec.id AS section_id,
				sec.num AS section_num,
				crs.num AS course_num,
				sub.abbr AS subject_abbr,
				qt.name AS type,
				q.objective,
				q.description,
				l.name AS location,
				qd.timestamp AS due_date
			FROM quest q
			INNER JOIN quest_type qt
				ON q.type_id = qt.id
				AND qt.name = :type_name
			INNER JOIN quest_date_linkage qd_linkage
				ON qd_linkage.quest_id = q.id
			INNER JOIN date qd
				ON qd.id = qd_linkage.date_id
			LEFT JOIN (
				quest_section_linkage qs_linkage,
				section sec,
				course crs,
				subject sub
			)
				ON qs_linkage.quest_id = q.id
				AND qs_linkage.section_id = sec.id
				AND sec.course_id = c.id
				AND crs.subject_id = sub.id
			LEFT JOIN (quest_location_linkage ql_linkage, location l)
				AND ql_linkage.location_id = l.id
				ON q.id = ql_linkage.quest_id
			WHERE q.id = :id
			GROUP BY q.id
			ORDER BY due_date ASC
		";

		$data = $this->db->fetch($sql, array(
			'id' => $params['id'],
			'type_name' => QuestTypeSetting::TASK,
		));

		// debug
		// error_log('task dao attribute - ' . print_r($data, true));

		return $this->updateAttribute($data);
	}

	/**
	 * Implement DAOInterface::update()
	 *
	 * This is not tested!
	 */
	public function update() {
		foreach ($this->quest->attribute as $attr => $value) {
			$this->quest->$attr = $this->attr[$attr];
		};
		$this->quest->update();

		$this->date->timestamp = $this->attr['due_date'];
		$this->date->update();
	}

	/**
	 * Implement DAOInterface::destroy()
	 */
	public function destroy() {
		$this->quest->destroy();
		$this->date->destroy();
	}

}
