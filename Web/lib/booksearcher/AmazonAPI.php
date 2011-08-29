<?php
	require_once 'AmazonRequest.php';
	
	class AmazonAPI{
		// Amazon Access Key Id and Secret Access Key
/*
		private $accessKeyId  = "AKIAJ5FH5BHY2U5VEQRA";
		private $secretAccessKey  = "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";
		private $associateTag  = "msco04-20";
*/
                private $accessKeyId;
                private $secretAccessKey;
                private $associateTag;


		private $book;

		private $cartURL;

		function __construct(){
			global $config; 
			$this->accessKeyId = $config->Amazon['accessKeyId'];
			$this->secretAccessKey = $config->Amazon['secretAccessKey'];
			$this->associateTag = $config->Amazon['associateTag'];
		}

		//SearchIndex Value is listed on 
		//http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/APPNDX_SearchIndexValues.html
        
		//Check the xml received from Amazon
		//exception if fail
		private function checkXmlResponse($response){
			if ($response === False){
				throw new Exception("Fail connect to Amazon");
			}
           		else{
				if (isset($response->Items->Item[0]->ItemAttributes->Title)){

					//store the result
					$this->result = $response;
					$this->book = $this->result->Items->Item;

					return ($response);
				}
				else{
					throw new Exception("Invalid xml response.");
				}
			}
		}
        
		//query Amazon with the params
		//simpleXmlObject response
		private function queryAmazon($params){
			return amazonRequest($params, $this->accessKeyId, $this->secretAccessKey, $this->associateTag);
		}

		//Results searched by keywords
		public function searchBookKeyword($keyword){
			$params = array(
				"Operation"   => "ItemSearch",
				"Keywords"    => $keyword,
				"SearchIndex" => "Books",
				"ResponseGroup" => "ItemAttributes,Images,Offers"
			);

			$xmlResponse = $this->queryAmazon($params);

			return $this->checkXmlResponse($xmlResponse);
		}

		//Results searched by title
		public function searchBookTitle($title){
			$params = array(
				"Operation"   => "ItemSearch",
				"Title"    => $title,
				"SearchIndex" => "Books",
				"ResponseGroup" => "ItemAttributes,Images,Offers"
			);

			$xmlResponse = $this->queryAmazon($params);

			return $this->checkXmlResponse($xmlResponse);
		}

		//Results searched by author
		public function searchBookAuthor($author){
			$params = array(
				"Operation"   => "ItemSearch",
				"Author"    => $author,
				"SearchIndex" => "Books",
				"ResponseGroup" => "ItemAttributes,Images,Offers"
			);

			$xmlResponse = $this->queryAmazon($params);

			return $this->checkXmlResponse($xmlResponse);
		}
   
        
		//Results searched by asin
		public function searchBookAsin($asin){
			$params = array(
				"Operation"     => "ItemLookup",
				"ItemId"        => $asin,
				"ResponseGroup" => "ItemAttributes,Images,Offers"
			);

			$xmlResponse = $this->queryAmazon($params);

			return $this->checkXmlResponse($xmlResponse);
		}


		//Results searched by isbn
		public function searchBookIsbn($isbn){
			$params = array(
				"Operation"     => "ItemLookup",
				"IdType"        => "ISBN",
				"ItemId"        => $isbn,
				"SearchIndex"   => "Books",
				"ResponseGroup" => "ItemAttributes,Images,Offers"
			);


			$xmlResponse = $this->queryAmazon($params);

			return $this->checkXmlResponse($xmlResponse);
		}

		public function getTitle(){
			return $this->book->ItemAttributes->Title;
		}

		public function getISBN(){
			return $this->book->ItemAttributes->ISBN;
		}

		public function getASIN(){
			return $this->book->ASIN;
		}

		public function getAuthors(){
			foreach($this->book->ItemAttributes->Author as $Author){
				if (isset($Authors)){
					$Authors = $Authors . "   ,   " . $Author;
				}else{
					$Authors = $Author;
				}
			}

			foreach($this->book->ItemAttributes->Creator as $Author){
				if (isset($Authors)){
					$Authors = $Authors . "   ,   " . $Author;
				}else{
					$Authors = $Author;
				}
			}
			return $Authors;
		}

		public function getSmallImageLink(){
			return  $this->book->SmallImage->URL;
		}

		public function getMediumImageLink(){
			return  $this->book->MediumImage->URL;
		}

		public function getLargeImageLink(){
			return  $this->book->LargeImage->URL;
		}

		public function getDetailLink(){
			return $this->book->DetailPageURL;
		}

		public function getListPrice(){
			$price = substr($this->book->ItemAttributes->ListPrice->FormattedPrice,1,strlen($this->book->ItemAttributes->ListPrice->FormattedPrice));
			return $price;
		}

		public function getLowestNewPrice(){
			$price = substr($this->book->Offers->Offer->OfferListing->Price->FormattedPrice,1,strlen($this->book->Offers->Offer->OfferListing->Price->FormattedPrice));
			return $price;
		}

		public function getLowestNewLink(){
			return $this->getDetailLink();
		}

		public function getMarketPlaceLowestNewPrice(){
			$price = substr($this->book->OfferSummary->LowestNewPrice->FormattedPrice,1,strlen($this->book->OfferSummary->LowestNewPrice->FormattedPrice));
			return $price;
		}

		public function getMarketPlaceLowestUsedPrice(){
			$price = substr($this->book->OfferSummary->LowestUsedPrice->FormattedPrice,1,strlen($this->book->OfferSummary->LowestUsedPrice->FormattedPrice));
			return $price;
		}

		//sth wrong with this function
		public function getAllOfferPriceLink(){
			foreach ($this->book->itemlinks as $link){
				if($link->description == "All Offers"){
					return $link->url;
				};
			};
		}


		public function createCart($isbns){
			$params = array(
				"Operation"   => "CartCreate",
			);

			for($i=0; $i<count($isbns); $i++){
				$params["Item." . ($i+1) . ".ASIN"] = $isbns[$i];
				$params["Item." . ($i+1) . ".Quantity"] = 1;
			}

			$xmlResponse = $this->queryAmazon($params);

			$this->cartURL = $xmlResponse->Cart->PurchaseURL;

			return $this->cartURL;
		}

	}

?>
