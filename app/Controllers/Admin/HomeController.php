<?php
	
namespace App\Controllers\Admin;

use Slim\Views\Twig;

use App\Models\Owner;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController{

	public function index(Response $response, Twig $view){

		$pendingNewOwners = Owner::where('is_pending', 1 )->count();

		$data = ['pendingNewOwners' => $pendingNewOwners];

		return $view->render($response, 'admin/dashboard.twig', $data);
	
	}

}