<?php
/**
 * @file
 * Suggest list of books for a class
 */

require_once LIB_PATH . '/booksearcher/AmazonAPI.php';
require_once LIB_PATH . '/booksearcher/BarnesNobleAPI.php';
require_once LIB_PATH . '/booksearcher/BookRenterAPI.php';
require_once LIB_PATH . '/booksearcher/eCampusAPI.php';
require_once LIB_PATH . '/booksearcher/ValoreBooksAPI.php';

class BookSuggestModel extends Model {

	/**
	 * Access to book list record
	 */
	private $book_list;

	private $list;
	private $amazonSearch;

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->amazonSearch = new AmazonAPI();
	}


	/**
	 * Get book list
	 *
	 * @param $section_id
	 *
	 * @return array
	 *  the returned variable must be of array type!
	 */
	public function getBookList($section_id) {
		$this->book_list = new BookListDAO($this->db);
		$has_reading = $this->book_list->read(array('section_id' => $section_id));
		
		// debug
		// error_log('asdfsadf' . print_r($this->book_list->list, true));

		if (!$has_reading) {
			//return $this->list;
			return array('message' => 'no reading');
		}
		// the system truncates the list if there is only one record... we need to 
		// restore it back
		$record = array();
		if (isset($this->book_list->list['isbn'])) {
			$record[0] = $this->book_list->list;
		} else {
			$record = $this->book_list->list;
		}

		// debug
		// error_log('book suggest record - ' . print_r($record, true));

		$this->list = array();	

		//$this->list = array($record[0]['isbn'],$this->getSingleBookRankList($record[0]['isbn']));

		for ($i = 0; $i < count($record); $i++) {
			$isbn = $record[$i]['isbn'];
			$this->amazonSearch->searchBookIsbn($isbn);
			$title = (string)$this->amazonSearch->getTitle();
			$image = (string)$this->amazonSearch->getSmallImageLink(); 

			$this->list[] = array(
				$title => array(
					'image'  => $image,
					'offers' => $this->getSingleBookRankList($isbn),
				),
			);
		}

		// debug
		// error_log('book suggest result - ' . print_r($this->list, true));

		return $this->list;
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

		//start to querysubstr($ecampusSearch->getLowestNewPrice(),0,strlen($ecampusSearch->getLowestNewPrice())),
		//new
		$newprice = array(
			'Amazon'	=> substr($this->amazonSearch->getLowestNewPrice(),1,strlen($this->amazonSearch->getLowestNewPrice())),
			'eCampus'	=> substr($ecampusSearch->getLowestNewPrice(),0,strlen($ecampusSearch->getLowestNewPrice())),
			'BookRenter'	=> substr($bookrenterSearch->getLowestNewPrice(),1,strlen($bookrenterSearch->getLowestNewPrice())),
			'ValoreBooks'   => substr($valorebookSearch->getLowestNewPrice(),1,strlen($valorebookSearch->getLowestNewPrice())),
			'AmazonMarket'  => substr($this->amazonSearch->getMarketPlaceLowestNewPrice(),1,strlen($this->amazonSearch->getMarketPlaceLowestNewPrice())),
			'eCampusMArket' => substr($ecampusSearch->getLowestMarketPlacePrice(),0,strlen($ecampusSearch->getLowestMarketPlacePrice()))
		);

		$newlink = array(
			'Amazon'	=> $this->amazonSearch->getLowestNewLink(),
			'eCampus'	=> $ecampusSearch->getLowestNewLink(),
			'BookRenter'	=> $bookrenterSearch->getLowestNewLink(),
			'ValoreBooks'   => $valorebookSearch->getLowestNewLink(),
			'AmazonMarket'  => $this->amazonSearch->getLowestNewLink(),
			'eCampusMArket' => $ecampusSearch->getLowestMarketPlaceLink()
		);

		//used
		$usedprice = array(
			'eCampus'	=> substr($ecampusSearch->getLowestUsedPrice(),0,strlen($ecampusSearch->getLowestUsedPrice())),
			'BookRenter'	=> substr($bookrenterSearch->getLowestUsedPrice(),1,strlen($bookrenterSearch->getLowestUsedPrice)),
			'AmazonMarket'  => substr($this->amazonSearch->getMarketPlaceLowestUsedPrice(),1,strlen($this->amazonSearch->getMarketPlaceLowestUsedPrice()))		
		);
		$usedlink = array(
			'eCampus'	=> $ecampusSearch->getLowestUsedLink(),
			'BookRenter'	=> $bookrenterSearch->getLowestUsedLink(),
			'AmazonMarket'  => $this->amazonSearch->getLowestNewLink()
		);

		//rental
		$rentalprice = array(
			'eCampus'	=> substr($ecampusSearch->getLowestRentalPrice(),0,strlen($ecampusSearch->getLowestRentalPrice())),
			'BookRenter'	=> substr($bookrenterSearch->getLowestRentalPrice(),1,strlen($bookrenterSearch->getLowestRentalPrice()))			
		);
		$rentallink = array(
			'eCampus'	=> $ecampusSearch->getLowestRentalLink(),
			'BookRenter'	=> $bookrenterSearch->getLowestRentalLink()
		);

		//begin sort
		natsort($newprice);
		natsort($usedprice);
		natsort($rentalprice);

		//return $newprice;
		return $newprice;	

		//rank new book
		foreach($newprice as $storename => $price){
			$new[$storename] = array(
				'price'  => $price,
				'link'   => $newlink[$storename]
			);
		};

		foreach($usedprice as $storename => $price){
			$used[$storename] = array(
				'price'  => $price,
				'link'   => $usedlink[$storename]
			);
		};

		foreach($rentalprice as $storename => $price){
			$rental[$storename] = array(
				'price'  => $price,
				'link'   => $rentallink[$storename]
			);
		};

		$rankList = array(
			'New'      =>     $new,
			'Used'     =>     $used,
			'Rental'   =>     $rental
		);

		return $rankList;
	}
}
