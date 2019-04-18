<?php

namespace App\Support\FileHandling\Contracts;

use Slim\Http\UploadedFile;

use Psr\Http\Message\ResponseInterface as Response;

interface FileHandlingInterface{

	public function getExtension($filename);

	public function getRandomFileName($extension);

	public function store($directory, $seperator, UploadedFile $uploadedFile, $filename);

	public function load($filename, Response $response);

	public function getMime($filename);

}