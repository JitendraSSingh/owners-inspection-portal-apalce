<?php

namespace App\Controllers\Admin\Auth;

use Slim\Views\Twig;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use App\AdminAuth\AdminAuth;

use Slim\Interfaces\RouterInterface as Router;

use Slim\Flash\Messages as Flash;


class AuthController{

	protected $view;
	protected $adminAuth;
	protected $router;
	protected $flash;

	public function __construct(Twig $view, AdminAuth $adminAuth, Router $router, Flash $flash){
		$this->view = $view;
		$this->adminAuth = $adminAuth;
		$this->router = $router;
		$this->flash = $flash;
	}

	public function getSignIn(Response $response, Twig $view){

		return $this->view->render($response, 'admin/auth/signin.twig');

	}

	public function postSignIn(Request $request, Response $response, Twig $view){
        
        if($this->adminAuth->attempt($request->getParam('userid'),$request->getParam('password'))){
        	return $response->withRedirect($this->router->pathFor('admin.properties'));
        }

        $this->flash->addMessage('error','Could Not Login with this details');
        
        return $response->withRedirect($this->router->pathFor('login.auth.admin'));

	}

	public function getSignOut(Response $response){
        $this->adminAuth->logout();
        return $response->withRedirect($this->router->pathFor('login.auth.admin'));
    }
}