<?php

namespace App\Controllers\Cron;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use App\Support\Rest\Contracts\ApiInterface;

use App\Support\Xml\Contracts\PropertyPopulateInterface;

class PropertyCronController{

	protected $palaceapi;

	protected $propertyPopulate;

	public function __construct(ApiInterface $palaceapi, PropertyPopulateInterface $propertyPopulate){

		$this->palaceapi = $palaceapi;

		$this->propertyPopulate = $propertyPopulate;

	}


	public function index(Request $request, Response $response){

		$propertyObj = $this->palaceapi->setEndPoint('Service.svc/RestService/ViewAllDetailedProperty')->get();

		$propertyObj = simplexml_load_string($propertyObj);

		$populateReport = $this->propertyPopulate->setPropertyObj($propertyObj)->run()->generateReport();

	}
	
}