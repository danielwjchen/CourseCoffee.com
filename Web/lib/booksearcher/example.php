<?php

	include("AmazonAPI.php");
	
	include("eCampusAPI.php");

	include("BarnesNobleAPI.php");

	include("BookRenterAPI.php");

	include("ValoreBooksAPI.php");
	
//	include("cjCheggAPI.php");

	$amazonSearch = new AmazonAPI();
	$amazonSearch->searchBookKeyword("Introduction to Algorithms");

	echo $amazonSearch->getLowestNewPrice();
	echo $amazonSearch->getMarketPlaceLowestNewPrice();
	echo $amazonSearch->getMarketPlaceLowestUsedPrice();
	echo $amazonSearch->getSmallImageLink();

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
	$search = new BarnesNobleAPI();
	$result = $search->searchBookKeyword("Introduction to Algorithm");
	var_dump($result);
 */

?>

