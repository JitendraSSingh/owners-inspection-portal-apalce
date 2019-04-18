<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

use App\Models\User;

class EmailAvailableEdit extends AbstractRule{

	protected $oldEmail;

	public function __construct($oldEmail){
		$this->oldEmail = $oldEmail;
	}

    public function validate($input){
    	if($this->oldEmail === $input){
    		return true;
    	}
        return User::where('email',$input)->count() === 0;
    }
}