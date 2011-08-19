<?php
	class ValoreBooksAPI{
		//params
		private	$siteID = "6WFt80";
		private $isbn;

		//initial
		function __construct($input) {
			$this->isbn = $input;
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
			$book = $this->getBuyPrice();
			$price = $book->condition[0]->bestPrice;

			return $price;
		}

		public function getLowestNewLink(){
			$link = $this->linkBuyBook();
			return $link;
		}
	}
