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
	const BOOK_FOUND_SINGLE   = 'Here is the book we think you might need for this class.';
	const BOOK_FOUND_MULTIPLE = 'Here is a list of books we think you might need for this class.';
	const BOOK_FOUND_NONE     = 'We didn\'t find required reading for this class.';
	/**
	 * @} End of "message"
	 */

	/**
	 * Access to book list record
	 */
	private $book_list;
	private $list;
	private $cache;

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->amazonSearch = new AmazonAPI();
		$this->cache = new DBCache();
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
		$this->book_list = new BookListDAO($this->db);
		$has_reading = $this->book_list->read(array('section_id' => $section_id));
		
		// debug
		// error_log('asdfsadf' . print_r($this->book_list->list, true));

		if (!$has_reading) {
			//return $this->list;
			return array('message' => self::BOOK_FOUND_NONE);
		}

		$cacheKey = 'bookList' . $section_id;

		$cacheValue = $this->cache->get($cacheKey);
		if ($cacheValue) {
			return json_decode($cacheValue['value'], true);
		}

		// the system truncates the list if there is only one record... we need to 
		// restore it back
		$record  = array();
		$message = '';
		if (isset($this->book_list->list['isbn'])) {
			$message   = self::BOOK_FOUND_SINGLE;
			$record[0] = $this->book_list->list;
		} else {
			$message = self::BOOK_FOUND_MULTIPLE;
			$record  = $this->book_list->list;
		}

		// debug
		// error_log('book suggest record - ' . print_r($record, true));

		$this->list = array();	


		try {
			for ($i = 0; $i < count($record); $i++) {
				$isbn = $record[$i]['isbn'];
				$this->amazonSearch->searchBookIsbn($isbn);
				$title = (string)$this->amazonSearch->getTitle();
				$image = (string)$this->amazonSearch->getSmallImageLink(); 

				// debug
				// error_log('image - ' . $image);

				$this->list[$title] = array(
					'image'  => $image,
					'offers' => $this->getSingleBookRankList($isbn),
				);
			}
		} catch (Exception $e) {
			Logger::Write($e->getMessage());
		}

		// debug
		// error_log('book suggest result - ' . print_r($this->list, true));

		$result = array(
			'success' => true,
			'message' => $message,
			'list'    => $this->list
		);
		
		$this->cache->set($cacheKey, json_encode($result));

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
