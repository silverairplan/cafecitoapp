<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LiveStream extends Model
{
	protected $table = "livestream";
	protected $fillable = ['coverimage','memberlimit','livemethod','title','creater','participants','status'];

	function user()
	{
		return $this->belongsTo(User::class,'creater');
	}
}