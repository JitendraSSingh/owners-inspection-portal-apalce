<?php

namespace App\Helpers;

class Event{

	public static function generateValidityDay($days = 1){
		
		date_default_timezone_set("Pacific/Auckland");
		
		if($days < 1){
			$days = 1;
		}

		return (time() + ($days * 24 * 60 * 60)); 
	}

}