<?php

interface DAOSetupInterface{

	/**
	 * Prepare the envirionment for testing
	 *
	 * @param array $param_type
	 *  parameters to be passed, default to null
	 *  - random
	 *    generate param randomly
	 *  - specific
	 *    passing an array of params to be used
	 *  - all
	 *    generate all possible params, usedful for TypeDAO classes when params 
	 *    are defined in already
	 */
  public static function Prepare(array $params = array('random' => true));

	/**
	 * Clean up the environment after testing
	 *
	 * @params string $stage
	 *  name of the stage, which is usually the name of the table in databse.
	 */
	public static function CleanUp();
}
