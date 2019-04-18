<?php

namespace App\Controllers;

use Slim\Views\Twig;

use App\Models\Property;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Interfaces\RouterInterface as Router;

use App\Auth\Auth;

use Slim\Flash\Messages as Flash;

use App\Models\Owner;

class HomeController{

	protected $auth;

	protected $router;

	protected $flash;
	
	public function __construct(Auth $auth, Router $router, Flash $flash){
		$this->auth = $auth;
		$this->router = $router;
		$this->flash = $flash;
	}

	public function index(Request $request, Response $response, Twig $view){
		return $view->render($response, 'home.twig');
	}

	public function postLogin(Request $request, Response $response){

		$auth = $this->auth->attempt(
			$request->getParam('userid'), 
			$request->getParam('password')
		);

		if (!$auth) {
			$this->flash->addMessage('error', 'Could Not sign in with those details.');
			return $response->withRedirect($this->router->pathFor('home'));
		}

		return $response->withRedirect($this->router->pathFor('owner.index'));
	}

}