<?php

namespace App\Helpers;

class AllowedFileMeta{

	public static function getAllowedDocMimeTypes(){

		return ['application/msword' ,

				'application/pdf',

				'text/plain' ,

				'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ,

				'application/vnd.openxmlformats-officedocument.wordprocessingml.template' ,

				'application/vnd.ms-word.document.macroEnabled.12' ,

				'application/vnd.ms-word.template.macroEnabled.12' ,

				'application/vnd.ms-excel' ,

				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ,

				'application/vnd.openxmlformats-officedocument.spreadsheetml.template' ,

				'application/vnd.ms-excel.sheet.macroEnabled.12' ,

				'application/vnd.ms-excel.template.macroEnabled.12' ,

				'application/vnd.ms-excel.addin.macroEnabled.12' ,

				'application/vnd.ms-excel.sheet.binary.macroEnabled.12' ,

				'application/vnd.oasis.opendocument.text' ,

				'application/vnd.oasis.opendocument.text-template' ,

				'application/vnd.oasis.opendocument.text-web' ,

				'application/vnd.oasis.opendocument.spreadsheet' ,

				'application/vnd.oasis.opendocument.spreadsheet-template'

			];
	}

	public static function getAllowedDocFileExtensions(){

		return [

				'doc' , 

				'pdf' ,

				'dot' , 

				'docx' , 

				'dotx' , 

				'docm' , 

				'dotm' , 

				'xls' ,

				'xlt' ,

				'xla' ,

				'xlsx' ,

				'xltx' ,

				'xlsm' ,

				'xlam' ,

				'xlsb' ,

				'odt' ,

				'ott' ,

				'oth' ,

				'ods' ,

				'ots' 

			];

	}

	public static function getAllowedImageMimeTypes(){
		return [ 'image/bmp' ,'image/gif', 'image/jpeg', 'image/png', 'image/webp'];
	}

	public static function getAllowedImageFileExtensions(){
		return [ 'bmp', 'gif', 'jpeg', 'jpg', 'png',  'webp'];
	}

}