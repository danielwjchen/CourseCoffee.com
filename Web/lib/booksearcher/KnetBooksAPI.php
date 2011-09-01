<?php

//KnetBook only accept 13 digits ISBN

	class KnetBooksAPI{
		//params
		private	$SASID;
		private $isbn;
		private $result;

		//initial
		function __construct($isbn) {
			global $config;
			$this->SASID = $config->KnetBooks['sasid'];

			$this->isbn = $isbn;
			$this->result = $this->searchBookInfo();
		}

		//change 10 digits isbn to 13 digits
		public function to13($isbn){
			$isbn = str_replace('-', '', $isbn);  
			if(!preg_match('/^\d+x?$/i', $isbn)){  
				return null;  
			}
			if(strlen($isbn) == 13){
				return $isbn;
			}  
			
			$sum = 0;  
			$num = '978' . substr($isbn, 0, 9);  
			for($i = 0; $i < 12; $i++){  
				$n = $num[$i];  
				if(($i + 1) % 2 == 0){  
					$sum += $n * 3;  
				}else{  
					$sum += $n;  
				}  
			}
			$m = $sum % 10;  
			$check = 10 - $m;  
			
			return $num . $check;
		}  
		

		//search book detail info
		//return xml result
		public function searchBookInfo(){
			$request = "http://www.knetbooks.com/botprice.asp?isbn=" . $this->to13($this->isbn);
			// send request
			$response = @file_get_contents($request);

			return $response;
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
			$pos_beg = strpos($this->result,"Rental Price");
			$pos_end = strpos($this->result,"Rental Duration");
			if($pos_beg){
				$tmp = substr($this->result, $pos_beg, ($pos_end - $pos_beg));
				$pos_beg = strpos($tmp, '$');
				$pos_end = strpos($tmp, '<');
				$price = substr($tmp,$pos_beg+1, $pos_end);
				return $price;
			}
			return false;
		}

		public function getLowestRentalLink(){
			if($this->result != 'Not Found'){
				return $this->linkBookDetail();
			}
			return false;
		}
	}

?>
