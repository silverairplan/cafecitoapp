<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Likes extends Model
{
    //
    protected $table = "likerequest";
    protected $fillable = ['user','liked','type'];
}