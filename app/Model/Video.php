<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    //
    protected $table = "videos";
    protected $fillable = ['title','coverimage','creater','source'];

    function user()
	{
		return $this->belongsTo(User::class,'creater');
	}
}