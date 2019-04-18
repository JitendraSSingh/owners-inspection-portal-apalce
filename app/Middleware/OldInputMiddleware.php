<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Views\Twig;

class OldInputMiddleware{

    public $view;

    public function __construct(Twig $view){
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $response, callable $next){
        if(array_key_exists('old', $_SESSION)){
            $this->view->getEnvironment()->addGlobal('old', $_SESSION['old']);
        }
        $_SESSION['old'] = $request->getParams();

        $response = $next($request,$response);
        return $response;
    }
}