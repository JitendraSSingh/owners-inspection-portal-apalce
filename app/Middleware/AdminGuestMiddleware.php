<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Interfaces\RouterInterface as Router;
use App\AdminAuth\AdminAuth;

class AdminGuestMiddleware{
	
	protected $router;
	protected $auth;

	public function __construct(Router $router, AdminAuth $adminAuth){
		$this->adminAuth = $adminAuth;
		$this->router = $router;
	}

	public function __invoke(Request $request, Response $response, callable $next){
		
		if($this->adminAuth->check()){
		    return $response->withRedirect($this->router->pathFor('admin.properties'));
		}
		$response = $next($request,$response);
		return $response;
	}
}