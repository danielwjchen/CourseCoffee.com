<?php
	class BarnesNobleAPI{
		//params
		private	$affiliateToken = "e3df218443c206f8e431089dbab623830c4a8b3c907b8bcfd8142184d2fc9210";
		private $merchantID= "36889";

		private $result;

		//search book detail info
		//return xml result
		public function searchBookKeyword($keyword){
			$request = "http://productsearch.linksynergy.com/productsearch?token=" . $this->affiliateToken . "&keyword=" . $keyword . "&cat=Books" . "&MaxResults=20&pagenumber=1&mid=" . $this->merchantID. "&sort=retailprice&sorttype=asc";
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
					return $parsedXml;
				}
			}
		}



		public function linkBookDetail(){
			$link = "http://www.dpbolvw.net/click-" . $this->PID . "-10507377?Keyword=" . "good";
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
	}


?>
