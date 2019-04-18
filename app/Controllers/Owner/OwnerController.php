<?php

namespace App\Controllers\Owner;

use Slim\Views\Twig;

use App\Models\Owner;

use App\Models\Property;

use App\Support\FileHandling\Contracts\FileHandlingInterface;

use Psr\Http\Message\ServerRequestInterface as Request;

use Psr\Http\Message\ResponseInterface as Response;

use Slim\Interfaces\RouterInterface as Router;

use Slim\Flash\Messages as Flash;

use App\Models\Inspection;

use App\Auth\Auth;

class OwnerController{

	protected $router;
	protected $auth;
	protected $flash;
	protected $fileHandling;

	public function __construct(Router $router, Auth $auth, Flash $flash, FileHandlingInterface $fileHandling){
		$this->router = $router;
		$this->auth = $auth;
		$this->flash = $flash;
		$this->fileHandling = $fileHandling;
	}

	public function index(Request $request, Response $response, Twig $view){

		$owner = Owner::find($_SESSION['owner']['id']);

		return $view->render($response,'owner/index.twig', compact('owner'));

	}

	public function getSignOut(Request $request, Response $response){

		$this->auth->logout();

		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function getProperty($id, $propertycode, Response $response, Twig $view){
		
		$property = Property::where('id', $id)->where('code',$propertycode)->where('owner_code',$this->auth->owner()->code)->first();

		if($property){
			return $view->render($response, 'owner/property.twig',compact('property'));
		}

	}

	public function getOwnerIdNameByEmail($emailaddress, Response $response){
		$ownerId = Owner::where('email_1', $emailaddress)->orderBy('full_name','asc')->get(['code','full_name']);
		return $response->withJson($ownerId);
	}

	public function getInspection($propertyid, $propertycode, $inspectionid, Response $response, Twig $view){
		$property = Property::where('id', $propertyid)->where('code',$propertycode)->where('owner_code',$this->auth->owner()->code)->first();
		$inspection = Inspection::where('property_id',$property->id)->where('id',$inspectionid)->first();
		if($inspection){
			
			return $view->render($response, 'owner/inspection.twig', compact('inspection','propertyid','propertycode','property'));
		}
	}

	public function getFile($filename, Response $response){
		$this->fileHandling->load($filename ,$response);
	}

}