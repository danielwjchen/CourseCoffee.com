<?php

//KnetBook seems only accept 13 digits ISBN

	class KnetBooksAPI{
		//params
		private	$SASID = "532647";
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
			$request = "http://www.knetbooks.com/botprice.asp?isbn=" . $this->isbn;
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
					return $parsedXml;
				}
			}
		}

		public function linkBookDetail(){
			$link = "http://www.shareasale.com/r.cfm?u=" . $this->SASID . "&b=289670&m=31586&afftrack=&urllink=www.knetbooks.com/bk-detail.asp?isbn=" . $this->isbn;
			return $link;
		}

		public function linkAddRentalToCart(){
			$link = "http://www.shareasale.com/r.cfm?u=" . $this->SASID . "&b=289670&m=31586&afftrack=&urllink=www.knetbooks.com/shopping-cart%3Fitem=" . $this->isbn . "%26newused=r%26qty=1%26action=add";
			return $link;
		}

		public function getLowestRentalPrice(){
			return $this->result;
		}

		public function getLowestRentalLink(){
			return $this->linkBookDetail();
		}
	}

?>
