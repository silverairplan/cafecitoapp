<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
	protected $table = "messages";
    protected $fillable = ['from','to','message','status','livestreamid','attached'];

    function fromuser()
    {
    	return $this->belongsTo(User::class,'from');
    }

    function touser()
    {
    	return $this->belongsTo(User::class,'to');
    }
}