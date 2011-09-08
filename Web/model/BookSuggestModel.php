<?php
/**
 * @file
 * Suggest list of books for a class
 */

require_once LIB_PATH . '/booksearcher/AmazonAPI.php';
require_once LIB_PATH . '/booksearcher/BarnesNobleAPI.php';
require_once LIB_PATH . '/booksearcher/BookRenterAPI.php';
require_once LIB_PATH . '/booksearcher/CheggAPI.php';
require_once LIB_PATH . '/booksearcher/eCampusAPI.php';
require_once LIB_PATH . '/booksearcher/KnetBooksAPI.php';
require_once LIB_PATH . '/booksearcher/ValoreBooksAPI.php';

class BookSuggestModel extends Model {

	/**
	 * def_group message
	 * @{
	 * a group of message to indicate the result
	 */
	const BOOK_FOUND_SINGLE   = 'Here is the book we think you will need for this class.';
	const BOOK_FOUND_MULTIPLE = 'Here is a list of books we think you will need for this class.';
	const BOOK_FOUND_NONE     = 'We didn\'t find required reading for this class.';
	const API_FAIL            = 'We can\'t find the requested book from online vendors.';
	/**
	 * @} End of "message"
	 */

	// Event message when we can't find the item with fiven isbn
	const INVALID_ISBN = 'Fail to fetch result for - ';

	const QUEUE_NEW      = 'NEW';
	const QUEUE_FAILED   = 'FAILED';
	const QUEUE_STARTED  = 'STARTED';
	const QUEUE_FINISHED = 'FINISHED';

	// book cache expires in 12 hours
	const CACHE_EXPIRE   = 43200;

	/**
	 * Access to book list record
	 */
	protected $book_dao;
	protected $list;
	protected $cache;

	/**
	 * Extend Model::__construct()
	 */
	function __construct($sub_domain) {
		parent::__construct($sub_domain);
		$this->amazonSearch = new AmazonAPI();
		$this->cache = new DBCache();
		$this->book_dao = new BookListDAO($this->institution_db);
		$this->crawler_dao = new BookCrawlerQueueDAO($this->default_db);
	}

	/**
	 * Create cache by compress the book list array into a string.
	 * 
	 * Using gzcompress() is probably not the most optimal to create hash key, Let
	 * me know if you've found a two-way hash that can replace this.
	 */
	protected function encodeBookCacheKey() {
		return gzcompress(json_encode($this->book_dao->list, true));
		return Crypto::Digest(json_encode($this->book_dao->list, true));
	}

	/**
	 * Decompress the cache key to get the book list
	 */
	protected function decodeBookCacheKey($cache_key) {
		$this->book_dao->list = json_decode(gzuncompress($cache_key), true);
		return count($this->book_dao->list) == 1 ? self::BOOK_FOUND_SINGLE : self::BOOK_FOUND_MULTIPLE;
	}

	/**
	 * Generate book list for a given class
	 *
	 * @return string
	 */
	protected function generateBookList($section_id) {
		$has_reading = $this->book_dao->read(array('section_id' => $section_id));
		
		// debug
		// error_log('asdfsadf' . print_r($this->book_dao->list, true));

		if (!$has_reading) {
			return self::BOOK_FOUND_NONE;
		}

		return count($this->book_dao->list) == 1 ? self::BOOK_FOUND_SINGLE : self::BOOK_FOUND_MULTIPLE;
	}

	/**
	 * Process book list and query vendor APIs
	 */
	protected function processBookList() {
		$book_list = $this->book_dao->list;
		$list = array();
		for ($i = 0; $i < count($book_list); $i++) {
			try {
				$isbn = $book_list[$i]['isbn'];
				$this->amazonSearch->searchBookIsbn($isbn);
				$title = (string)$this->amazonSearch->getTitle();
				if (empty($title)) {
					Logger::Write(self::INVALID_ISBN . $isbn);
				} else {
					$image = (string)$this->amazonSearch->getSmallImageLink(); 
					$list[$title] = array(
						'image'  => $image,
						'offers' => $this->getSingleBookRankList($isbn),
					);
				}
			} catch (Exception $e) {
				Logger::Write(__METHOD__ . ' BookSearch API error: ' . $e->getMessage());
			}

			// debug
			// error_log('image - ' . $image);

		}

		if (empty($list)) {
			return array(
				'error' => true,
				'message' => self::API_FAIL,
			);
		}

		return array(
			'success' => true,
			'list'    => $list
		);

	}

	/**
	 * Get book list
	 *
	 * @param $section_id
	 *
	 * @return array
	 *  the returned variable must be of array type!
	 *  On success:
	 *   - success:
	 *   - message:
	 *   - list
	 *  On failure:
	 *   - error:
	 *   - message:
	 */
	public function getBookList($section_id) {
		$message = $this->generateBookList($section_id);

		if ($message == self::BOOK_FOUND_NONE) {
			return array('message' => $message);
		}

		/**
		 * The cache key is the digest of the serialized book list. This way we can
		 * share cache among classes that have the same list of readings.
		 *
		 * Why go through all this trouble? Most classes have the same reading list 
		 * for every section, e.g. CSE 232. However, some classes, especially social
		 * studies, have optional readings for each section, and they are almost never
		 * the same.
		 */
		$cache_key   = $this->encodeBookCacheKey();
		$cache_value = $this->cache->get($cache_key);

		if ($cache_value) {
			$is_queued = $this->crawler_dao->read(array('cache_key' => $cache_key));
			if (!$is_queued) {
				$this->crawler_dao->create(array(
					'cache_key' => $cache_key, 
					'status' => self::QUEUE_NEW
				));
			}
			return json_decode($cache_value, true);
		}


		// debug
		// error_log('book suggest book_list - ' . print_r($book_list, true));

		$result = $this->processBookList();

		if (isset($result['error'])) {
			return $result;
		}

		$result['message'] = $message;

		// debug
		// error_log('book suggest result - ' . print_r($list, true));

		
		$this->cache->set($cache_key, json_encode($result), time() + self::CACHE_EXPIRE);
		$this->crawler_dao->create(array(
			'cache_key' => $cache_key, 
			'status' => self::QUEUE_NEW
		));

		return $result;

	}


	/**
	 * Save information into list->new   ->storeXX->price
	 *                                        ->link
	 *                                  ->storeYY->price
	 *            	                          ->link
	 *                          ->used  ->
	 *                          ->rental->
	 */
	public function getSingleBookRankList($isbn){

		$ecampusSearch = new eCampusAPI($isbn);
		$bookrenterSearch = new BookRenterAPI($isbn);
		$valorebookSearch = new ValoreBooksAPI($isbn);
		$cheggSearch = new CheggAPI($isbn);
		$knetbooksSearch = new KnetBooksAPI($isbn);

		//new
		$newprice = array(
			'Amazon'	=> $this->amazonSearch->getLowestNewPrice(),
			'eCampus'	=> $ecampusSearch->getLowestNewPrice(),
			'BookRenter'	=> $bookrenterSearch->getLowestNewPrice(),
			'ValoreBooks'   => $valorebookSearch->getLowestNewPrice(),
			'AmazonMarket'  => $this->amazonSearch->getMarketPlaceLowestNewPrice()
		);

		$newlink = array(
			'Amazon'	=> (string)$this->amazonSearch->getLowestNewLink(),
			'eCampus'	=> (string)$ecampusSearch->getLowestNewLink(),
			'BookRenter'	=> (string)$bookrenterSearch->getLowestNewLink(),
			'ValoreBooks'   => (string)$valorebookSearch->getLowestNewLink(),
			'AmazonMarket'  => (string)$this->amazonSearch->getLowestNewLink()
		);

		//used
		$usedprice = array(
			'eCampus'	=> $ecampusSearch->getLowestUsedPrice(),
			'BookRenter'	=> $bookrenterSearch->getLowestUsedPrice(),
			'AmazonMarket'  => $this->amazonSearch->getMarketPlaceLowestUsedPrice(),
			'eCampusMArket' => $ecampusSearch->getLowestMarketPlacePrice()
		);
		$usedlink = array(
			'eCampus'	=> (string)$ecampusSearch->getLowestUsedLink(),
			'BookRenter'	=> (string)$bookrenterSearch->getLowestUsedLink(),
			'AmazonMarket'  => (string)$this->amazonSearch->getLowestNewLink(),
			'eCampusMArket' => (string)$ecampusSearch->getLowestMarketPlaceLink()
		);

		//rental
		$rentalprice = array(
			'eCampus'	=> $ecampusSearch->getLowestRentalPrice(),
			'BookRenter'	=> $bookrenterSearch->getLowestRentalPrice(),
			'Chegg'		=> $cheggSearch->getLowestRentalPrice(),
			'KnetBooks'     => $knetbooksSearch->getLowestRentalPrice()
		);
		$rentallink = array(
			'eCampus'	=> (string)$ecampusSearch->getLowestRentalLink(),
			'BookRenter'	=> (string)$bookrenterSearch->getLowestRentalLink(),
                        'Chegg'         => (string)$cheggSearch->getLowestRentalLink(),
			'KnetBooks'     => (string)$knetbooksSearch->getLowestRentalLink()
		);

		//begin sort
		natsort($newprice);
		natsort($usedprice);
		natsort($rentalprice);
	

		//rank new book
		foreach($newprice as $storename => $price){
			$new[$storename] = array(
				'price'  => $price,
				'link'   => (string)$newlink[$storename]
			);
		};

		foreach($usedprice as $storename => $price){
			$used[$storename] = array(
				'price'  => $price,
				'link'   => (string)$usedlink[$storename]
			);
		};

		foreach($rentalprice as $storename => $price){
			$rental[$storename] = array(
				'price'  => $price,
				'link'   => (string)$rentallink[$storename]
			);
		};

		$rankList = array(
			'new'       => $new,
			'used'      => $used,
			'rental'    => $rental,
			'listPrice' => $this->amazonSearch->getListPrice()
		);

		return $rankList;
	}
}
