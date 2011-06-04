<?php
/**
 * Define database functions that a DB class must implement
 */
class DB{

	static private $pdo;

	/**
	 * Construct a singleton database connection
	 * 
	 * @param array $config
	 *  an associative array that defines a PDO instance
	 *  - driver
	 *  - host
	 *  - database
	 *  - user
	 *  - password
	 *
	 */
	function __construct($config) {
		if (empty($this->pdo)) {
			$this->pdo = new PDO(
				$config['driver'].':dbname='.$config['database'].';host='.$config['host'].';port='.$config['port'], 
				$config['user'], 
				$config['password']
			);
		}
	}

	/**
	 * Execute a database query and return the result
	 *
	 * @param string $sql
	 *  a SQL query
	 * @param array $arg
	 *  an associatve array of arugument to be binded to the statment. default 
	 *  to null.
	 *
	 * @return array $result
	 *  result of the query operation. 
	 */
	public function fetch($sql, array $arg = NULL) {
		$sth = $this->pdo->prepare($sql);
		$sth->execute($arg);
		$result = $sth->fetchAll();
		if (count($result) == 1) {
			return $result[0];
		} else {
			return $result;
		}
	}

	/**
	 * Execute a database function
	 *
	 * @param string $sql
	 *  a SQL query
	 * @param array $arg
	 *  an associatve array of arugument to be binded to the statment. default 
	 *  to null.
	 *
	 * @return bool $result
	 *  result of the query operation. 
	 */
	public function perform($sql, array $arg = NULL) {
		$sth = $this->pdo->prepare($sql);
		$sth->execute($arg);
	}
}
