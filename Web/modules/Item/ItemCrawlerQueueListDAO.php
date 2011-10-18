<?php
/**
 * @file
 * Fetch lists from item_crawler_queue
 */
class ItemCrawlerQueueListDAO extends ListDAO implements ListDAOInterface {

	/**
	 * Implement ListDAOInterface::read()
	 */
	public function read($params) {
		$sql = "
			SELECT * FROM `item_crawler_queue`
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
