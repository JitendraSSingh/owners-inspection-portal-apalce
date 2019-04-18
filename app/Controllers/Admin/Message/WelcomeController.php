<?php

namespace App\Controllers\Admin\Message;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use App\Mail\Mailer\Mailer;

use App\Helpers\Security;

use App\Helpers\Event;

use App\Helpers\Url;

use Slim\Interfaces\RouterInterface as Router;

use Slim\Flash\Messages as Flash;

use App\Models\Owner;

class WelcomeController{

	protected $router;

	public function __construct(Router $router, Flash $flash){

		$this->router = $router;

		$this->flash = $flash;

	}

public function sendWelcome($ownerId,$ownerCode,Request $request, Response $response,Mailer $mailer){

		$owner = Owner::where('id',$ownerId)
			->where('code',$ownerCode)->first();

		if($owner){
			//Additional Code to Reset the password as well

			$owner->password_link_hash = Security::getRandomHash();

			$token_expiry = date("Y-m-d H:i:s", Event::generateValidityDay(7));

			$owner->token_expiry = $token_expiry;

			//Security Token Saved
			$owner->save();

			$userOwner = new \stdClass;

			$userOwner->fullname = $owner->full_name;

			$userOwner->ownerCode = $owner->code;

			$userOwner->email = ($owner->email_1) ? $owner->email_1 : $owner->email_2;

			$userOwner->token_expiry = date("jS F Y h:i:s A", strtotime($owner->token_expiry));

			$userOwner->passwordLink = Url::getBaseUrl().$this->router->pathFor('passwordSetFromLink',['ownerid' => $owner->id, 'ownercode' => $owner->code, 'token' => $owner->password_link_hash ]);

			//Code Ends

				if($userOwner->email !== ""){

				$mailer->send('emails/owner/welcome_message.twig', compact('userOwner'), function($message) use ($userOwner){
					$message->subject('Tommy\'s Property Management New Owner\'s Portal Application');
					$message->to($userOwner->email, $userOwner->fullname);

				});

				$data = [ 'id' => $owner->id, 'full_name' => $owner->full_name,  date("jS F Y h:i:s A", strtotime($userOwner->token_expiry))];

				return $response->withJson($data);
			}

			return $response->withJson(['error'=>'Could Not Send Email Please Chcek this Landlord has a valid Email ID'],404);

		}

		return $response->withJson(['error'=>'Owner Not Found'],404);
	}
}