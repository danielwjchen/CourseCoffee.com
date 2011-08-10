<?php

require_once LIB_PATH . '/booksearcher/AmazonAPI.php';
require_once LIB_PATH . '/booksearcher/BarnesNobleAPI.php';
require_once LIB_PATH . '/booksearcher/BookRenterAPI.php';
require_once LIB_PATH . '/booksearcher/eCampusAPI.php';
require_once LIB_PATH . '/booksearcher/ValoreBooksAPI.php';

/**
 * @file
 * Suggest list of books for a class
 */
class BookSuggestModel extends Model {

	/**
	 * Access to book list record
	 */
	private $book_list;

	private $list;

	/**
	 * Get book list
	 *
	 * @param $section_id
	 */
	public function getBookList($section_id) {
		$this->book_list = new BookListDAO($this->db);
		$has_no_reading = $this->book_list->read(array('section_id' => $section_id));
		error_log('asdfsadf' . print_r($this->book_list->list, true));

		if ($has_no_reading) {
			//return $this->list;
			return "no reading";
		}
		// the system truncates the list if there is only one record... we need to 
		// restore it back
		if (isset($this->book_list->list['isbn'])) {
			$record[0] = $this->book_list->list;
		} else {
			$record = $this->book_list->list;
		}

		//return $record;
		$this->list = array();	

		//$this->list = array($record[0]['isbn'],$this->getSingleBookRankList($record[0]['isbn']));

		for ($i = 0; $i < count($record); $i++) {
			$isbn = $record[$i]['isbn'];
			array_push($this->list,array($isbn, $this->getSingleBookRankList($isbn)));
		}

		return $this->list;
		//return "have result";
	}


	/*
	 *save information into list->new   ->storeXX->price
	 *                                        ->link
	 *                                  ->storeYY->price
	 *            	                          ->link
	 *                          ->used  ->
	 *                          ->rental->
	 */
	public function getSingleBookRankList($isbn){

		$amazonSearch = new AmazonAPI();
		$amazonSearch->searchBookIsbn($isbn);
		$ecampusSearch = new eCampusAPI($isbn);
		$bookrenterSearch = new BookRenterAPI($isbn);
		$valorebookSearch = new ValoreBooksAPI($isbn);

		//start to querysubstr($ecampusSearch->getLowestNewPrice(),0,strlen($ecampusSearch->getLowestNewPrice())),
		//new
		$newprice = array(
			'Amazon'	=> substr($amazonSearch->getLowestNewPrice(),1,strlen($amazonSearch->getLowestNewPrice())),
			'eCampus'	=> substr($ecampusSearch->getLowestNewPrice(),0,strlen($ecampusSearch->getLowestNewPrice())),
			'BookRenter'	=> substr($bookrenterSearch->getLowestNewPrice(),1,strlen($bookrenterSearch->getLowestNewPrice())),
			'ValoreBooks'   => substr($valorebookSearch->getLowestNewPrice(),1,strlen($valorebookSearch->getLowestNewPrice())),
			'AmazonMarket'  => substr($amazonSearch->getMarketPlaceLowestNewPrice(),1,strlen($amazonSearch->getMarketPlaceLowestNewPrice())),
			'eCampusMArket' => substr($ecampusSearch->getLowestMarketPlacePrice(),0,strlen($ecampusSearch->getLowestMarketPlacePrice()))
		);

		$newlink = array(
			'Amazon'	=> (string)$amazonSearch->getLowestNewLink(),
			'eCampus'	=> (string)$ecampusSearch->getLowestNewLink(),
			'BookRenter'	=> (string)$bookrenterSearch->getLowestNewLink(),
			'ValoreBooks'   => (string)$valorebookSearch->getLowestNewLink(),
			'AmazonMarket'  => (string)$amazonSearch->getLowestNewLink(),
			'eCampusMArket' => (string)$ecampusSearch->getLowestMarketPlaceLink()
		);

		//used
		$usedprice = array(
			'eCampus'	=> substr($ecampusSearch->getLowestUsedPrice(),0,strlen($ecampusSearch->getLowestUsedPrice())),
			'BookRenter'	=> substr($bookrenterSearch->getLowestUsedPrice(),1,strlen($bookrenterSearch->getLowestUsedPrice)),
			'AmazonMarket'  => substr($amazonSearch->getMarketPlaceLowestUsedPrice(),1,strlen($amazonSearch->getMarketPlaceLowestUsedPrice()))		
		);
		$usedlink = array(
			'eCampus'	=> (string)$ecampusSearch->getLowestUsedLink(),
			'BookRenter'	=> (string)$bookrenterSearch->getLowestUsedLink(),
			'AmazonMarket'  => (string)$amazonSearch->getLowestNewLink()
		);

		//rental
		$rentalprice = array(
			'eCampus'	=> substr($ecampusSearch->getLowestRentalPrice(),0,strlen($ecampusSearch->getLowestRentalPrice())),
			'BookRenter'	=> substr($bookrenterSearch->getLowestRentalPrice(),1,strlen($bookrenterSearch->getLowestRentalPrice()))			
		);
		$rentallink = array(
			'eCampus'	=> (string)$ecampusSearch->getLowestRentalLink(),
			'BookRenter'	=> (string)$bookrenterSearch->getLowestRentalLink()
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
			'New'       =>     $new,
			'Used'      =>     $used,
			'Rental'    =>     $rental,
			'Image'     =>     array(
				'SmallURL'     => (string)$amazonSearch->getSmallImageLink(),
				'MediumURL'    => (string)$amazonSearch->getMediumImageLink(),
				'LargeURL'     => (string)$amazonSearch->getLargeImageLink()
			),
			'Title'     =>     (string)$amazonSearch->getTitle(),
			'ListPrice' =>     substr($amazonSearch->getListPrice(),1,strlen($amazonSearch->getListPrice()))
		);

		return $rankList;
	}
}
