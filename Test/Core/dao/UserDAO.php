<?php

require_once dirname(__FILE__) . '/DAOTestCase.php';

class UserDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $defualtUser;
	protected $defaultParams;

	/**
	 * Implement DAOTestTemplate::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH . 'UserDAO.php';

	}

	/**
	 * Implement DAOTestTemplate::setUp().
	 */
	function setUp() {
		$this->db->perform('TRUNCATE TABLE `user`');
		$this->defaultParams = array(
			'account' => 's1300045',
			'password' => 'asdfasdfasdfasdf',
		);
		$this->db->perform(
			"INSERT INTO `user` (account, password) VALUES (:account, :password)",
			$this->defaultParams);
		$this->defaultObject = $this->db->fetch(
			"SELECT * FROM `user` WHERE account = :account AND password = :password",
			$this->defaultParams);
	}

	/**
	 * Implement DAOTestTemplate::tearDown().
	 */
	function tearDown() {
		$this->db->perform('TRUNCATE TABLE `user`');
	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$user = new UserDAO($this->db, $this->defaultParams);

		$result = ($user->account == $this->defaultObject['account'] && 
				$user->password == $this->defaultObject['password'] && 
				$user->id == $this->defaultObject['id']);

		$error = "
			id - {$user->id}
			account - {$user->account}
			password - {$user->password}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testCreate()
	 */
	function testCreate() {
		$credential = array('account' => '333',	'password' => 'asdasda');
		$user = new UserDAO($this->db);
		$user->create($credential);
		$result = ($user->account == $credential['account'] &&
				$user->password == $credential['password']);

		$this->assertTrue($result, 'User Creation');

	}

	/**
	 * Implement DAOTestTemplate::testRead().
	 */
	function testRead() {
		$user = new UserDAO($this->db);
		$user->read($this->defaultParams);

		$result = ($user->account == $this->defaultObject['account'] && 
				$user->password == $this->defaultObject['password'] && 
				$user->id == $this->defaultObject['id']);

		$error = "
			id - {$user->id}
			account - {$user->account}
			password - {$user->password}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$user = new UserDAO($this->db, $this->defaultParams);
		$user->account = 'foo';
		$user->password = 'bar';
		$user->update();
		$result = (($user->account != $this->defaultObject['account'] && 
				$user->password != $this->defaultObject['password']) &&
				$user->id == $this->defaultObject['id']);

		$this->assertTrue($result, 'User Update');

	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$user = new UserDAO($this->db, $this->defaultParams);
		$result = (empty($user->account) && 
				empty($user->password) &&
				empty($user->id));
		$error = "
			account - {$user->account}
			password - {$user->password}
			id - {$user->id}
		";

		$this->assertTrue($result, $error);

	}

}

