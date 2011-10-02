<?php
/**
 * @file
 * Test script for Emailer class
 */
include __DIR__ . '/includes/bootstrap.php';
include __DIR__ . '/config.php';

$from = array(
	'name'    => 'Daniel Chen',
	'address' => 'daniel@coursecoffee.com',
);
$to = array(
	'name'    => 'Daniel Chen',
	'address' => 'chendan4@msu.edu',
);
$subject = 'test message again';
$message['html'] = <<<HTML
<html>
	<head>
		<title>test message</title>
		<meta HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html;cjarset=utf-8" />
	</head>
	<body>
		<h1>This is a test message</h1>
		<img src="http://daniel.coursecoffee.net/images/logo.png" />
		<p>Does this work?</p>
		<p>But I am not.... </p>
	</body>
</html>
HTML;
//Emailer::Queue($from, $to, $subject, $message);
Emailer::ProcessQueue();
