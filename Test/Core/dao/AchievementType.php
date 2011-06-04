<?php

require_once dirname(__FILE__) . '/DAOTestCase.php';

class AchievementTypeDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $defaultObject;
	protected $defaultParams;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH .'AchievementTypeDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		$this->db->perform('TRUNCATE TABLE `achievement_type`');
		$this->defaultParams = array(
			'name' => mt_rand(4, 5)
		);

		$this->db->perform(
			"INSERT INTO `achievement_type` (`name`) VALUE (:name)",
			$this->defaultParams);

		$this->defaultObject = $this->db->fetch(
			"SELECT * FROM `achievement_type` WHERE `name` = :name",
			$this->defaultParams);
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		$this->db->perform('TRUNCATE TABLE `achievement_type`');
	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$type = new AchievementTypeDAO($this->db, $this->defaultParams);

		$result = ($type->id == $this->defaultObject['id'] &&
			$type->name == $this->defaultObject['name']);

		$error = "
			id - {$type->id}
			type - {$type->name}
		";

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testCreate()
	 */
	function testCreate() {
		$params = array('name' => mt_rand(4,5));
		$type = new AchievementTypeDAO($this->db);
		$type->create($params);
		$result = ($type->name == $params['name']);

		$error = print_r($params, true) . "\n" . print_r($type, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testRead().
	 */
	function testRead() {
		$type = new AchievementTypeDAO($this->db);
		$type->read($this->defaultObject);

		$result = ($type->name == $this->defaultObject['name'] && 
				$type->id == $this->defaultObject['id']);

		$error = "
			id - {$type->id}
			name - {$type->name}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$type = new AchievementTypeDAO($this->db, $this->defaultParams);
		$type->name = 'foo bar';
		$type->update();
		$result = ($type->name != $this->defaultObject['name'] &&
				$type->id == $this->defaultObject['id']);

		$error = "
			id - {$type->id}
			name - {$type->name}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$type = new AchievementTypeDAO($this->db, $this->defaultParams);
		$result = (empty($type->name) && empty($type->id));

		$error = "
			name - {$type->name}
			id - {$type->id}
		";

		$this->assertTrue($result, $error);

	}

}
