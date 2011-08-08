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

	/**
	 * Get book list
	 *
	 * @param $section_id
	 */
	public function getBookList($section_id) {
		$this->book_list = new BookListDAO($this->db);
		$has_no_reading = $this->book_list->read(array('section_id' => $section_id));
		error_log('asdfsadf' . print_r($this->book_list->list, true));
		$list = array();
		$amazonSearch = new AmazonAPI();
		if ($has_no_reading) {
			return array(
				'list' => null
			);
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
			$amazonSearch->searchBookIsbn($isbn);
			$list[$i]['image']  = $amazonSearch->getSmallImageLink();
			$list[$i]['title']  = $amazonSearch->getTitle();
			$list[$i]['amazon'] = array(
				'market_new'  => $amazonSearch->getMarketPlaceLowestNewPrice(),
				'market_used' => $amazonSearch->getMarketPlaceLowestUsedPrice(),
				'new'         => $amazonSearch->getLowestNewPrice(),
			);
			$ecampusSearch = new eCampusAPI($isbn);
			$list[$i]['eCampus'] = array(
				'new'         => $ecampusSearch->getLowestNewPrice(),
				'new_link'    => $ecampusSearch->getLowestNewLink(),
				'used'        => $ecampusSearch->getLowestUsedPrice(),
				'used_link'   => $ecampusSearch->getLowestUsedLink(),
				'rental'      => $ecampusSearch->getLowestRentalPrice(),
				'rental_link' => $ecampusSearch->getLowestRentalLink(),
				'market'      => $ecampusSearch->getLowestMarketPlacePrice(),
				'market_link' => $ecampusSearch->getLowestMarketPlaceLink(),
			);

		}

		return array(
			'list' => $list
		);

	}

}
