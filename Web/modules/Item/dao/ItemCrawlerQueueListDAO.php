<?php
/**
 * @file
 * Fetch list of items from book_crawler_queue
 */
class ItemCrawlerQueueListDAO extends ListDAO implements ListDAOInterface {

	/**
	 * Implement ListDAOInterface::read()
	 */
	public function read(array $params = null) {
		$sql = "
			SELECT * FROM `book_crawler_queue`
			WHERE `status` = :status
		";

		$sql_params['status'] = $params['status'];
		if (isset($params['limit'])) {
			$sql = $this->setLimit($sql, $params['limit']);
		}

		$this->list = $this->db->fetchList($sql, $sql_params);

		return !empty($this->list);
	}
}
