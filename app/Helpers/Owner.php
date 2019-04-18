<?php

namespace App\Helpers;

use SimpleXMLElement;

class Owner{

	public static function convert(SimpleXMLElement $ownerObj){

		return [

		'code' => $ownerObj->OwnerCode ,

		'first_name' => $ownerObj->OwnerFirstName ,

		'last_name' => $ownerObj->OwnerLastName ,

		'full_name' => $ownerObj->OwnerFullName ,

		'address' => $ownerObj->OwnerAddress ,

		'phone_home' => $ownerObj->OwnerPhoneHome ,

		'phone_mobile' => $ownerObj->OwnerMobile ,

		'phone_work' => $ownerObj->OwnerPhoneWork ,

		'email_1' => $ownerObj->OwnerEmail1 ,

		'email_2' => $ownerObj->OwnerEmail2 ,

		'opening_balance' => $ownerObj->OwnerOpeningBalance ,

		'current_balance' => $ownerObj->OwnerCurrentBalance ,

		'payment_type' => $ownerObj->OwnerPaymentType ,

		'change_code' => $ownerObj->OwnerChangeCode ,

		'archived' => $ownerObj->OwnerArchived
		];
	
	}
}