<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    //
    protected $table = "carts";
    protected $fillable = ['product_id','quantity','creater'];

    function product()
	{
		return $this->belongsTo(Product::class,'product_id');
	}

	function user()
	{
		return $this->belongsTo(User::class,'creater');
	}
}
