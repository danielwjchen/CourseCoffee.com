<?php
/**
 * @file 
 * Process book list item in queue and update cache
 */

class ItemQueueModel extends ItemSuggestModel {

	private $crawler_queue_dao;

	/**
	 * Extend ItemSuggestModel::__construct()
	 */
	function __construct($sub_domain) {
		parent::__construct($sub_domain);
		$this->crawler_queue_dao = new ItemCrawlerQueueListDAO($this->default_db);
	}

	/**
	 * Process item in Queue
	 */
	public function processItemQueue() {
		$this->crawler_dao->status = self::QUEUE_FINISHED;
		$this->crawler_dao->destroy();
		$list_params = array(
			'status' => self::QUEUE_NEW,
			'limit'  => array(
				'offset' => 0,
				'count'  => 10,
			),
		);

		$has_records = true;

		do {
			$has_records = $this->crawler_queue_dao->read($list_params);
			$queue_items = $this->crawler_queue_dao->list;
			for ($i = 0; $i < count($queue_items); $i++) {
				$cache_key = $queue_items[$i]['cache_key'];
				$this->crawler_dao->cache_key = $cache_key;
				$this->crawler_dao->status    = self::QUEUE_STARTED;
				$this->crawler_dao->update();
				try {
					$message = $this->decodeItemCacheKey($cache_key);
					$result  = $this->processItemList();
					if (!isset($result['error'])) {
						$result['message'] = $message;
					}
					$this->cache->set($cache_key, json_encode($result), time() + self::CACHE_EXPIRE);
					$this->crawler_dao->status = self::QUEUE_FINISHED;
					$this->crawler_dao->update();
				} catch (Exception $e) {
					$this->crawler_dao->status = self::QUEUE_FAILED;
					$this->crawler_dao->update();
					Logger::Write(__METHOD__ . ' -  ItemSearch API error: ' . $e->getMessage());
				}

			}
			$list_params['limit']['offset']++;

		} while ($has_records);

	}
}
