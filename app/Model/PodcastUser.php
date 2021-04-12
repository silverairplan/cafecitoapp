<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PodcastUser extends Model
{
    //
    protected $table = "podcastusers";
    protected $fillable = ['userid','podcast_id'];
}
