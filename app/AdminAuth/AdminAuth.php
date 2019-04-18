<?php

namespace App\AdminAuth;

use App\Models\User;

class AdminAuth{

	public function user(){
		if(isset($_SESSION['user'])){
			return User::find($_SESSION['user']['id']);
		}
		return false;
	}

	public function check(){
		return isset($_SESSION['user']);
	}

	public function attempt($userid, $password){

		//Check if user exsist
		$user = User::where('email',$userid)->first();
		if(!$user){
			return false;
		}
		if(password_verify($password, $user->password)){
			$_SESSION['user']['id'] = $user->id;
			return true;
		}
		return false;
	}

	public function logout(){
		unset($_SESSION['user']);
	}

}