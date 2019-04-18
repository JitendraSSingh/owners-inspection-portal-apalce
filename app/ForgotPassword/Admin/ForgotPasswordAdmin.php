<?php

namespace App\ForgotPassword\Admin;

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

use App\Models\User;

class ForgotPasswordAdmin{

	protected $router;

	public function __construct(Router $router, Flash $flash){

		$this->router = $router;

		$this->flash = $flash;

	}

	public function sendPasswordLink(Request $request, Validator $validator, Response $response,Mailer $mailer){

		$user = User::where('email',$request->getParam('email'))->first();
		
		$validation = $validator->validate($request, [
			'email' => v::noWhitespace()->notEmpty()->email()
		]);

		if($validation->hasFailed()){

			return $response->withRedirect($this->router->pathFor('login.auth.admin'));
		}

		if($user){
		
			//Security Token Saved
			$user->password_link_hash = Security::getRandomHash();

			$token_expiry = date("Y-m-d H:i:s", Event::generateValidityDay(7));
			
			$user->token_expiry = $token_expiry;
			
			$user->save();

			//Now Send The email 
			$userAdmin = new \stdClass;

			$userAdmin->name = $user->name;

			$userAdmin->email = $user->email;

			$userAdmin->token_expiry = date("jS F Y h:i:s A", strtotime($user->token_expiry));

			$userAdmin->passwordLink = Url::getBaseUrl().$this->router->pathFor('login.auth.admin.reset.password.userid.token',['userid' => $user->id, 'token' => $user->password_link_hash ]);

				if($userAdmin->email !== ""){
				
				$mailer->send('emails/admin/password_reset_link.twig', compact('userAdmin'), function($message) use ($userAdmin){
					$message->subject('Tommy\'s Owner\'s Portal Password Reset for Admin');
					$message->to($userAdmin->email, $userAdmin->name);
		
				});
				
				
				$this->flash->addMessage('info', 'Check email for Reset');

				return $response->withRedirect($this->router->pathFor('login.auth.admin'));
			}

			return $response->withRedirect($this->router->pathFor('login.auth.admin'));			
			
		}

		return $response->withRedirect($this->router->pathFor('login.auth.admin'));
	}

	public function getPasswordSetFromLink($userid,$token,Response $response,Twig $view){

		$user = User::where('id',$userid)->where('password_link_hash',$token)->where('token_expiry', '>=', date("Y-m-d H:i:s"))->first();

		if($user){

			return $view->render($response,'admin/password_reset.twig',['userid'=>$user->id,'token' => $token]);
		}

	}

	public function postPasswordSetFromLink($userid,$token,Validator $validator,Request $request, Response $response){

		$user = User::where('id',$userid)->where('password_link_hash',$token)->where('token_expiry', '>=', date("Y-m-d H:i:s"))->first();

		if($user){
			//Give Them View To Set Password
			
		$validation = $validator->validate($request, [
			'password' => v::noWhitespace()->notEmpty(),
			'confirm_password' => v::noWhitespace()->notEmpty()->confirmPassword($request->getParam('password'))
		]);

		if($validation->hasFailed()){
		    return $response->withRedirect($this->router->pathFor('login.auth.admin.reset.password.userid.token',['userid' => $user->id, 'token' => $user->password_link_hash ]));
		}

		try{

			User::where('id',$user->id)
			->update(['password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
					  'token_expiry' => date("Y-m-d H:i:s", ( time() - 5 ))
					]);

			$this->flash->addMessage('info', 'Password Reset Successfull');
			return $response->withRedirect($this->router->pathFor('login.auth.admin'));
		}
		catch(Exception $e){
			//TODO No Owner Found
			//
			$this->flash->addMessage('error', 'There was some error in Resetting the Password');
			return $response->withRedirect($this->router->pathFor('login.auth.admin'));
		}

		}

	}	

}