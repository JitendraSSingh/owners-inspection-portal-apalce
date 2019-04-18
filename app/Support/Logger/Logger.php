<?php

namespace App\Support\Logger;

class Logger{

	protected $file;

	protected $loggerFileHandle;

	public function __construct($file){
		$this->file = $file;
	}
	
	public function initialize(){
		try{
			$this->loggerFileHandle = fopen($this->file, 'a+');
		}
		catch(Exception $e){
			return false;
		}
		return $this;
	}

	public function addMessage($message){
		try{
			fwrite($this->loggerFileHandle, $message);
		}
		catch(Exception $e){
			return false;
		}
		return $this;	
	}

	public function loggerClose(){
		fclose($this->loggerFileHandle);
	}
}
