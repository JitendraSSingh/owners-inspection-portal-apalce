<?php
	
namespace App\Controllers\Admin;

use Slim\Views\Twig;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

class InspectionController{

	public function index(Response $response, Twig $view){

		return $view->render($response, 'admin/inspection/index.twig');
	
	}

	public function getAddInspection(Response $response, Twig $view){

		return $view->render($response, 'admin/inspection/add.twig');
	
	}

	public function getEditInspection(Response $response, Twig $view){

		return $view->render($response, 'admin/inspection/edit.twig');
	
	}
}