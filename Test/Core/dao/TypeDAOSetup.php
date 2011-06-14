<?php

abstract class TypeDAOSetup extends DAOSetup{

	static public function CleanUp() {
		$table = strtolower(preg_replace('/([a-z0-9])([A-Z])/','$1_$2', str_replace('TypeDAOSetup', '', __CLASS__)));
		self::$db->perform("TRUNCATE TABLE `{$table}`");
	}
}
