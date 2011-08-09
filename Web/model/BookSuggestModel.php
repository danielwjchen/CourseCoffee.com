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
			return $this->list;
		}
		// the system truncates the list if there is only one record... we need to 
		// restore it back
		if (isset($this->book_list->list['isbn'])) {
			$record[0] = $this->book_list->list;
		} else {
			$record = $this->book_list->list;
		}
	
		for ($i = 0; $i < count($record); $i++) {
			$isbn = $record[$i]['isbn'];

			$this->list = array(
				$isbn   =>   $this->getSingleBookRankList($isbn)
			);

		}
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

		//start to query
		//new
		$newprice = array(
			'Amazon'	=> $amazonSearch->getLowestNewPrice(),
			'eCampus'	=> $ecampusSearch->getLowestNewPrice(),
			'BookRenter'	=> $bookrenterSearch->getLowestNewPrice(),
			'ValoreBooks'   => $valorebookSearch->getLowestNewPrice(),
			'AmazonMarket'  => $amazonSearch->getMarketPlaceLowestNewPrice(),
			'eCampusMArket' => $ecampusSearch->getLowestMarketPlacePrice()
		);
		$newlink = array(
			'Amazon'	=> $amazonSearch->getLowestNewLink(),
			'eCampus'	=> $ecampusSearch->getLowestNewLink(),
			'BookRenter'	=> $bookrenterSearch->getLowestNewLink(),
			'ValoreBooks'   => $valorebookSearch->getLowestNewLink(),
			'AmazonMarket'  => $amazonSearch->getLowestNewLink(),
			'eCampusMArket' => $ecampusSearch->getLowestMarketPlaceLink()
		);

		//used
		$usedprice = array(
			'eCampus'	=> $ecampusSearch->getLowestUsedPrice(),
			'BookRenter'	=> $bookrenterSearch->getLowestUsedPrice(),
			'AmazonMarket'  => $amazonSearch->getMarketPlaceLowestUsedPrice()
		)
		$usedlink = array(
			'eCampus'	=> $ecampusSearch->getLowestUsedLink(),
			'BookRenter'	=> $bookrenterSearch->getLowestUsedLink(),
			'AmazonMarket'  => $amazonSearch->getLowestNewLink()
		);

		//rental
		$rentalprice = array(
			'eCampus'	=> $ecampusSearch->getLowestRentalPrice(),
			'BookRenter'	=> $bookrenterSearch->getLowestRentalPrice()			
		);
		$rentallink = array(
			'eCampus'	=> $ecampusSearch->getLowestRentalLink(),
			'BookRenter'	=> $bookrenterSearch->getLowestRentalLink()
		);

		//begin sort
		asort($newprice);
		asort($usedprice);
		asort($rentalprice);


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
