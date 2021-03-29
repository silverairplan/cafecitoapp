<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    //
    protected $table = "paymenthistory";
    protected $fillable = ['productinfo','price','type','methodid','creater'];

    function userinfo()
	{
		return $this->belongsTo(User::class,'creater');
	}

	function method()
	{
		return $this->belongsTo(PaymentMethod::class,'methodid');
	}
}