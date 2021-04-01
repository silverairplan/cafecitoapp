<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Model\User;
use App\Model\PaymentMethod;
use Stripe\Error\Card;
use Cartalyst\Stripe\Stripe;
use App\Model\PaymentHistory;
use App\Model\Product;
use App\Model\RequestInfo;
use App\Model\Review;

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

	public function history(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();

		if($user)
		{
			$histories = PaymentHistory::where('creater',$user->id)->orderBy('created_at','DESC')->get();
			$list = array();
			foreach ($histories as $key => $history) {
				if($history->method)
				{
					array_push($list,$history);
				}
			}

			return ['success'=>true,'history'=>$list];
		}
		else
		{
			return ['success'=>false];
		}
	}

	public function payment(Request $request)
	{
		$requestinfo = $request->input('request');
		$products = $request->input('products');
		$subtotal = $request->input('subtotal');
		$fee = $request->input('fee');
		$token = $request->input('token');
		$stripe = new Stripe(env('STRIPE_SECRET'));
		$cardtoken = $request->input('cardtoken');
		$paymentmethod = $request->input('paymentmethod');
		$shippinginfo = $request->input('shippinginfo');
		$user = User::where('token',$token)->first();

		if($user)
		{
			try
			{
				$charge = $stripe->charges()->create([
					'card'=>$cardtoken,
					'currency'=>'USD',
					'amount'=>$subtotal + $fee,
					'description'=>$user->fullname . ' has paid for ' . ($requestinfo?'request':'card')
				]);

				if($charge['status'] == 'succeeded')
				{
					$paymentdata = array(['amount'=>$fee]);
					$type = 'unknown';
					$requestitem = null;

					if($requestinfo)
					{
						$requestinfo['customerid'] = $user->id;
						$requestitem = RequestInfo::create($requestinfo);

						array_push($paymentdata,['id'=>$requestitem->id,'amount'=>$subtotal]);
						$type = 'request';
					}
					else if($products)
					{
						$paymentdata = $products;
						$type = 'product';
					}
					else
					{
						array_push($paymentdata,['amount'=>$subtotal]);
					}

					PaymentHistory::create([
						'productinfo'=>json_encode($paymentdata),
						'price'=>$subtotal + $fee,
						'methodid'=>$paymentmethod,
						'type'=>$type,
						'creater'=>$user->id
					]);


					return array('success'=>true,'message'=>$requestitem != null?'You have successfully create request for ' . $requestitem->influencerinfo->fullname:'You have successfully purchase products','type'=>$type);
				}
				else
				{
					return array('success'=>false,'message'=>$charge['status']);
				}
			}
			catch(Exception $e)
			{
				return array('success'=>false,'message'=>$e->getMessage());
			}
			catch(\Cartalyst\Stripe\Exception\CardErrorException $e)
			{
				return array('success'=>false,'message'=>$e->getMessage());
			}
			catch(\Cartalyst\Stripe\Exception\MissingParameterException $e)
			{
				return array('success'=>false,'message'=>$e->getMessage());
			}	
		}
		else
		{
			return array('success'=>false,'message'=>'You have to signin first');
		}
	}

	public function getrequest(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();

		if($user)
		{
			$requests = RequestInfo::where('customerid',$user->id)->get();
			$array = array();
			foreach ($requests as $key => $value) {
				if($value->influencerinfo)
				{
					$value->influencerinfo->reviews = Review::where('influencerid',$value->influencer)->get();
					array_push($array,$value);
				}
			}

			return ['success'=>true,'data'=>$array];
		}
		else
		{
			return ['success'=>false];
		}
	}


}