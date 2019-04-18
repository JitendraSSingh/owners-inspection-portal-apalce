<?php

namespace App\Support\FileHandling;

use App\Support\FileHandling\Contracts\FileHandlingInterface;

use Slim\Http\UploadedFile;

use App\Models\File;

use Psr\Http\Message\ResponseInterface as Response;


class FileHandling implements FileHandlingInterface{

	public function getExtension($filename){

		return pathinfo($filename, PATHINFO_EXTENSION);

	}

	public function getRandomFileName($extension){

		$filename = bin2hex(random_bytes(16));

		return sprintf('%s.%0.16s', $filename, $extension);

	}

	public function store($directory, $seperator, UploadedFile $uploadedFile, $filename){

		$uploadedFile->moveTo($directory.$seperator.$filename);

		return $filename;

	}


	public function load($filename ,Response $response){

			$file = File::where('random_name', $filename)->where('type', 'file_attachment')->first();
			if($file){
			$name = $file->name;
			$extension = pathinfo($filename)['extension'];
			$realFile = $file->location.'/'.$file->random_name;
	      	header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename=' . basename($name));
		    header('Content-Transfer-Encoding: binary');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($realFile));
		    ob_clean();
		    flush();
		    readfile($realFile);
		    exit;
		}		

	}

	public function getMime($filename){

		return mime_content_type($filename);

	}

}