<?php
	class eCampusAPI{
		//params
		private	$PID;
		private $isbn;
		private $result;

		//initial
		function __construct($input) {
			global $config;
			$this->PID = $config->eCampus['pid'];

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
			$price = substr($this->result->NewPrice, 0, strlen($this->result->NewPrice));
			return $price;
		}

		public function getLowestNewLink(){
			return $this->linkAddNewToCart();
		}

		public function getLowestUsedPrice(){
			$price = substr($this->result->UsedPrice, 0, strlen($this->result->UsedPrice));
			return $price;
		}

		public function getLowestUsedLink(){
			return $this->linkAddUsedToCart();
		}

		public function getLowestRentalPrice(){
			$price = substr($this->result->RentalPrice, 0, strlen($this->result->RentalPrice));
			return $price;
		}

		public function getLowestRentalLink(){
			return $this->linkAddRentalToCart();
		}

		public function getLowestMarketPlacePrice(){
			$price = substr($this->result->MarketPlacePrice, 0, strlen($this->result->MarketPlacePrice));
			return $price;
		}

		public function getLowestMarketPlaceLink(){
			$link = "http://www.dpbolvw.net/click-" . $this->PID . "-10490394?ISBNUPC=" . $this->isbn;
			return $link;
		}

	}


?>
