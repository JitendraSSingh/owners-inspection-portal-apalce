<?php

namespace App\Support\Xml\Contracts;

interface OwnerPopulateInterface{

	public function ownerExists($owner_code);

	public function ownerUpdated($old_change_code, $new_change_code);

	public function updateOwner($owner_code, array $data);

	public function createOwner(array $data);

	public function run();
}