<?php
	class BookRenterAPI{
		//params
		private	$developKey = "5Y7HAQxTTxjijoV17ThDCzfRxQVze6NY";
		private $apiVersion= "2011-02-01";
		private $result;

		private $isbn;
		private $link = "http://www.shareasale.com/r.cfm?u=532647&b=96706&m=14293&urllink=www.bookrenter.com%2Fmodern-american-women-writers-0020820259-9780020820253";


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
			echo $response;
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
					return $parsedXml;
				}
			}
		}



		public function getLowestNewPrice(){
			foreach($this->result->book->prices->purchase_price as $purchase ){
				if($purchase->attributes() == "new") $price = $purchase;
			};

			return $price;
		}

		public function getLowestNewLink(){
			return $this->link;
		}

		public function getLowestUsedPrice(){
			foreach($this->result->book->prices->purchase_price as $purchase ){
				if($purchase->attributes() == "used") $price = $purchase;
			};

			return $price;
		}

		public function getLowestUsedLink(){
			return $this->link;
		}


		public function getLowestRentalPrice(){
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
