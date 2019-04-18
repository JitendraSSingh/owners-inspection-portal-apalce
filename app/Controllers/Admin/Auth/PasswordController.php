<?php

namespace App\Controllers\Admin\Auth;

use Slim\Views\Twig;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use App\Validation\Validator;

use Respect\Validation\Validator as v;

use Slim\Interfaces\RouterInterface as Router;

use App\Models\Owner;

use Slim\Flash\Messages as Flash;

use App\Helpers\Security;

use App\Helpers\Event;

use App\Helpers\Url;

use App\Mail\Mailer\Mailer;

class PasswordController{

	protected $router;

	public function __construct(Router $router, Flash $flash){

		$this->router = $router;

		$this->flash = $flash;

	}

	public function getPasswordSet($id,$ownercode,Request $request, Response $response, Twig $view){

		return $view->render($response, 'admin/auth/passwordset.twig', compact('id','ownercode'));

	}

	public function postPasswordSet(Request $request, Response $response,Validator $validator ,Twig $view){

		$validation = $validator->validate($request, [
			'password' => v::noWhitespace()->notEmpty(),
			'confirm_password' => v::noWhitespace()->notEmpty()->confirmPassword($request->getParam('password'))
		]);

		if($validation->hasFailed()){
			return $response->withRedirect($this->router->pathFor('admin.setpassword',['id'=>$request->getParam('owner_id'), 'ownercode' => $request->getParam('owner_code')]));
		}


		try{

			Owner::where('id',$request->getParam('owner_id'))
			->where('code',$request->getParam('owner_code'))
			->update(['password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT)]);

			$this->flash->addMessage('info', 'Password Set For The Owner');
			return $response->withRedirect($this->router->pathFor('admin.setpassword',['id'=>$request->getParam('owner_id'), 'ownercode' => $request->getParam('owner_code')]));
		}
		catch(Exception $e){
			//TODO No Owner Found
			//
			$this->flash->addMessage('error', 'There was some error in setting the Password');
			return $response->withRedirect($this->router->pathFor('admin.setpassword',['id'=>$request->getParam('owner_id'), 'ownercode' => $request->getParam('owner_code')]));
		}

		return $response->withRedirect($this->router->pathFor('admin.setpassword',['id'=>$request->getParam('owner_id'), 'ownercode' => $request->getParam('owner_code')]));

	}

	public function sendPasswordLink($ownerId,$ownerCode,Request $request, Response $response,Mailer $mailer){

		$owner = Owner::where('id',$ownerId)
			->where('code',$ownerCode)->first();

		if($owner){

			//Security Token Saved
			$owner->password_link_hash = Security::getRandomHash();

			$token_expiry = date("Y-m-d H:i:s", Event::generateValidityDay(7));

			$owner->token_expiry = $token_expiry;

			$owner->save();

			//Now Send The email
			$userOwner = new \stdClass;

			$userOwner->fullname = $owner->full_name;

			$userOwner->ownerCode = $owner->code;

			$userOwner->email = ($owner->email_1) ? $owner->email_1 : $owner->email_2;

			$userOwner->token_expiry = date("jS F Y h:i:s A", strtotime($owner->token_expiry));

			$userOwner->passwordLink = Url::getBaseUrl().$this->router->pathFor('passwordSetFromLink',['ownerid' => $owner->id, 'ownercode' => $owner->code, 'token' => $owner->password_link_hash ]);

				if($userOwner->email !== ""){

				$mailer->send('emails/owner/password_set_link.twig', compact('userOwner'), function($message) use ($userOwner){
					$message->subject('Tommy\'s Owner\'s Portal Password Set');
					$message->to($userOwner->email, $userOwner->fullname);

				});

				$data = [ 'id' => $owner->id, 'full_name' => $owner->full_name,  date("jS F Y h:i:s A", strtotime($userOwner->token_expiry))];

				return $response->withJson($data);
			}

			return $response->withJson(['error'=>'Could Not Send Email Please Chcek this Landlord has a valid Email ID'],404);

		}

		return $response->withJson(['error'=>'Owner Not Found'],404);
	}

	public function getPasswordSetFromLink($ownerid,$ownercode,$token,Response $response,Twig $view){

		$owner = Owner::where('id',$ownerid)->where('code',$ownercode)->where('password_link_hash',$token)->where('token_expiry', '>=', date("Y-m-d H:i:s"))->first();

		if($owner){
			return $view->render($response,'owner/password_set.twig',['ownerid'=>$owner->id,'ownercode'=>$owner->code,'token' => $token]);
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
			return $response->withRedirect($this->router->pathFor('passwordSetFromLink',['ownerid' => $owner->id, 'ownercode' => $owner->code, 'token' => $owner->password_link_hash ]));
		}

		try{

			Owner::where('id',$ownerid)
			->where('code',$ownercode)
			->update(['password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
					  'token_expiry' => date("Y-m-d H:i:s", ( time() - 5 ))
					]);

			$this->flash->addMessage('info', 'You can now login with the email and the password which you have just set');
			return $response->withRedirect($this->router->pathFor('home'));
		}
		catch(Exception $e){
			//TODO No Owner Found
			//
			$this->flash->addMessage('error', 'There was some error in setting the Password');
			return $response->withRedirect($this->router->pathFor('home'));
		}

		}

	}

}