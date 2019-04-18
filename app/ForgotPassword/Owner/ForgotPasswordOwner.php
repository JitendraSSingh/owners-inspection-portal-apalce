<?php

namespace App\ForgotPassword\Owner;

use App\Mail\Mailer\Mailer;

use Slim\Flash\Messages as Flash;

use Slim\Views\Twig;

use App\Helpers\Security;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Interfaces\RouterInterface as Router;

use App\Validation\Validator;

use App\Helpers\Event;

use Respect\Validation\Validator as v;

use App\Helpers\Url;

use App\Models\Owner;

class ForgotPasswordOwner{

	protected $router;

	public function __construct(Router $router, Flash $flash){

		$this->router = $router;

		$this->flash = $flash;

	}

	public function sendPasswordLink(Request $request, Response $response,Mailer $mailer){

		$owner = Owner::where('code',$request->getParam('userid'))->where('email_1',$request->getParam('email'))->first();
		
		if($owner){
			
			//Security Token Saved
			$owner->password_link_hash = Security::getRandomHash();

			$token_expiry = date("Y-m-d H:i:s", Event::generateValidityDay(7));
			
			$owner->token_expiry = $token_expiry;
			
			$owner->save();

			//Now Send The email 
			$userOwner = new \stdClass;

			$userOwner->fullname = $owner->full_name;

			$userOwner->email = $request->getParam('email');

			$userOwner->ownerCode = $owner->code;

			$userOwner->token_expiry = date("jS F Y h:i:s A", strtotime($owner->token_expiry));

			$userOwner->passwordLink = Url::getBaseUrl().$this->router->pathFor('owner.reset.password.ownerid.ownercode',['ownerid' => $owner->id, 'ownercode' => $owner->code, 'token' => $owner->password_link_hash ]);

				if($userOwner->email !== ""){
				
				$mailer->send('emails/owner/password_reset_link.twig', compact('userOwner'), function($message) use ($userOwner){
					$message->subject('Tommy\'s Owner\'s Portal Password Reset');
					$message->to($userOwner->email, $userOwner->fullname);
			
				});
				$this->flash->addMessage('info', 'Check email for Reset');

				return $response->withRedirect($this->router->pathFor('home'));				
				
			}
			return $response->withRedirect($this->router->pathFor('home'));	
			
		}
		return $response->withRedirect($this->router->pathFor('home'));

	}

	public function getPasswordSetFromLink($ownerid,$ownercode,$token,Response $response,Twig $view){

		$owner = Owner::where('id',$ownerid)->where('code',$ownercode)->where('password_link_hash',$token)->where('token_expiry', '>=', date("Y-m-d H:i:s"))->first();

		if($owner){
			return $view->render($response,'owner/password_reset.twig',['ownerid'=>$owner->id,'ownercode'=>$owner->code,'token' => $token]);
		}

	}

	public function postPasswordSetFromLink($ownerid,$ownercode,$token,Validator $validator,Request $request, Response $response){

		$owner = Owner::where('id',$ownerid)->where('code',$ownercode)->where('password_link_hash',$token)->where('token_expiry', '>=', date("Y-m-d H:i:s"))->first();

		if($owner){
			//Give Them View To Set Password
			
		$validation = $validator->validate($request, [
			'password' => v::noWhitespace()->notEmpty(),
			'confirm_password' => v::noWhitespace()->notEmpty()->confirmPassword($request->getParam('password'))
		]);

		if($validation->hasFailed()){
			return $response->withRedirect($this->router->pathFor('owner.reset.password.ownerid.ownercode',['ownerid' => $owner->id, 'ownercode' => $owner->code, 'token' => $owner->password_link_hash ]));
		}

		try{

			Owner::where('id',$ownerid)
			->where('code',$ownercode)
			->update(['password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
					  'token_expiry' => date("Y-m-d H:i:s", ( time() - 5 ))
					]);

			$this->flash->addMessage('info', 'Password Reset For The Owner');
			return $response->withRedirect($this->router->pathFor('home'));
		}
		catch(Exception $e){
			//TODO No Owner Found
			//
			$this->flash->addMessage('error', 'There was some error in resetting the Password');
			return $response->withRedirect($this->router->pathFor('home'));
		}

		}

	}
}