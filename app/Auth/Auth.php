<?php

namespace App\Auth;

use App\Models\Owner;

class Auth{

	public function owner(){
		if(isset($_SESSION['owner'])){
		return Owner::find($_SESSION['owner']['id']);
		}
		return false;
	}

	public function check(){
		return isset($_SESSION['owner']['id']);
	}

	public function attempt($userid, $password){

		//Check If the owner exists before checking the password
		$owner = Owner::where('code',$userid)->first();
		
		if(!$owner){
			return false;
		}

		if(password_verify($password, $owner->password)){
			$_SESSION['owner']['id'] = $owner->id;
			return true;
		}

		return false;

	}

	public function logout(){
		unset($_SESSION['owner']);
	}
}