<?php

class UserDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * Implements SetupInterface::Prepare().
	 */
  public static function Prepare(array $params = array('random' => true)) {
		parent::Prepare();
		$params = array(
			'account' => 's1300045',
			'password' => 'asdfasdfasdfasdf',
		);
		
		self::$db->perform(
			"INSERT INTO `user` (account, password) VALUES (:account, :password)",
			$params
		);

		$record = self::$db->fetch(
			"SELECT * FROM `user` WHERE account = :account AND password = :password",
			$params
		);

		return array('record' => $record, 'params' => $params);
	}
}
