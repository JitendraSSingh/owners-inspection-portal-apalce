<?php

namespace App\Support\Xml;

use App\Support\Xml\Contracts\OwnerPopulateInterface;

use SimpleXMLElement;

use PDOException;

use App\Models\Owner;

use App\Helpers\Owner as OwnerHelper;

class OwnerPopulate implements OwnerPopulateInterface{

	protected $newOwners = [];

	protected $updatedOwners = [];

	protected $ownerObj;

	protected $orphanNewOwners = [];

	protected $orphanUpdateOwners = [];

	protected $errors = [];


	public function setOwnerObj(SimpleXMLElement $ownerObj){

		$this->ownerObj = $ownerObj;

		return $this;

	}

	public function ownerExists($owner_code){
		
		return Owner::where( 'code', $owner_code )->first();
	
	}

	public function ownerUpdated($old_change_code, $new_change_code){

		return !($old_change_code === (string)$new_change_code);
	
	}

	public function updateOwner($owner_code, array $data){

		try{
		
		//Remove email_1, email_2 from updation because Client wants to update them manually and also Palace API doesn't provide updated Email
		unset($data['email_1']);
		
		unset($data['email_2']);

		Owner::where( 'code' , $owner_code )->update($data);

		}

		catch(PDOException $e){

			$this->orphanUpdateOwners[] = $data;

			$this->errors[] = $e->getMessage();

		}

	}

	public function createOwner(array $data){

		try{

			Owner::create($data);	
		
		}

		catch(PDOException $e){

			$this->orphanNewOwners[] = $data;

			$this->errors[] = $e->getMessage();

		}
	
	}


	public function run(){



		//Loop for every Owner in XML
		foreach ($this->ownerObj->ViewAllDetailedOwner as $key => $value) {

			$ownerXmlConvert = OwnerHelper::convert($value);


			//Check if that owner already exists through "OwnerCode" field
			if( $owner = $this->ownerExists($ownerXmlConvert['code'] ) ){
				
				//If Change code has changed mean property has an updated data
				if($this->ownerUpdated($owner->change_code, $ownerXmlConvert['change_code'])){

					$this->updateOwner($owner->code, $ownerXmlConvert);

					//Save it To Update Owner Array
					$this->updatedOwners[] = $ownerXmlConvert;
				}

			}
			else{

				//Create New Owner But Only Those Which are Not Archived or archived == false
				if($value->OwnerArchived == "false"){
					//populate new ones
					$this->createOwner($ownerXmlConvert);
					//Save it To New Owner Array
					$this->newOwners[] = $ownerXmlConvert;
				}

					
			}
		}

		return $this;	

	}

	public function generateReport(){

		return [ 

				'report' => [
						
							'newOwners' => $this->newOwners, 

							'updatedOwners' => $this->updatedOwners,

							'orphanNewOwners' => $this->orphanNewOwners,

							'orphanUpdateOwners' => $this->orphanUpdateOwners,

							'errors' => $this->errors

							]

			];
	
	}
	
}