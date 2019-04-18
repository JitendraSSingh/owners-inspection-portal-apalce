<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Interfaces\RouterInterface as Router;
use App\Auth\Auth;

class GuestMiddleware{
	
	protected $router;
	protected $auth;

	public function __construct(Router $router, Auth $auth){
		$this->auth = $auth;
		$this->router = $router;
	}

	public function __invoke(Request $request, Response $response, callable $next){
		
		if($this->auth->check()){
		    return $response->withRedirect($this->router->pathFor('owner.index'));
		}
		$response = $next($request,$response);
		return $response;
	}
}