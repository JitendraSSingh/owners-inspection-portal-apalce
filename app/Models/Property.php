<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Owner;

use App\Models\Inspection;

class Property extends Model{

	protected $table = 'property';

	protected $fillable = [ 
		
		'code' , 
		
		'unit' , 
		
		'address_1' , 
		
		'address_2' , 

		'address_3' , 

		'address_4' ,

		'full_address' ,

		'postal_code' ,

		'status' ,

		'opening_balance' ,

		'current_balance' ,

		'archived' ,

		'rental_period' ,

		'rental_amount' ,

		'phone' ,

		'name' ,

		'change_code' ,

		'owner_code' ,

		'agent_full_name' ,

		'agent_email_1' ,

		'agent_email_2'

		];

		public function owner(){

			return $this->belongsTo(Owner::class, 'owner_code', 'code');
		}

		/**
		  * Get Inspections from the Property
		  */

	public function inspections(){

		return $this->hasMany(Inspection::class)->orderBy( 'date' , 'desc' );

	}
	public function getCommaAddress(){
		
		$commaSeperateAddressArr = [];
		$unit = $this->unit;
		$address_1 = $this->address_1;
		$address_2 = $this->address_2;
		$address_3 = $this->address_3;
		$address_4 = $this->address_4;
		if(isset($unit)){
			$commaSeperateAddressArr[] = $unit." ".$address_1;
		}
		else{
			$commaSeperateAddressArr[] = $address_1;	
		}
		$commaSeperateAddressArr[] = $address_2;
		$commaSeperateAddressArr[] = $address_3;
		$commaSeperateAddressArr[] = $address_4;
		return implode(",", $commaSeperateAddressArr);
	}
} 