<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Property;

class Owner extends Model{

	protected $table = 'owner';

	protected $fillable = [

		'code' ,

		'first_name' ,

		'last_name' ,

		'full_name' ,

		'address' ,

		'phone_home' ,

		'phone_mobile' ,

		'phone_work' ,

		'email_1' ,

		'email_2' ,

		'password' ,

		'password_link_hash' ,

		'opening_balance' ,

		'current_balance' ,

		'payment_type' ,

		'change_code' ,

		'archived'

	];

	public function properties(){

		return $this->hasMany( Property::class, 'owner_code', 'code');
	}

	public function activeProperties(){
		return $this->properties()->where('status','Active')->where('archived','false')->get();
	}

	public function pending(){
		return ($this->is_pending === 1) ? 'Pending' : 'Active';
	}

	

}