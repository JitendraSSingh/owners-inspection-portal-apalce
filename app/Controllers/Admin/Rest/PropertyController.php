<?php
	
namespace App\Controllers\Admin\Rest;

use App\Models\Property;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

class PropertyController{

	public function allByFullAddress(Request $request, Response $response){

		$addresses = Property::where('archived', "false")->pluck('full_address')->toArray();

		return $response->withJson($addresses);

	}

	public function getByFullAddress($fulladdress,Request $request, Response $response){

			$property = Property::where('full_address', $fulladdress)->first();

			$data = [ 'property_code', $property->code];
			
			return $response->withJson($data,200);

	}
	
}