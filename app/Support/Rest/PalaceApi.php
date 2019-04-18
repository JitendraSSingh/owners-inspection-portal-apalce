<?php

namespace App\Support\Rest;

use GuzzleHttp\ClientInterface;

use App\Support\Rest\Contracts\ApiInterface;

class PalaceApi implements ApiInterface{

	protected $client;

	private $username;

	private $password;

	public function __construct(ClientInterface $client, $username, $password){

		$this->client = $client;

		$this->username = $username;

		$this->password = $password;
	}

	public function setEndPoint($endpoint){

		$this->endpoint = $endpoint;

		return $this;
	
	}

	public function get(){
	
		$response = $this->client->request('GET', 
		
		$this->endpoint, 
		
			[
				'auth' => [$this->username, $this->password],

				'headers' => [

					'content-type' => 'application/xml',

					'Accept' => 'application/xml'

				]

			]

		);

			if( ($response->getStatusCode()) === 200 ){

			$body = $response->getBody();

			$contents = (string) $body;

			return $contents;

			}

			return FALSE;
	
	}
}