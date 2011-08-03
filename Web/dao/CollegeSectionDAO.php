<?php
/**
 * @file
 * Represent college section records in database
 */
class CollegeSectionDAO extends DAO implements DAOInterface{

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'institution_id',
			'institution',
			'subject_id',
			'subject_title',
			'subject_abbr',
			'course_id',
			'course_title',
			'course_num',
			'section_id',
			'section_num',
		);
		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create().
	 */
	public function create($params) {
		if (!isset($params['course_id']) || !isset($params['num'])) {
			throw new Exception('incomplete college section pramas - ' . print_r($params, true));
			return ;

		}else{
			return $this->db->insert("
				INSERT INTO `section` (`course_id`, `num`)
				VALUES (:course_id, :num)",
			array(
				'course_id' => $params['course_id'], 
				'num' => $params['num']
			));

		}

	}

	/**
	 * Extend DAO::read().
	 *
	 * @param array $params
	 *  - id
	 *  - course_id
	 *  - section_num
	 *  - like
	 *     - subject_abbr
	 *     - course_num
	 *     - section_num
	 */
	public function read($params) {
		$sql = "
			SELECT 
				s.course_id,
				s.id AS section_id,
				c.num AS course_num,
				c.title AS course_title,
				sbt.abbr AS subject_abbr,
				sbt.title AS subject_num
			FROM `section` s
			INNER JOIN course c
				ON s.course_id = c.id
			INNER JOIN subject sbt
				ON c._subject_id = sbt.id
		";
		$sql_params = array();
		
		if (isset($params['id'])) {
			$sql_params = array('id' => $params['id']);
			$sql .= "WHERE s.`id` = :id";

		} elseif (isset($params['course_id']) && isset($params['section_num'])) {
			$sql_params = array(
				'course_id' => $params['course_id'],
				'num' => $params['num'],
			);
			$sql .= "WHERE s.`course_id` = :course_id AND s.`num` = :section_num";

		// get all sections below to a course
		} elseif (isset($params['course_id'])) {
			$sql_params = array('course_id' => $params['course_id']);
			$sql .= "WHERE s.`course_id` = :course_id";
			$this->list = $this->db->fetch($sql, $sql_params);
			return empty($data);

		// match string pattern
		} elseif (isset($params['like'])) {

			if (
				isset($params['like']['subject_abbr']) &&
				isset($params['like']['course_num']) &&
				isset($params['like']['section_num'])
			) {
				$sql .= "
					WHERE sbt.abbr LIKE :subject_abbr
						AND c.num LIKE :course_num
						AND s.num LIKE :section_num
				";

				$sql_params = array(
					'subject_abbr' => '%' . $params['like']['subject_abbr'] . '%',
					'course_num'   => '%' . $params['like']['course_num'] . '%',
					'section_num'  => '%' . $params['like']['section_num'] . '%',
				);

			} elseif (
				isset($params['like']['subject_abbr']) &&
				isset($params['like']['course_num']) 
			){
				$sql .= "
					WHERE sbt.abbr LIKE :subject_abbr
						AND c.num LIKE :course_num
				";

				$sql_params = array(
					'subject_abbr' => '%' . $params['like']['subject_abbr'] . '%',
					'course_num'   => '%' . $params['like']['course_num'] . '%',
				);

			} elseif (isset($params['like']['subject_abbr'])) {
				$sql .= "	WHERE sbt.abbr LIKE :subject_abbr";
				$sql_params = array(
					'subject_abbr' => '%' . $params['like']['subject_abbr'] . '%'
				);

			} else {
				throw new Exception('unknown section pattern');
			}

			$this->list = $this->db->fetch($sql, $sql_params);
			return empty($data);


		} else {
			throw new Exception('unknown section identifier');

		}

		$data = $this->db->fetch($sql, $sql_params);
		return $this->updateAttribute($data);

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `section` SET
				`course_id` = :course_id,
				`num` = :num
			WHERE `id` = :id
		";
		$this->db->perform($sql, array(
			'course_id' => $this->attr['course_id'], 
			'num' => $this->attr['num']
		));

	}

	/**
	 * Extend DAO::destroy().
	 */
	public function destroy() {
		$sql = "DELETE FROM `section` WHERE `id` = :id";
		$this->db->perform($sql, array('id' => $this->attr['id']));

	}
}
