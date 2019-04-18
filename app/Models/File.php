<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Inspection;

use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model{

	use SoftDeletes;

	protected $table = 'files';

	protected $fillable = [ 

		'inspection_id' ,

		'owner_code' ,

		'name' ,

		'caption' ,

		'random_name' ,

		'location' ,

		'type' ,

		'mime_type'

	 ];

	public function inspection(){

		return $this->belongsTo(Inspection::class);

	}


}