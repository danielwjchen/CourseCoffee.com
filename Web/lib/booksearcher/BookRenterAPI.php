<?php
	class BookRenterAPI{
		//params
		private	$developKey;
		private $apiVersion;
		private $uid;
		private $term;
		private $result;

		private $isbn;
		private $URLPrefix;
		private $link;


		//initial
		function __construct($input) {
			global $config;
			$this->uid = $config->BookRenter['uid'];
                        $this->developKey = $config->BookRenter['developKey'];
			$this->apiVersion = $config->BookRenter['apiVersion'];
			$this->term = $config->BookRenter['term'];
			$this->URLPrefix = 'http://www.shareasale.com/r.cfm?u='.$this->uid.'&b=96706&m=14293&urllink=';

			$this->isbn = $input;

			$this->result = $this->getBookInformationISBN();
			$this->link = $this->URLPrefix . substr($this->result->book->book_url, 7, strlen($this->result->book->book_url));
		}

		//search book detail info
		//return xml result
		public function getBookInformationISBN(){
			$request = "http://www.bookrenter.com/api/fetch_book_info?developer_key=" . $this->developKey .
				   "&version=" . $this->apiVersion . "&isbn=" . $this->isbn;

			// send request
			$response = @file_get_contents($request);
			if ($response === false){
				return false;
			}
			else{
				// parse XML to SimpleXMLObject
				$parsedXml = simplexml_load_string($response);
				if ($parsedXml === false){
					return False; // no xml
				}
				else{
					return $parsedXml;
				}
			}
		}

		public function getBookInformationKeyword($keyword){

			$request = "http://www.bookrenter.com/api/search_book_infos?developer_key=" . $this->developKey .
			           "&book_details=y&version=" . $this->apiVersion . "&term=" . $keyword . "items_per_page=3";

			// send request
			$response = @file_get_contents($request);

			if ($response === false){
				return false;
			}
			else{
				// parse XML to SimpleXMLObject
				$parsedXml = simplexml_load_string($response);
				if ($parsedXml === false){
					return false; // no xml
				}
				else{
					return $parsedXml;
				}
			}
		}


		public function buyBackBookISBN(){
			$request = "http://www.bookrenter.com/api/get_buybacks?developer_key" . $this->developKey.
		        	   "&version=" . $this->apiVersion . "&isbns=" . $this->isbn;

			// send request
			$response = @file_get_contents($request);

			if ($response === false){
				return false;
			}
			else{
				// parse XML to SimpleXMLObject
				$parsedXml = simplexml_load_string($response);
				if ($parsedXml === False){
					return false; // no xml
				}
				else{
					return $parsedXml;
				}
			}
		}

		public function getLowestNewPrice(){
			if (isset($this->result->book->prices->purchase_price)) {
				foreach($this->result->book->prices->purchase_price as $purchase ){
					if($purchase->attributes() == "new") $price = $purchase;
				};
				$price = substr($price,1,strlen($price));
				return $price;
			}

			return false;
		}

		public function getLowestNewLink(){
                        if (isset($this->result->book->prices->purchase_price)) {
                                return $this->link;
                        }
			return false;
		}

		public function getLowestUsedPrice(){
                        if (isset($this->result->book->prices->purchase_price)) {
                                foreach($this->result->book->prices->purchase_price as $purchase ){
                                        if($purchase->attributes() == "used") $price = $purchase;
                                };
                                $price = substr($price,1,strlen($price));
                                return $price;
                        }

                        return false;
		}

		public function getLowestUsedLink(){
		        if (isset($this->result->book->prices->purchase_price)) {
                                return $this->link;
                        }
                        return false;
		}


		public function getLowestRentalPrice(){
                        if (isset($this->result->book->prices->rental_price)) {
                                foreach($this->result->book->prices->rental_price as $rental ){
                            	    if($rental->attributes() == $this->term) $price = $rental;
                        	};

               		        $price = substr($price,1,strlen($price));
                        	return $price;
                        }

                        return false;
		}

		public function getLowestRentalLink(){
                        if (isset($this->result->book->prices->rental_price)) {
				return $this->link;
			}
			return false;
		}
	}

?>
