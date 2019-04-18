<?php

namespace App\Controllers\Cron;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use App\Support\Rest\Contracts\ApiInterface;

use App\Support\Xml\Contracts\OwnerPopulateInterface;

use App\Support\Logger\Logger;

class OwnerCronController{

	protected $palaceapi;

	protected $ownerPopulate;

	public function __construct(ApiInterface $palaceapi, OwnerPopulateInterface $ownerPopulate){

		$this->palaceapi = $palaceapi;

		$this->ownerPopulate = $ownerPopulate;

	}


	public function index(Request $request, Response $response){

		$ownerObj = $this->palaceapi->setEndPoint('Service.svc/RestService/ViewAllDetailedOwner ')->get();

		$ownerObj = simplexml_load_string($ownerObj);

		$populateReport = $this->ownerPopulate->setOwnerObj($ownerObj)->run()->generateReport();

		// $logger = new Logger('/var/www/html/logs/onwer.txt');

		// if($logger = $logger->initialize()){
		// 	dump($logger->addMessage(serialize($populateReport))->loggerClose());	
		// 	die();	
		// }
		
		}
	
}