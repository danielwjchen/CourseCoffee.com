<?php

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/DAOSetup.php';

interface DAOTestTemplate {
	/**
	 * Set up test case.
	 */
	public function setUp();

	/**
	 * Tear down test case.
	 */
	public function tearDown();

	/**
	 * Test object instantiation.
	 */
	public function testInstantiation();

	/**
	 * Test object creation
	 */
	public function testCreate();

	/**
	 * Test object read.
	 */
	public function testRead();

	/**
	 * Test object update.
	 */
	public function testUpdate();

	/**
	 * Test object destroy.
	 */
	public function testDestroy();

}

abstract class DAOTestCase extends CoreTestCase{
	protected $db;

	function __construct() {
		parent::__construct();
		$this->db = new DB($this->config->db);
		Factory::Init($this->config->db);
    DAOSetup::Init($this->config->db);
	}

  function setUp() {
    return ;
  }

  function tearDown() {
  }

}
