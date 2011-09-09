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
			if(isset($this->result->NewPrice)){
				$price = substr($this->result->NewPrice, 0, strlen($this->result->NewPrice));
				return $price;
			}
			return false;
		}

		public function getLowestNewLink(){
			if(isset($this->result->NewPrice)){
				return $this->linkAddNewToCart();
			}
			return false;
		}

		public function getLowestUsedPrice(){
			if(isset($this->result->UsedPrice)){
				$price = substr($this->result->UsedPrice, 0, strlen($this->result->UsedPrice));
				return $price;
			}
			return false;
		}

		public function getLowestUsedLink(){
			if(isset($this->result->UsedPrice)){
				return $this->linkAddUsedToCart();
			}
			return false;
		}

		public function getLowestRentalPrice(){
			if(isset($this->result->RentalPrice)){
				$price = substr($this->result->RentalPrice, 0, strlen($this->result->RentalPrice));
				return $price;
			}
			return false;
		}

		public function getLowestRentalLink(){
                        if(isset($this->result->RentalPrice)){
				return $this->linkAddRentalToCart();
			}
			return false;
		}

		public function getLowestMarketPlacePrice(){
			if(isset($this->result->MarketPlacePrice)){
				$price = substr($this->result->MarketPlacePrice, 0, strlen($this->result->MarketPlacePrice));
				return $price;
			}
			return false;
		}

		public function getLowestMarketPlaceLink(){
			if(isset($this->result->MarketPlacePrice)){
				$link = "http://www.dpbolvw.net/click-" . $this->PID . "-10490394?ISBNUPC=" . $this->isbn;
				return $link;
			}
			return false;
		}
	}
?>
