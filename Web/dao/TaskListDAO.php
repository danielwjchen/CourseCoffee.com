<?php
/**
 * @file
 * Represents a task (sub-quest) with in a Quest.
 */
class TaskListDAO extends ListDAO implements ListDAOInterface{

	/**
	 * Extend DAO::read()
	 */
	public function read($params) {

		$data = array();
		$sql = "
			SELECT 
				q.id,
				q.user_id AS creator_id,
				sec.id AS section_id,
				sec.num AS section_num,
				crs.num AS course_num,
				sub.abbr AS subject_abbr,
				qt.name AS type,
				q.objective,
				q.description,
				qd.timestamp AS due_date
			FROM quest q
			INNER JOIN quest_type qt
				ON q.type_id = qt.id
				AND qt.name = '" . QuestType::TASK . "'
			INNER JOIN quest_date_linkage qd_linkage
				ON qd_linkage.quest_id = q.id
			INNER JOIN date qd
				ON qd.id = qd_linkage.date_id
		";

		if (isset($params['range'])) {
			$sql = "(" . $sql . "
				INNER JOIN quest_user_linkage qu_linkage
					ON qu_linkage.user_id = :quest_user_id
					AND qu_linkage.quest_id = q.id
				LEFT JOIN (
					quest_section_linkage qs_linkage, 
					section sec,
					course crs,
					subject sub
				)
					ON qs_linkage.quest_id = q.id
					AND qs_linkage.section_id = sec.id
					AND sec.course_id = crs.id
					AND crs.subject_id = sub.id
				%s
				GROUP BY q.id
			) UNION DISTINCT (" . 
			$sql . "
				INNER JOIN (
					quest_section_linkage qs_linkage, 
					user_section_linkage us_linkage,
					section sec,
					course crs,
					subject sub
				)
					ON qs_linkage.quest_id = q.id
					AND qs_linkage.section_id = us_linkage.section_id
					AND us_linkage.user_id = :section_user_id
					AND sec.id = qs_linkage.section_id
					AND sec.course_id = crs.id
					AND crs.subject_id = sub.id
				%s
				GROUP BY q.id
			)
			ORDER BY due_date ASC
			";

			$sql = isset($params['limit']) ? $this->setLimit($sql, $params['limit']) : $sql;

			// if we have a specified begin and end date for the range
			if (
				isset($params['range']['begin_date']) && 
				isset($params['range']['end_date'])) 
			{
				$where_clause = "
					WHERE qd.timestamp >= :begin_date
						AND qd.timestamp <= :end_date
				";
				$sql = sprintf($sql, $where_clause, $where_clause);
				$data = $this->db->fetch($sql, array(
					'begin_date' => $params['range']['begin_date'],
					'end_date' => $params['range']['end_date'],
					'section_user_id' => $params['user_id'],
					'quest_user_id' => $params['user_id'],
				));
			// all tasks due before
			} elseif (isset($params['range']['end_date'])) {
				$where_clause = "
					WHERE qd.timestamp <= :end_date
				";
				$sql = sprintf($sql, $where_clause, $where_clause);
				$data = $this->db->fetch($sql, array(
					'end_date' => $params['range']['end_date'],
					'section_user_id' => $params['user_id'],
					'quest_user_id' => $params['user_id'],
				));
			// all tasks due after
			} elseif (isset($params['range']['begin_date'])) {
				$where_clause = "
					WHERE qd.timestamp >= :begin_date
				";
				$sql = sprintf($sql, $where_clause, $where_clause);
				$data = $this->db->fetch($sql, array(
					'begin_date' => $params['range']['begin_date'],
					'section_user_id' => $params['user_id'],
					'quest_user_id' => $params['user_id'],
				));
			} else {
				throw new Exception("unknown task identifier - " . print_r($params, true));
			}
		// get tasks belong to class
		} elseif (isset($params['section_id'])) {
			$sql .= "
				INNER JOIN (
					quest_section_linkage qs_linkage, 
					section sec,
					course crs,
					subject sub
				)
					ON qs_linkage.quest_id = q.id
					AND qs_linkage.section_id = sec.id
					AND sec.course_id = crs.id
					AND crs.subject_id = sub.id
				WHERE qs_linkage.section_id = :section_id
				GROUP BY q.id
				ORDER BY due_date ASC
			";

			$sql = isset($params['limit']) ? $this->setLimit($sql, $params['limit']) : $sql;

			$data = $this->db->fetch($sql, array(
				'section_id' => $params['section_id'],
			));

		// get tasks belong to user
		} elseif (isset($params['user_id'])) {
			$where_clause = "WHERE qu_linkage.user_id = :user_id";
			$sql = sprintf($sql, $where_clause);
			$data = $this->db->fetch($sql, array(
				'user_id' => $params['user_id'],
			));


		} else {
			throw new Exception("unknown task identifier - " . print_r($params, true));

		}

		// debug
		// error_log(__METHOD__ . ' : data - ' . print_r($data, true));
		// error_log(__METHOD__ . " : sql - " . $sql);
		// error_log(__METHOD__ . " : params- " . print_r($params, true));

		$this->list = $data;
		return !empty($data);
	}

}
