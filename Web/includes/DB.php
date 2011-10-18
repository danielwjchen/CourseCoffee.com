<?php
/**
 * Define database functions that a DB class must implement
 */
class DB{

	private $pdo;

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
		try {
			$this->pdo = new PDO(
				$config['driver'].':dbname='.$config['name'].';host='.$config['host'].';port='.$config['port'], 
				$config['user'], 
				$config['password']
			);

		} catch (PDOException $e) {
			echo 'Fail to init PDO: ' . $e->getMessage() . ' - ' . print_r($config, true);
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
		try { 
			$sth = $this->pdo->prepare($sql);
			if (empty($arg)) {
				$result = $sth->execute();

			} else {
				$result = $sth->execute($arg);

			}

			if ($result === false) {
				Logger::Write("PDO::errorInfo(): " . print_r($sth->errorInfo(), true));
				return false;
				
			} 
			$rows = array();
			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$rows[] = $row;
			}

			// we truncated the result if rows were fetched and there is only one row 
			// or records
			$rows = isset($rows[0]) && count($rows) == 1 ? $rows[0] : $rows;

			return empty($rows) ? null : $rows;

		} catch (PDOException $e) {
			Logger::Write("PDO Exception: " . $e->getMessage());
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
	public function fetchList($sql, array $arg = NULL) {
		try { 
			$sth = $this->pdo->prepare($sql);
			if (empty($arg)) {
				$result = $sth->execute();

			} else {
				$result = $sth->execute($arg);

			}

			if ($result === false) {
				Logger::Write("PDO::errorInfo(): " . print_r($sth->errorInfo(), true));
				return false;
				
			} 
			$rows = array();
			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$rows[] = $row;
			}

			return empty($rows) ? null : $rows;

		} catch (PDOException $e) {
			Logger::Write("PDO Exception: " . $e->getMessage());
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
		try {
			$sth = $this->pdo->prepare($sql);
			if (empty($arg)) {
				$result = $sth->execute();

			} else {
				$result = $sth->execute($arg);

			}

			if ($result === false) {
				echo $sql . "\n";
				echo "PDO::errorInfo(): " . print_r($sth->errorInfo(), true) . "\n";
				return false;

			} else {
				return true;
			}

		} catch (PDOException $e) {
			echo 'Fail to preform: ' . $e->getMessage();

		}

	}

	/**
	 * Execure a select query
	 *
	 * @param string $sql
	 *  a SQL query
	 * @param array $arg
	 *  an associatve array of arugument to be binded to the statment
	 *
	 * @return $result
	 *  false on failure, or the primary key of the row created
	 */
	public function insert($sql, array $arg) {
		try {
			$sth = $this->pdo->prepare($sql);
			$result = $sth->execute($arg);

			if ($result === false) {
				echo $sql . "\n";
				echo "PDO::errorInfo(): " . print_r($sth->errorInfo(), true) . "\n";
				return false;

			} else {
				return $this->pdo->lastInsertId();

			}

		} catch (PDOException $e) {
			echo 'Fail to preform: ' . $e->getMessage();

		}

	}

}
