<?php

namespace App\Support\Xml\Contracts;

interface PropertyPopulateInterface{

	public function propertyExists($property_code);

	public function propertyUpdated($old_change_code, $new_change_code);

	public function updateProperty($property_code, array $data);

	public function createProperty(array $data);

	public function run();
}