<?php

	function amazonRequest($params, $accessKeyID, $secretAccessKey, $associateTag){
	
    		// basic params
		$method = "GET";
		$host = "ecs.amazonaws.com";
		$uri = "/onca/xml";
	
	    	// extra params
		$params["Service"] = "AWSECommerceService";
		$params["AWSAccessKeyId"] = $accessKeyID;
		$params["AssociateTag"] = $associateTag;
		// GMT timestamp
		$params["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
		// API version
		$params["Version"] = "2009-03-31";
	
		// sort params
		ksort($params);
	
		// create query
		$query = array();
		foreach ($params as $param=>$value){
	        	$param = str_replace("%7E", "~", rawurlencode($param));
	        	$value = str_replace("%7E", "~", rawurlencode($value));
        		$query[] = $param."=".$value;
		}
		$query = implode("&", $query);
	    
		// create string to sign
		$stringToSign = $method."\n".$host."\n".$uri."\n".$query;
	
		// calculate HMAC with SHA256 and base64-encoding
		$signature = base64_encode(hash_hmac("sha256", $stringToSign, $secretAccessKey, True));
	
		// encode signature
		$signature = str_replace("%7E", "~", rawurlencode($signature));
    
		// create request
	    	$request = "http://".$host.$uri."?".$query."&Signature=".$signature;
	    
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
?>
