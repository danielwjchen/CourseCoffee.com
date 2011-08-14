<?php

	include("AmazonAPI.php");
	
	include("eCampusAPI.php");

	include("BarnesNobleAPI.php");

	include("BookRenterAPI.php");

	include("ValoreBooksAPI.php");

	include("KnetBooksAPI.php");
	
//	include("cjCheggAPI.php");

/*	
	$stime=microtime(true);
	$etime=microtime(true);
	$total=$etime-$stime;
	$str_total = var_export($total, TRUE);  
	if(substr_count($str_total,"E")){  
		$float_total = floatval(substr($str_total,5));  
		$total = $float_total/100000;  
		echo "The total encode time is $total ".'second';  
	}
 */


	$amazonSearch = new AmazonAPI();

	$isbns = array("1848000693","0201558025");

	echo $amazonSearch->buyAllNewBooks($isbns);

/*	$amazonSearch->searchBookIsbn("0415991404");
	echo $amazonSearch->getAuthors();
	echo $amazonSearch->getLowestNewPrice();
	echo $amazonSearch->getMarketPlaceLowestNewPrice();
	echo $amazonSearch->getMarketPlaceLowestUsedPrice();
	echo $amazonSearch->getSmallImageLink();
 */


/*
	$ecampusSearch = new eCampusAPI("0262033844");
	$tt = $ecampusSearch->getLowestNewPrice();
	$tt = $ecampusSearch->getLowestNewLink();
	$tt = $ecampusSearch->getLowestUsedPrice();
	$tt = $ecampusSearch->getLowestUsedLink();
	$tt = $ecampusSearch->getLowestRentalPrice();
	$tt = $ecampusSearch->getLowestRentalLink();
	$tt = $ecampusSearch->getLowestMarketPlacePrice();
	$tt = $ecampusSearch->getLowestMarketPlaceLink();
	echo $tt;
 */

/*
 * valorebooks
 *
	$search = new ValoreBooksAPI("0262033844");
	$tt = $search->getLowestNewPrice();
	$tt = $search->getLowestNewLink();
	echo $tt;
 */

/*
 * bookrenter
 *
	$search = new BookRenterAPI("9780077274306");
	$tt = $search->getLowestNewPrice();
	$tt = $search->getLowestNewLink();
	$tt = $search->getLowestUsedPrice();
	$tt = $search->getLowestUsedLink();
	$tt = $search->getLowestRentalPrice();
	$tt = $search->getLowestRentalLink();
	echo $tt;
 */


/*
 * knetbooks
 */

//	$search = new KnetBooksAPI("0262033848");
//	echo $search->getLowestRentalPrice();
//	echo $search->getLowestRentalLink();



/*
	$search = new BarnesNobleAPI();
	$result = $search->searchBookKeyword("Introduction to Algorithm");
	var_dump($result);
 */

?>

