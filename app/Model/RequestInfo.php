<?php 

	namespace App\Model;

	use Illuminate\Database\Eloquent\Model;

	class RequestInfo extends Model
	{
		protected $table = "requests";
		protected $fillable = ["type","for","influencer","to","from","pronoun",'occasion',"instruction","email","phone","status","hidevideo","quantity","reply","customerid"];

		public function influencerinfo()
		{
			return $this->belongsTo(User::class,'influencer');
		}

		public function customerinfo()
		{
			return $this->belongsTo(User::class,'customerid');
		}
	}