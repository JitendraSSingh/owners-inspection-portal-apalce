<?php

namespace App\Helpers;

use App\Support\FileHandling\Contracts\FileHandlingInterface;

use App\Helpers\AllowedFileMeta; 

class Uploader{

	const DS = '/';

	private $filehandling;

	public $maxUploadSize;

	public $storageDirectory;
	
	public function __construct(FileHandlingInterface $fileHandling){

		$this->maxUploadSize = 10 * 1024 * 1024; //10MB

		$this->fileHandling = $fileHandling;

	}

	public function setMaxUploadSize($maxUploadSize){
		
		$this->maxUploadSize = $maxUploadSize;

		return $this;
	}

	public function setStorageDirectory($storageDirectory){

		$this->storageDirectory = $storageDirectory;

		return $this;

	}

	public function uploadAttachmentFiles($uploadedFiles){

		$allowedDocMimeTypes = AllowedFileMeta::getAllowedDocMimeTypes();		

		$allowedDocFileExtensions = AllowedFileMeta::getAllowedDocFileExtensions();

		$uploadedInspectionFiles = [];

		foreach ($uploadedFiles as $key => $uploadedFileObj) {
			
			$mime_type = $this->fileHandling->getMime($uploadedFileObj->file);

			$extension = $this->fileHandling->getExtension($uploadedFileObj->getClientFilename());

			$uploadErrorCode = $uploadedFileObj->getError();						

			if( 
				( in_array( $mime_type, $allowedDocMimeTypes ) )	 	&&
				( in_array( strtolower($extension), $allowedDocFileExtensions ) )	&&
				( $uploadedFileObj->getSize() <= $this->maxUploadSize )	&&
				( $uploadErrorCode === UPLOAD_ERR_OK )
			)
			{

				//Everything OK Upload File
				$random_filename = $this->fileHandling->getRandomFileName($extension);

				$random_filename = $this->fileHandling->store($this->storageDirectory, self::DS, $uploadedFileObj, $random_filename);
				$uploadedInspectionFiles[$key]['real_name'] = $uploadedFileObj->getClientFilename();
				$uploadedInspectionFiles[$key]['random_filename'] = $random_filename;
				$uploadedInspectionFiles[$key]['type'] = $mime_type;
			}

		}

		return $uploadedInspectionFiles;

	}

	public function uploadImageFiles($uploadedFiles){

		$allowedImageMimeTypes = AllowedFileMeta::getAllowedImageMimeTypes();

		$allowedImageFileExtensions = AllowedFileMeta::getAllowedImageFileExtensions();
				
		$uploadedImageFiles = [];	

		foreach ($uploadedFiles as $key => $uploadedFileObj) {
			
			$mime_type = $this->fileHandling->getMime($uploadedFileObj->file);

			$extension = $this->fileHandling->getExtension($uploadedFileObj->getClientFilename());

			$uploadErrorCode = $uploadedFileObj->getError();		

			if( 
				( in_array( $mime_type, $allowedImageMimeTypes ) ) 		&& 
				( in_array( strtolower($extension), $allowedImageFileExtensions ) ) &&
				( $uploadedFileObj->getSize() <= $this->maxUploadSize ) &&
				( $uploadErrorCode === UPLOAD_ERR_OK )
			){

				//Everything OK Upload Image File
				$random_image_filename = $this->fileHandling->getRandomFileName($extension);

				$random_image_filename = $this->fileHandling->store($this->storageDirectory, self::DS, $uploadedFileObj, $random_image_filename);

				$uploadedImageFiles[$key]['real_name'] = $uploadedFileObj->getClientFilename();
				$uploadedImageFiles[$key]['random_image_filename'] = $random_image_filename;
				$uploadedImageFiles[$key]['type'] = $mime_type;
			}

		}

		return $uploadedImageFiles;
	}

}