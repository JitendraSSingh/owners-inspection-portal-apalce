<?php

namespace App\Middleware;

use Slim\Views\Twig;

use Psr\Http\Message\ServerRequestInterface as Request;

use Psr\Http\Message\ResponseInterface as Response;

use Slim\Csrf\Guard;

class CsrfViewMiddleware {

	protected $view;

	protected $guard;

	public function __construct(Twig $view, Guard $guard){

		$this->guard = $guard;

		$this->view = $view;

	}

	public function __invoke(Request $request, Response $response, callable $next){

		$this->view->getEnvironment()->addGlobal('csrf', [

			'field' => '<input type="hidden" name="'.$this->guard->getTokenNameKey().'" value="'.$this->guard->getTokenName().'" >

				<input type="hidden" name="'.$this->guard->getTokenValueKey().'" value="'.$this->guard->getTokenValue().'">',

		]);

		$response = $next($request, $response);

		return $response;
	
	}

}