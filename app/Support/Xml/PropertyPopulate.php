<?php

namespace App\Support\Xml;

use App\Support\Xml\Contracts\PropertyPopulateInterface;

use SimpleXMLElement;

use PDOException;

use App\Models\Property;

use App\Helpers\Property as PropertyHelper;

class PropertyPopulate implements PropertyPopulateInterface{

	protected $newProperties = [];

	protected $updatedProperties = [];

	protected $propertyObj;

	protected $orphanNewProperties = [];

	protected $orphanUpdateProperties = [];

	protected $errors = [];
	

	public function setPropertyObj(SimpleXMLElement $propertyObj){

		$this->propertyObj = $propertyObj;

		return $this;

	}


	public function propertyExists($property_code){
		
		return Property::where('code',$property_code)->first();
	
	}

	public function propertyUpdated($old_change_code, $new_change_code){

		return !($old_change_code === (string)$new_change_code);
	
	}

	public function updateProperty($property_code, array $data){

		try{

		Property::where( 'code' , $property_code )->update($data);

		}

		catch(PDOException $e){

			$this->errors[] = $e->getMessage();

			$this->orphanUpdateProperties[] = $data;

		}

	}

	public function createProperty(array $data){

		try{

			Property::create($data);	
		
		}

		catch(PDOException $e){

			$this->errors[] = $e->getMessage();

			$this->orphanNewProperties[] = $data;

		}
	
	}


	public function run(){



		//Loop for every Property in XML
		foreach ($this->propertyObj->ViewAllDetailedProperty as $key => $value) {

			$propertyXmlConvert = PropertyHelper::convert($value);


			//Check if that property already exists through "PropertyCode" field
			if( $property = $this->propertyExists($propertyXmlConvert['code'] ) ){
				
				//If Change code has changed mean property has an updated data
				if($this->propertyUpdated($property->change_code, $propertyXmlConvert['change_code'])){

					$this->updatedProperties[] = $propertyXmlConvert;
				
					//Save it To Update Property Array
					$this->updateProperty($property->code, $propertyXmlConvert);
				}

			}
			else{
				//Create New Property But Only Those Which are Not Archived or archived == false and also Status == "Active"
				
				if( ($value->PropertyStatus == "Active") && ($value->PropertyArchived == "false" ) ){

					$this->createProperty($propertyXmlConvert);
					//Save it To New Property Array
					$this->newProperties[] = $propertyXmlConvert;
				
				}	
			}
		}

		return $this;	

	}

	public function generateReport(){

		return [ 

				'report' => [
						
							'newProperties' => $this->newProperties, 

							'updatedProperties' => $this->updatedProperties, 

							'orphanNewProperties' => $this->orphanNewProperties,

							'orphanUpdateProperties' => $this->orphanUpdateProperties,

							'errors' => $this->errors

							]

			];
	
	}
	
}