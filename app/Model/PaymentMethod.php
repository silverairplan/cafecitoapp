<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    //
    protected $table = "paymentmethod";
    protected $fillable = ['address','apartment','city','state','country','post_code','card_name','card_number','card_expiry','card_cvv','card_type','creater'];

    function userinfo()
	{
		return $this->belongsTo(User::class,'creater');
	}
}