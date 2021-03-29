<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	protected $table = "products";
	protected $fillable = ["title","price","description","image","creater"];

	public function createrinfo()
	{
		return $this->belongsTo(User::class,'creater');
	}
}