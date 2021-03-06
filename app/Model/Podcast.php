<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Podcast extends Model
{
    //
    protected $table = "podcast";
    protected $fillable = ['feedurl','image','title','description','creator','author','price','type'];

    function createrinfo()
    {
    	return $this->belongsTo(User::class,'creator');
    }
}
