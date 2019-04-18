<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Views\Twig;

class ValidationErrorsMiddleware{

	protected $view;

	public function __construct(Twig $view){

		$this->view = $view;

	}

	public function __invoke(Request $request, Response $response, callable $next){

		if(array_key_exists('errors', $_SESSION)){

		$this->view->getEnvironment()->addGlobal('errors', $_SESSION['errors']);

		unset($_SESSION['errors']);

		}

		$response = $next($request,$response);

		return $response;

	}

}