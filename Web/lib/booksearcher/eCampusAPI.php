<?php
	class eCampusAPI{
		//params
		private	$PID = "5394229";
		private $isbn;
		private $result;

		//initial
		function __construct($input) {
			$this->isbn = $input;
			$this->result = $this->searchBookInfo();
		}

		//search book detail info
		//return xml result
		public function searchBookInfo(){
			$request = "http://www.ecampus.com/botpricexml.asp?isbn=" . $this->isbn;
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
					return $parsedXml;
				}
			}
		}

		public function linkBookDetail(){
			$link = "http://www.dpbolvw.net/click-" . $this->PID . "-5029466?ISBN=" . $this->isbn;
			return $link;
		}

		public function linkAddNewToCart(){
			$link = "http://www.tkqlhce.com/click-" . $this->PID . "-10641046?ISBN=" . $this->isbn;
			return $link;
		}

		public function linkAddUsedToCart(){
			$link = "http://www.kqzyfj.com/click-" . $this->PID . "-10569291?ISBN=" . $this->isbn;
			return $link;
		}

		public function linkAddRentalToCart(){
			$link = "http://www.dpbolvw.net/click-" . $this->PID . "-10835786?ISBN=" . $this->isbn;
			return $link;
		}


		public function getLowestNewPrice(){
			return $this->result->NewPrice;
		}

		public function getLowestNewLink(){
			return $this->linkAddNewToCart();
		}

		public function getLowestUsedPrice(){
			return $this->result->UsedPrice;
		}

		public function getLowestUsedLink(){
			return $this->linkAddUsedToCart();
		}

		public function getLowestRentalPrice(){
			return $this->result->RentalPrice;
		}

		public function getLowestRentalLink(){
			return $this->linkAddRentalToCart();
		}

		public function getLowestMarketPlacePrice(){
			return $this->result->RentalPrice;
		}

		public function getLowestMarketPlaceLink(){
			$link = "http://www.dpbolvw.net/click-" . $this->PID . "-10490394?ISBNUPC=" . $this->isbn;
			return $link;
		}

	}


?>
