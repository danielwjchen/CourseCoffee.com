<?php

/**
 * Declare methods that must be tested
 */
interface DAOTestCaseInterface{

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

