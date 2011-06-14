<?php

/**
 * Define the basic methods needed to perform a unit test.
 */
interface SetupInterface{

	/**
	 * Prepare the envirionment for testing
	 *
	 * @params string $param
	 *  name of the stage, which is usually the name of the table in databse.
	 */
	static public function Prepare($param);

	/**
	 * Clean up the environment after testing
	 *
	 * @params string $stage
	 *  name of the stage, which is usually the name of the table in databse.
	 */
	static public function CleanUp();
}
