<?php

namespace App\Controllers\Admin\Property;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Property;

use Illuminate\Pagination\LengthAwarePaginator;

use Slim\Views\Twig as View;

class PropertyController{

    public function index(Request $request, Response $response, View $view){

        $properties = Property::where('status','Active')->where('archived','false')->paginate(10);

        return $view->render($response, 'admin/property/index.twig', compact('properties'));


    }

}