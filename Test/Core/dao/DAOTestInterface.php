<?php

interface DAOTestInterface{
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

