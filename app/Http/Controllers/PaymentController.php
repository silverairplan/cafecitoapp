<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Model\User;
use App\Model\PaymentMethod;

class PaymentController extends Controller
{
	public function __construct()
	{

	}


	public function create(Request $request)
	{
		$token = $request->input('token');
		$cardinfo = $request->input('cardinfo');

		$user = User::where('token',$token)->first();

		if($user)
		{

			$cardinfo['creater'] = $user->id;			
			$paymentmethod = PaymentMethod::create($cardinfo);
			return array('success'=>true,'paymentmethod'=>$paymentmethod);
		}	
		else
		{
			return array('success'=>false);
		}
	}

	public function getpaymentmethod(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();

		if($user)
		{
			$paymentmethods = PaymentMethod::where('creater',$user->id)->get();
			return array('success'=>true,'paymentmethod'=>$paymentmethods);
		}
		else
		{
			return array('success'=>false);
		}
	}
}