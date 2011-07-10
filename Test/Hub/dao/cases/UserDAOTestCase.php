<?php

require_once __DIR__ . '/../bootstrap.php';

class UserDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$user = new UserDAO($this->db, $this->params);
		$result = (
			$user->account == $this->record['account'] && 
			$user->password == $this->record['password'] && 
			$user->id == $this->record['id']
		);

		$error = print_r($user, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array('account' => '333',	'password' => 'asdasda');
		$user = new UserDAO($this->db);
		$user->create($params);

		$new_record = $this->db->fetch("
			SELECT * FROM user WHERE account = :account",
			array('account' => $params['account'])
		);

		$result = ($new_record['account'] == $params['account']);
		$error = print_r($new_record, true) . print_r($params, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$user = new UserDAO($this->db);
		$user->read($this->params);
		$result = (
			$user->account == $this->record['account'] && 
			$user->password == $this->record['password'] && 
			$user->id == $this->record['id']
		);

		$error = print_r($user, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$user = new UserDAO($this->db, $this->params);
		$user->account = 'foo';
		$user->password = 'bar';
		$user->update();
		$record = $this->db->fetch("
			SELECT * FROM user WHERE id = :id",
			array('id' => $this->record['id'])
		);

		$result = (
			($user->id == $this->record['id']) &&
			($user->account != $this->record['account']) &&
			($record['id'] == $this->record['id']) &&
			($record['account'] != $this->record['account'])
		);

		$error = print_r($user, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$user = new UserDAO($this->db, $this->params);
		$user->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM user WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (empty($user->id) && empty($record));
		$error = print_r($record, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);

	}

}

