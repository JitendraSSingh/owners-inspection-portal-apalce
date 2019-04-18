<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Property;

use App\Models\File;

class Inspection extends Model{

	use SoftDeletes;

	protected $fillable = [ 

		'property_id' ,

		'date' ,

		'short_statement',

		'description' ,

		'urls'

	 ];

	public function property(){

	 	return $this->belongsTo(Property::class);
	 
	}

	public function files(){

		return $this->hasMany(File::class);

	}

	public function attachmentFiles(){

		return $this->files()->where('type','file_attachment')->get();

	}

	public function imageFiles(){

		return $this->files()->where('type','image_attachment')->get();

	}

	public function descriptionExcerpt(){
		return (substr($this->description, 0, 24));
	}

	public function urlsDecode(){
		return (json_decode($this->urls));
	}

	public function getDescriptionHTML(){

		return htmlspecialchars_decode($this->description);
		
	}

	public function getDescriptionEditHTML(){

		return json_encode($this->getDescriptionHTML());
		
	}

	public function dateTimeDecode(){
		$format = "Y/m/d H:i";
		return date($format, strtotime($this->date));
	}

	public function getUrls(){
		return json_decode($this->urls);
	}
}