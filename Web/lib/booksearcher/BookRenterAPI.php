<?php
	class BookRenterAPI{
		//params
		private	$developKey = "5Y7HAQxTTxjijoV17ThDCzfRxQVze6NY";
		private $apiVersion= "2011-02-01";
		private $result;

		private $isbn;
		private $URLPrefix= "http://www.shareasale.com/r.cfm?u=532647&b=96706&m=14293&urllink=";
		private $link;


		//initial
		function __construct($input) {
			$this->isbn = $input;

			$result = $this->getBookInformationISBN();
		}

		//search book detail info
		//return xml result
		public function getBookInformationISBN(){
			$request = "http://www.bookrenter.com/api/fetch_book_info?developer_key=" . $this->developKey .
				   "&version=" . $this->apiVersion . "&isbn=" . $this->isbn;

			// send request
			$response = @file_get_contents($request);
			if ($response === False){
				return False;
			}
			else{
				// parse XML to SimpleXMLObject
				$parsedXml = simplexml_load_string($response);
				if ($parsedXml === False){
					return False; // no xml
				}
				else{

					$this->result = $parsedXml;
					$bookURL = substr($this->result->book->book_url, 7, strlen($this->result->book->book_url));
					$this->link = $this->URLPrefix . $bookURL;
					return $parsedXml;
				}
			}
		}

		public function getBookInformationKeyword($keyword){

			$request = "http://www.bookrenter.com/api/search_book_infos?developer_key=" . $this->developKey .
			           "&book_details=y&version=" . $this->apiVersion . "&term=" . $keyword . "items_per_page=3";

			// send request
			$response = @file_get_contents($request);

			if ($response === False){
				return False;
			}
			else{
				// parse XML to SimpleXMLObject
				$parsedXml = simplexml_load_string($response);
				if ($parsedXml === False){
					return False; // no xmlwe
				}
				else{
					$this->result = $parsedXml;
					$bookURL = substr($this->result->book->book_url, 7, strlen($this->result->book->book_url));
					$this->link = $this->URLPrefix . $bookURL;
					return $parsedXml;
				}
			}
		}


		public function buyBackBookISBN(){

			$request = "   http://www.bookrenter.com/api/get_buybacks?developer_key" . $this->developKey.
		        	   "&version=" . $this->apiVersion . "&isbns=" . $this->isbn;

			// send request
			$response = @file_get_contents($request);

			if ($response === False){
				return False;
			}
			else{
				// parse XML to SimpleXMLObject
				$parsedXml = simplexml_load_string($response);
				if ($parsedXml === False){
					return False; // no xmlwe
				}
				else{
					$this->result = $parsedXml;
					$bookURL = substr($this->result->book->book_url, 7, strlen($this->result->book->book_url));
					$this->link = $this->URLPrefix . $bookURL;
					return $parsedXml;
				}
			}
		}



		public function getLowestNewPrice(){
			$price = '';
			if (!is_array($this->result->book->prices->purchase_price)) {
				return $price;
			}

			foreach($this->result->book->prices->purchase_price as $purchase ){
				if($purchase->attributes() == "new") $price = $purchase;
			};

			return $price;
		}

		public function getLowestNewLink(){
			return $this->link;
		}

		public function getLowestUsedPrice(){
			$price = '';
			if (!is_array($this->result->book->prices->purchase_price)) {
				return $price;
			}

			foreach($this->result->book->prices->purchase_price as $purchase ){
				if($purchase->attributes() == "used") $price = $purchase;
			};

			return $price;
		}

		public function getLowestUsedLink(){
			return $this->link;
		}


		public function getLowestRentalPrice(){
			$price = '';
			if (!is_array($this->result->book->prices->rental_price)) {
				return $price;
			}

			foreach($this->result->book->prices->rental_price as $rental ){
				if($rental->attributes() == "90") $price = $rental;
			};

			return $price;
		}

		public function getLowestRentalLink(){
			return $this->link;
		}



	}


?>
