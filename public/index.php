<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require __DIR__ . '/../vendor/autoload.php';

// use GuzzleHttp\Client;

// $client = new Client([

// 	'base_uri' => 'https://serviceapi.realbaselive.com/',

// 	'timeout'  => 6.0

// ]);

// $response = $client->request('GET',
// 	'Service.svc/RestService/ViewAllDetailedProperty',

// 		[
// 			'auth' => ['development@verum.co.nz', 'V3ruUm309805'],

// 			'headers' => [

// 				'content-type' => 'application/xml',

// 				'Accept' => 'application/xml'

// 			]

// 		]

// 	);
// $body = $response->getBody();

// $contents = (string) $body;

// $contents = simplexml_load_string($contents);

// foreach ($contents->ViewAllDetailedProperty as $key => $value) {
// 	echo "<pre>";
// 	var_dump($value);
// 	echo "<pre>";
// }

// echo "<pre>";
// var_dump($contents);
// echo "</pre>";
require __DIR__ . '/../bootstrap/app.php';

$app->run();