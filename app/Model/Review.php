<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
	protected $table = "reviews";
	protected $fillable = ["title","description","reviews","influencerid","customerid"];
}