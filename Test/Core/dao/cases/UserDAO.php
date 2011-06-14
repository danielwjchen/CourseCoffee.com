<?php

require_once __DIR__ . '/../bootstrap.php';

class UserDAOTestCase extends DAOTestCase implements DAOTestInterface{

	protected $record;
	protected $params;

	/**
	 * Implement DAOTestInterface::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH . 'UserDAO.php';

	}

	/**
	 * Implement DAOTestInterface::setUp().
	 */
	function setUp() {
		DAOSetup::CleanUp('user');
		$stage = DAOSetup::Prepare('user');
		$this->record = $stage['record'];
		$this->params = $stage['params'];
	}

	/**
	 * Implement DAOTestInterface::tearDown().
	 */
	function tearDown() {
		DAOSetup::CleanUp('user');
	}

	/**
	 * Implement DAOTestInterface::testInstantiation()
	 */
	function testInstantiation() {
		$user = new UserDAO($this->db, $this->params);

		$result = ($user->account == $this->record['account'] && 
				$user->password == $this->record['password'] && 
				$user->id == $this->record['id']);

		$error = print_r($user, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestInterface::testCreate()
	 */
	function testCreate() {
		$params = array('account' => '333',	'password' => 'asdasda');
		$user = new UserDAO($this->db);
		$user->create($params);
		$result = ($user->account == $params['account'] &&
				$user->password == $params['password']);

		$error = print_r($user, true) . print_r($params, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestInterface::testRead().
	 */
	function testRead() {
		$user = new UserDAO($this->db);
		$user->read($this->params);

		$result = ($user->account == $this->record['account'] && 
				$user->password == $this->record['password'] && 
				$user->id == $this->record['id']);

		$error = print_r($user, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestInterface::testUpdate().
	 */
	function testUpdate() {
		$user = new UserDAO($this->db, $this->params);
		$user->account = 'foo';
		$user->password = 'bar';
		$user->update();
		$result = (($user->account != $this->record['account'] && 
				$user->password != $this->record['password']) &&
				$user->id == $this->record['id']);

		$error = print_r($user, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestInterface::testDestroy().
	 */
	function testDestroy() {
		$user = new UserDAO($this->db, $this->params);
		$record = $this->db->perform(
			'SELECT * FROM user WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($user->id) && empty($record));

		$error = print_r($user, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);

	}

}

