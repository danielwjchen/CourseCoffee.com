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
	function __construct($db, $params = NULL) {
		$this->quest                 = new QuestDAO($db);
		$this->date                  = new DateDAO($db);
		$this->quest_date_linkage    = new QuestDateLinkageDAO($db);
		$this->quest_section_linkage = new QuestSectionLinkageDAO($db);
		$this->quest_user_linkage    = new QuestUserLinkageDAO($db);

		$attr = array(
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

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create()
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
	 * Extend DAO::read()
	 */
	public function read($params) {
		$sql = "
			SELECT 
				q.id,
				q.user_id AS creator_id,
				qu_linkage.user_id,
				qs_linkage.section_id,
				qt.name AS type,
				q.objective,
				q.description,
				l.name AS location,
				qd.timestamp AS due_date
			FROM quest q
			INNER JOIN quest_user_linkage qu_linkage
				ON qu_linkage.quest_id = q.id
			INNER JOIN user u
				ON qu_linkage.user_id = u.id
			INNER JOIN quest_type qt
				ON q.type_id = qt.id
				AND qt.name = :type_name
			INNER JOIN quest_date_linkage qd_linkage
				ON qd_linkage.quest_id = q.id
			INNER JOIN date qd
				ON qd.id = qd_linkage.date_id
			LEFT JOIN quest_location_linkage ql_linkage
				ON q.id = ql_linkage.quest_id
			LEFT JOIN location l
				ON ql_linkage.location_id = l.id
			LEFT JOIN quest_section_linkage qs_linkage
				ON qs_linkage.quest_id = q.id
			LEFT JOIN user_section_linkage us_linkage
				ON us_linkage.section_id = qs_linkage.section_id
				AND us_linkage.user_id = u.id
			%s
			ORDER BY due_date ASC
		";
		if (isset($params['limit'])) {
			$sql .= "
				LIMIT {$params['limit']['offset']}, {$params['limit']['count']} 
			";
		}

		$data = array();

		// get a particular item
		if (isset($params['id'])) {
			$sql = sprintf($sql, "WHERE q.id = :id");
			$data = $this->db->fetch($sql, array(
				'id' => $params['id'],
				'type_name' => QuestType::TASK,
			));
			return $this->updateAttribute($data);

		// get tasks in arange
		} elseif (isset($params['range'])) {
			// if we have a specified begin and end date for the range
			if (
				isset($params['range']['begin_date']) && 
				isset($params['range']['end_date'])) 
			{
				error_log( $params['range']['end_date']);
				$where_clause = "
					WHERE u.id = :user_id
						AND qd.timestamp >= :begin_date
						AND qd.timestamp <= :end_date
				";
				$sql = sprintf($sql, $where_clause);
				$data = $this->db->fetch($sql, array(
					'user_id' => $params['user_id'],
					'begin_date' => $params['range']['begin_date'],
					'end_date' => $params['range']['end_date'],
					'type_name' => QuestType::TASK,
				));
			// all tasks due before
			} elseif (isset($params['range']['end_date'])) {
				$where_clause = "
					WHERE u.id = :user_id
						AND qd.timestamp <= :end_date
				";
				$sql = sprintf($sql, $where_clause);
				$data = $this->db->fetch($sql, array(
					'user_id' => $params['range']['user_id'],
					'end_date' => $params['range']['end_date'],
					'type_name' => QuestType::TASK,
				));
			// all tasks due after
			} elseif (isset($params['range']['begin_date'])) {
				$where_clause = "
					WHERE u.id = :user_id
						AND qd.timestamp >= :begin_date
				";
				$sql = sprintf($sql, $where_clause);
				$data = $this->db->fetch($sql, array(
					'user_id' => $params['user_id'],
					'begin_date' => $params['begin_date'],
					'type_name' => QuestType::TASK,
				));
			} else {
				throw new Exception("unknown task identifier - " . print_r($params, true));
			}

		// get tasks belong to user
		} elseif (isset($params['user_id'])) {
				$where_clause = "WHERE u.id = :user_id";
				$sql = sprintf($sql, $where_clause);
				$data = $this->db->fetch($sql, array(
					'user_id' => $params['user_id'],
					'type_name' => QuestType::TASK,
				));

		// get tasks belong to class
		} elseif (isset($params['section_id'])) {
			$where_clause = "
				WHERE qs_linkage.section_id = :section_id
			";
			$sql = sprintf($sql, $where_clause);
			$data = $this->db->fetch($sql, array(
				'section_id' => $params['section_id'],
			));

		} else {
			throw new Exception("unknown task identifier - " . print_r($params, true));

		}

		$this->list = $data;
		return empty($data);
	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$this->quest->update($this->attr);
		$this->date->update(array(
			'id' => $this->date->id,
			'type' => $this->date->type,
			'timestamp' => $this->attr['due_date']
		));
	}

	/**
	 * Extend DAO::destroy()
	 */
	public function destroy() {
		$this->quest->destroy();
		$this->date->destroy();
	}

}
