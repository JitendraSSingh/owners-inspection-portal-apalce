<?php

namespace App\Validation;

use Respect\Validation\Exceptions\NestedValidationException;

use Psr\Http\Message\ServerRequestInterface as Request; 

use Respect\Validation\Validator as Respect;

class Validator{

	protected $errors;

	public function validate(Request $request, array $rules){

		foreach ($rules as $field => $rule) {
			try{
				$rule->setName( ucfirst( str_replace('_', ' ', $field) ) )->assert($request->getParam($field));
			}
			catch(NestedValidationException $e){
				$this->errors[$field] = $e->getMessages();
			}
		}

		$_SESSION['errors'] = $this->errors;

		return $this;
	}

	public function hasFailed(){
		return !(empty($this->errors));
	} 
}