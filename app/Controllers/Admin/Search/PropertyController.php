<?php

namespace App\Controllers\Admin\Search;

use Slim\Views\Twig;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Property;

use App\Mail\Mailer\Mailer;

class PropertyController{

	public function index(Request $request, Response $response, Twig $view, Mailer $mailer){

		// $user = new \stdClass;

		// $user->name = 'Verum';

		// $user->email = 'jitusingh18@gmail.com';

		// 	$mailer->send('emails/welcome.twig', compact('user'), function($message) use ($user){
		// 	$message->to($user->email, $user->name);
		// });


		return $view->render($response, 'admin/search/property.twig');

	}

	public function getByFullAddress(Request $request, Response $response, Twig $view){

		$address = $request->getParam('full_address');

		$property = Property::where('full_address', $address)->first();

		$owner = $property->owner;

		$data = [ 'property' => $property , 'owner' => $owner ];

		return $view->render($response, 'admin/property/view.twig', $data);

	}

	public function getByPropId($propertyid,Request $request, Response $response, Twig $view){

		// Get header from request object
        $refererHeader = $request->getHeader('HTTP_REFERER');
        	$referer = "";
	        if ($refererHeader) {
	            // Extract referer value
	            $referer = array_shift($refererHeader);
	        }

		$property = Property::where('id', $propertyid)->first();

		$owner = $property->owner;

		$data = [ 'property' => $property , 'owner' => $owner, 'referer' => $referer];

		return $view->render($response, 'admin/property/view.twig', $data);

	}

}