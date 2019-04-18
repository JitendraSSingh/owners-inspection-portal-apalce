<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use App\AdminAuth\AdminAuth;

use Slim\Interfaces\RouterInterface as Router;

use Slim\Flash\Messages as Flash;

class AdminAuthMiddleware{

	protected $adminAuth;

	protected $router;

	protected $flash;

	public function __construct(AdminAuth $adminAuth, Router $router, Flash $flash){
		$this->adminAuth = $adminAuth;
		$this->router = $router;
		$this->flash = $flash;
	}

	public function __invoke(Request $request, Response $response,callable $next){
		if(!$this->adminAuth->check()){
			$this->flash->addMessage('error','Please Sign in before doing that');
			return $response->withRedirect($this->router->pathFor('login.auth.admin'));
		}
		$response = $next($request, $response);
		return $response;
	}

}