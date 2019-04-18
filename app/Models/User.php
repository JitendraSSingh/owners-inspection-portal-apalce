<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model{

    use SoftDeletes;

	protected $table = 'users';

	protected $fillable = ['email','password','name','is_admin'];


}