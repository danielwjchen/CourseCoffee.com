<?php
/**
 * @file
 * Represent section record in database
 */
class SectionDAO extends DAO implements DAOInterface{

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'subject_abbr',
			'course_num',
			'course_id',
			'num',
			'credit',
			'syllabus_status',
			'syllabus_id',
		);
		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create().
	 */
	public function create($params) {
		if (
				!isset($params['course_id']) || 
				!isset($params['num']) ||
				!isset($params['credit']) ||
				!isset($params['syllabus_status']) ||
				!isset($params['syllabus_id'])
		) {
			throw new Exception('incomplete section pramas - ' . print_r($params, true));
			return ;

		}else{
			return $this->db->insert("
				INSERT INTO `section` (
					`course_id`, 
					`num`,
					`credit`,
					`syllabus_status`,
					`syllabus_id`
				) VALUES (
					:course_id, 
					:num,
					:credit,
					:syllabus_status,
					:syllabus_id
				)",
			array(
				'course_id'       => $params['course_id'], 
				'num'             => $params['num'],
				'credit'          => $params['credit'],
				'syllabus_status' => $params['syllabus_status'],
				'syllabus_id'    => $params['syllabus_id'],
			));

		}

	}

	/**
	 * Extend DAO::read().
	 */
	public function read($params) {
		$sql = "
			SELECT 
				sec.id AS section_id,
				sec.course_id,
				sec.num AS section_num,
				sec.credit,
				sec.syllabus_status,
				sec.syllabus_id,
				crs.num AS course_num,
				sub.abbr AS subject_abbr
			FROM `section` sec
			INNER JOIN `course` crs
				ON sec.course_id = crs.id
			INNER JOIN `subject` sub
				ON crs.subject_id = sub.id
			WHERE 
		";
		
		if (isset($params['id'])) {
			$params = array('id' => $params['id']);
			$sql .= "sec.`id` = :id";

		} else {
			throw new Exception('unknown section identifier');

		}

		$data = $this->db->fetch($sql, $params);
		return $this->updateAttribute($data);

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `section` SET
				`num` = :num,
				`credit` = :credit,
				`syllabus_status` = :syllabus_status,
				`syllabus_id` = :syllabus_id
			WHERE `id` = :id
		";
		$this->db->perform($sql, array(
			'id'              => $this->attr['id'],
			'num'             => $this->attr['num'],
			'credit'          => $this->attr['credit'],
			'syllabus_id'     => $this->attr['syllabus_id'],
			'syllabus_status' => $this->attr['syllabus_status'],
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
