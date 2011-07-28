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
	 * Access to quest_linkage
	 */
	private $quest_linkage;

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->quest = new QuestDAO($db);
		$this->date = new DateDAO($db);
		$this->quest_date_linkage = new QuestDateLinkageDAO($db);
		$this->quest_linkage = new QuestLinkageDAO($db);

		$attr = array(
			'id',
      'type',
      'type_id',
			'user_id',
			'due_date',
			'quest_id',
			'objective',
			'description',
		);

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create()
	 *
	 * @param array $params
	 *   - quest_id: optional, e.g. an id to identify the class this task 
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
			'type' => 'end_date',
		));
		$this->quest_date_linkage->create(array(
			'quest_id' => $quest_id,
			'date_id' => $date_id,
		));
		if (isset($params['quest_id'])) {
			$this->quest_linkage->create(array(
				'parent_id' => $params['quest_id'],
				'child_id' => $quest_id,
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
				q.user_id,
				q_linkage.parent_id AS quest_id,
				qt.name AS type,
				q.objective,
				q.description,
				l.name AS location,
				qd.timestamp AS due_date
			FROM quest q
			INNER JOIN quest_type qt
				ON q.type_id = qt.id
			INNER JOIN quest_date_linkage qd_linkage
				ON qd_linkage.quest_id = q.id
			INNER JOIN date qd
				ON qd.id = qd_linkage.date_id
			LEFT JOIN quest_location_linkage ql
				ON q.id = ql.quest_id
			LEFT JOIN location l
				ON ql.location_id = l.id
			LEFT JOIN quest_linkage q_linkage
				ON q_linkage.child_id = q.id
			%s
		";
		if (isset($params['limit'])) {
			$sql .= "LIMIT {$params['offset']}, {$params['count']}";
		}

		$data = array();

		// get a particular item
		if (isset($params['id'])) {
			$sql = sprintf($sql, "WHERE q.id = :id");
			$data = $this->db->fetch($sql, array('id' => $params['id']));
			return $this->updateAttrribute($data);

		// get tasks in arange
		} elseif (isset($params['range'])) {
			// if we have a specified begin and end date for the range
			if (
				isset($params['range']['begin_date']) && 
				isset($params['range']['end_date'])) 
			{
				$where_clause = "
					WHERE q.user_id = :user_id
					AND qd.timestamp >= : begin_date
					AND qd.timestamp <= :end_date
				";
				$sql = sprintf($sql, $where_clause);
				$data = $this->db->fetch($sql, array(
					'user_id' => $params['user_id'],
					'begin_date' => $params['range']['begin_date'],
					'end_date' => $params['range']['end_date'],
				));
			// all tasks due before
			} elseif (isset($params['range']['end_date'])) {
				$where_clause = "
					WHERE q.user_id = :user_id
					AND qd.timestamp <= :end_date
				";
				$sql = sprintf($sql, $where_clause);
				$data = $this->db->fetch($sql, array(
					'user_id' => $params['range']['user_id'],
					'end_date' => $params['range']['end_date'],
				));
			// all tasks due after
			} elseif (isset($params['range']['begin_date'])) {
				$where_clause = "
					WHERE q.user_id = :user_id
					AND qd.timestamp >= :begin_date
				";
				$sql = sprintf($sql, $where_clause);
				$data = $this->db->fetch($sql, array(
					'user_id' => $params['user_id'],
					'begin_date' => $params['begin_date'],
				));
			} else {
				throw new Exception("unknown task identifier - " . print_r($params, true));
			}

		// get all tasks belong to user
		} elseif (isset($params['user_id'])) {
				$where_clause = "WHERE q.user_id = :user_id";
				$sql = sprintf($sql, $where_clause);
				$data = $this->db->fetch($sql, array(
					'user_id' => $params['user_id'],
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
