<?php
	class CheggAPI{
		//cj params
		private	$PID = "5394229";
		private $AID= "10692263";

		//chegg params
		private $KEY = "21ad2adf6a4ed87771729c5e61276de2";
		private $PW = "92137916";
		private $result;
		
		//initial
		function __construct($isbn) {
			$this->isbn = $isbn;
			$this->result = $this->searchBookISBN($isbn);
		}

		//search book detail info
		//return xml result
		public function searchBookISBN($isbn){
			
			$request = "http://api.chegg.com/rent.svc?KEY=" . $this->KEY . "&PW=". $this->PW . "&R=XML&V=2.0&isbn=" . $isbn ."&with_pids=1";
			
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

		public function getLowestRentalPrice(){
			$price = '';

			return $this->result->Items->Item->Terms->Term->Price;
		}

		public function getLowestRentalLink(){
			//build chegg's link
			$cheggcartlink = "http://www.chegg.com/?referrer=CJGATEWAY&PID=". $this->PID . "&AID=" . $this->AID . "&pids=" . $this->result->Items->Item->Terms->Term->Pid;

			$link = "http://www.jdoqocy.com/click-" . $this->PID . "-" . $this->AID . "?URL=" . $cheggcartlink;
			return $link;
		}
	}


?>
