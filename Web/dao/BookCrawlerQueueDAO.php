<?php
/**
 * @file
 * Oversee access to book_Crawler_queue
 */
class BookCrawlerQueueDAO extends DAO implements DAOInterface {

	/**
	 * Implement DAO::defineAttribute().
	 */
	protected function defineAttribute() {
		return array(
			'cache_key',
			'status',
			'created',
			'updated',
		);
	}

	/**
	 * Implement DAOInterface::create()
	 */
	public function create($params) {
		return $this->db->insert("
			REPLACE INTO `book_crawler_queue`
				(`cache_key`, `status`, `created`)
			VALUES
				(:cache_key, :status, UNIX_TIMESTAMP())
			",
			array(
				'cache_key' => $params['cache_key'],
				'status'     => $params['status'],
			)
		);
	}

	/**
	 * Implement DAOInterface::read()
	 */
	public function read($params) {
		$data = $this->db->fetch("
				SELECT * FROM `book_crawler_queue`
				WHERE `cache_key` = :cache_key
			",
			array('cache_key' => $params['cache_key'])
		);
		
		return $this->updateAttribute($data);
	}

	/**
	 * Implement DAOInterface::update()
	 */
	public function update() {
		$this->db->perform("
				UPDATE `book_crawler_queue` 
				SET `status` = :status, `updated` = UNIX_TIMESTAMP()
				WHERE `cache_key` = :cache_key
			",
			array(
				'status' => $this->attr['status'], 
				'cache_key' => $this->attr['cache_key']
			)
		);
		
	}

	/**
	 * Implement DAOInterface::destroy()
	 */
	public function destroy() {
		$sql = 'DELETE FROM `book_crawler_queue` WHERE ';
		$where_clause = array();
		$sql_params = array();
		if (!empty($this->attr['status'])) {
			$where_clause[] = '`status` = :status';
			$sql_params['status'] = $this->attr['status'];
		}
		if (!empty($this->attr['cache_key'])) {
			$where_clause[] = '`cache_key` = :cache_key';
			$sql_params['cache_key'] = $this->attr['cache_key'];
		}
		if (!empty($this->attr['created'])) {
			$where_clause[] = '`created` = :created';
			$sql_params['created'] = $this->attr['created'];
		}
		if (!empty($this->attr['updated'])) {
			$where_clause[] = '`updated` = :updated';
			$sql_params['updated'] = $this->attr['updated'];
		}
		$sql .= implode(' OR ', $where_clause);
		$this->db->perform($sql, $sql_params);
	}
}
