<?php
	class ValoreBooksAPI{
		//params
		private	$siteID = "6WFt80";
		private $isbn;
		private $book;

		//initial
		function __construct($input) {
			global $config;
			$this->isbn = $input;
			$this->siteID = $config->ValoreBooks['siteID'];
			$this->book = $this->getBuyPrice();
		}

		//return xml result
		public function getBuyPrice(){
			$request = "http://www.valorebooks.com/buy-prices/ISBN=" . $this->isbn;
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

		public function getSellPrice(){
			$request = "http://www.valorebooks.com/sell-prices/ISBN=" . $this->isbn;
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

		public function linkBuyBook(){
			$link = "http://www.valorebooks.com/affiliate/buy/siteID=" . $this->siteID . "/ISBN=" . $this->isbn;
			return $link;
		}

		public function linkSellBook(){
			$link = "http://www.valorebooks.com/affiliate/sell/siteID=" . $this->siteID . "/ISBN=" . $this->isbn;
			return $link;
		}



		public function getLowestNewPrice(){
			if(isset($this->book->condition[0]->bestPrice)){
				$price = $this->book->condition[0]->bestPrice;
				$price = substr($price,1,strlen($price));
				return $price;
			}
			return false;
		}

		public function getLowestNewLink(){
                        if(isset($this->book->condition[0]->bestPrice)){
				$link = $this->linkBuyBook();
				return $link;
			}
			return false;
		}
	}
