<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    //
    protected $table = "episodes";
    protected $fillable = ['podcast','title','audiourl','description','duration'];

    function podcastinfo()
	{
		return $this->belongsTo(Podcast::class,'podcast');
	}
}
