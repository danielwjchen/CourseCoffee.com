<?php
/**
 * @file 
 * Process book list item in queue and update cache
 */

class BookQueueModel extends BookSuggestModel {

	private $crawler_queue_dao;

	/**
	 * Extend BookSuggestModel::__construct()
	 */
	function __construct($sub_domain) {
		parent::__construct($sub_domain);
		$this->crawler_queue_dao = new BookCrawlerQueueListDAO($this->default_db);
	}

	/**
	 * Process item in Queue
	 */
	public function processBookQueue() {
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
					$message = $this->decodeBookCacheKey($cache_key);
					$result  = $this->processBookList();
					if (!isset($result['error'])) {
						$result['message'] = $message;
					}
					$this->cache->set($cache_key, json_encode($result), time() + self::CACHE_EXPIRE);
					$this->crawler_dao->status = self::QUEUE_FINISHED;
					$this->crawler_dao->update();
				} catch (Exception $e) {
					$this->crawler_dao->status = self::QUEUE_FAILED;
					$this->crawler_dao->update();
					Logger::Write(__METHOD__ . ' -  BookSearch API error: ' . $e->getMessage());
				}

			}
			$list_params['limit']['offset']++;

		} while ($has_records);

	}
}
