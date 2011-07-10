<?php

/**
 * Represent the relationship among semesters and courses
 */
class CollegeSemesterCourseLinkageDAO extends DAO implements DAOInterface {

	protected $college_course;
	protected $college_semester;
	protected $linkage;

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'semester',
			'course',
		);

		$this->college_course = Factory::DAO('CollegeCourse');
		$this->college_semester = Factory::DAO('CollegeSemester');
		$this->linkage = Factory::DAO('QuestLinkage');

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Read the semester and course record
	 *
	 * This is a helper function.
	 *
	 * @param array $params
	 *   - semester: either the id or the name of the semester is required to 
	 *     identify the record
	 *     - id: (optional)
	 *     - name: (optional)
	 *   - course: either the id, course code (abbr, num) or the name of the 
	 *     course is required to identify the record
	 *     - id: (optional)
	 *     - title: (optional)
	 *     - abbr: (optional)
	 *     - num: (optional)
	 */
	private function readSemesterAndCourse($params) {
		$semester_id = isset($params['semester']['id']) ? $params['semester']['id'] : null;
		$semester_name = isset($params['semester']['name']) ? isset($params['semester']['name']) : null;
		$semester_result = $this->college_semester->read(array(
			'id' => $semester_id,
			'name' => $semester_name,
		));

		$course_id = isset($params['course']['id']) ? $params['course']['id'] : null;
		$course_title = isset($params['course']['title']) ? isset($params['course']['title']) : null;
		$abbr = isset($params['course']['abbr']) ? isset($params['course']['abbr']) : null;
		$num = isset($params['course']['num']) ? isset($params['course']['num']) : null;
		$course_result = $this->college_course->read(array(
			'id' => $course_id,
			'title' => $course_title,
			'abbr' => $abbr,
			'num' => $num,
		));

		return $semester_result && $course_result;

	}

	/**
	 * Override DAO::create()
	 *
	 * Creates the linkage among a semester and a course.
	 *
	 * @param array $params
	 *   - semester: either the id or the name of the semester is required to 
	 *     identify the record
	 *     - id: (optional)
	 *     - name: (optional)
	 *   - course: either the id, course code (abbr, num) or the name of the 
	 *     course is required to identify the record
	 *     - id: (optional)
	 *     - title: (optional)
	 *     - abbr: (optional)
	 *     - num: (optional)
	 */
	public function create($params) {
		$this->readSemesterAndCourse($params);
		$this->linkage->create(array(
			'parent_id' => $this->college_semester->id,
			'child_id' => $this->college_course->id,
		));

	}

	/**
	 * Override DAO::read()
	 *
	 * @param array $params
	 *   - semester: either the id or the name of the semester is required to 
	 *     identify the record
	 *     - id: (optional)
	 *     - name: (optional)
	 *   - course: either the id, course code (abbr, num) or the name of the 
	 *     course is required to identify the record
	 *     - id: (optional)
	 *     - title: (optional)
	 *     - abbr: (optional)
	 *     - num: (optional)
	 */
	public function read($params) {
		$result = $this->readSemesterAndCourse($params);
		$linkage_result = $this->linkage->read(array(
			'parent_id' => $this->college_semester->id,
			'child_id' => $this->college_course->id,
		));

		if ($result && $linkage_result) {
			$this->attr['id'] = $this->linkage->id;
			$this->attr['semester'] = $this->college_semester->attribute;
			$this->attr['course'] = $this->college_course->attribute;
			return true;

		} else {
			return false;

		}

	}

	/**
	 * Override DAO::update()
	 *
	 * This only updates the linkage.
	 */
	public function update() {
		$this->linkage->parent_id = $this->college_semester->id;
		$this->linkage->child_id = $this->college_course->id;
		$this->linkage->update();

	}

	/**
	 * Override DAO::destroy()
	 *
	 * This only destory the linkage.
	 */
	public function destroy() {
		$this->linkage->destroy();

	}

}
