<?php

	include("AmazonAPI.php");
	
	include("eCampusAPI.php");

	include("BarnesNobleAPI.php");

	include("BookRenterAPI.php");

	include("ValoreBooksAPI.php");

	include("KnetBooksAPI.php");
	
//	include("cjCheggAPI.php");

	$stime1=microtime(true); //获取程序开始执行的时间
	$etime1=microtime(true);//获取程序执行结束的时间  
	$total1=$etime1-$stime1;   //计算差值  
	$str_total1 = var_export($total1, TRUE);  
	if(substr_count($str_total1,"E")){  
		$float_total1 = floatval(substr($str_total1,5));  
		$total = $float_total1/100000;  
		echo "The total encode time is $total1 ".'second';  
	}

/*
	$amazonSearch = new AmazonAPI();

	$amazonSearch->searchBookIsbn("0415991404");
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
//	$tt = $search->getLowestRentalPrice();
//	$tt = $search->getLowestRentalLink();
	echo $tt;



/*
	$search = new BarnesNobleAPI();
	$result = $search->searchBookKeyword("Introduction to Algorithm");
	var_dump($result);
 */

?>

