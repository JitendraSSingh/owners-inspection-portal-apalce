<?php

namespace App\Helpers;

use SimpleXMLElement;

class Property{

	public static function convert(SimpleXMLElement $propertyObj){

		return [ 
			
			'code' => (string)$propertyObj->PropertyCode, 
			
			'unit' => (string)$propertyObj->PropertyUnit, 
			
			'address_1' => (string)$propertyObj->PropertyAddress1, 
			
			'address_2' => (string)$propertyObj->PropertyAddress2, 

			'address_3' => (string)$propertyObj->PropertyAddress3, 

			'address_4' => (string)$propertyObj->PropertyAddress4,

			'full_address' => (string)$propertyObj->PropertyUnit.' '.(string)$propertyObj->PropertyAddress1.' '.(string)$propertyObj->PropertyAddress2.' '.(string)$propertyObj->PropertyAddress3.' '.(string)$propertyObj->PropertyAddress4,

			'postal_code' => (string)$propertyObj->PropertyPostCode,

			'status' => (string)$propertyObj->PropertyStatus,

			'opening_balance' => (string)$propertyObj->PropertyOpeningBalance,

			'current_balance' => (string)$propertyObj->PropertyCurrentBalance,

			'archived' => (string)$propertyObj->PropertyArchived,

			'rental_period' => (string)$propertyObj->PropertyRentalPeriod,

			'rental_amount' => (string)$propertyObj->PropertyRentAmount,

			'phone' => (string)$propertyObj->PropertyPhone,

			'name' => (string)$propertyObj->PropertyName,

			'change_code' => (string)$propertyObj->PropertyChangeCode,

			'owner_code' => (string)$propertyObj->PropertyOwnerCode,

			'agent_full_name' => (string)$propertyObj->PropertyAgentFullName,

			'agent_email_1' => (string)$propertyObj->PropertyAgentEmail1,

			'agent_email_2' => (string)$propertyObj->PropertyAgentEmail2

		];		
	
	}

}