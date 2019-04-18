<?php

namespace App\Controllers\Admin\Landlord;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Owner;

use Illuminate\Pagination\LengthAwarePaginator;

use Slim\Views\Twig as View;

class LandlordController{

    public function index(Request $request, Response $response, View $view){

        $owners = Owner::where('archived','false')->orderBy('full_name','asc')->paginate(10);

        return $view->render($response, 'admin/landlord/index.twig', compact('owners'));

    }

    public function getLandlordProperties($ownercode, Response $response, View $view){

        $owner = Owner::where('code', $ownercode)->first();
        if($owner){
        $properties = $owner->properties;
        return $view->render($response, 'admin/property/property.twig', compact('properties','owner'));
    	}
    }
}