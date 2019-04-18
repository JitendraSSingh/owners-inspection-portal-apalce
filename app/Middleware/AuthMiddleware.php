<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Interfaces\RouterInterface as Router;
use App\Auth\Auth;
use Slim\Flash\Messages as Flash;

class AuthMiddleware{

	protected $router;
	protected $auth;
	protected $flash;
	public function __construct(Router $router, Auth $auth, Flash $flash){
		$this->router = $router;
		$this->auth = $auth;
		$this->flash = $flash;
	}

	public function __invoke(Request $request, Response $response, callable $next){
		if(!$this->auth->check()){
			$this->flash->addMessage('error','Please sign in before doing that');
			return $response->withRedirect($this->router->pathFor('home'));
		}
		$response = $next($request,$response);
		return $response;
	}
}