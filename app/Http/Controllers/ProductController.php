<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Model\Product;
use App\Model\User;
use App\Model\Cart;
use App\Model\Notification;
use App\Services\NotificationService;

class ProductController extends Controller
{
	public function __construct()
	{

	}

	public function create(Request $request)
	{
		$token = $request->input('token');

		$user = User::where('token',$token)->first();

		if($user && $user->role == 'influencer')
		{
			$image = $request->file('image');
			$upload = "public/product";
			$insertinfo = array(
				'title'=>$request->input('title'),
				'price'=>$request->input('price'),
				'description'=>$request->input('description'),
				'creater'=>$user->id
			);

			if($image)
			{
				$image->move($upload,$image->getClientOriginalName());

				$insertinfo['image'] = $upload . '/' . $image->getClientOriginalName();
			}

			$product = Product::create($insertinfo);
			$product->creater = $product->createrinfo;

			return array('success'=>true,'product'=>$product);
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function update(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token);
		$id = $request->input('id');
		$title = $request->input('title');
		$description =$request->input('description');
		$price = $request->input('price');

		$product = Product::where('id',$id);
		if($user)
		{
			$updateinfo = array(
				'title'=>$title,
				'description'=>$description,
				'price'=>$price
			);

			$image = $request->file('image');
			if($image)
			{
				$upload = "public/product";
				$image->move($upload,$image->getClientOriginalName());
				$updateinfo['image'] = $upload . '/' . $image->getClientOriginalName();
			}

			$product->update($updateinfo);
			$product->creater = $product->createrinfo;
			return array('success'=>true,'product'=>$product);
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function getproducts(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();

		if($user)
		{
			$products = [];
			if($user->role == 'influencer')
			{
				$products = Product::where('creater',$user->id)->orderBy('created_at','DESC')->get();
			}
			else
			{
				$products = Product::orderBy('created_at','DESC')->get();
			}

			foreach ($products as $key => $product) {
				$products[$key]->creater = $product->createrinfo;
			}

			return array('success'=>true,'product'=>$products);
		}
	}

	public function deleteproduct(Request $request)
	{
		$token = $request->input('token');
		$id = $request->input('id');
		$user = User::where('token',$token)->first();

		if($user)
		{
			$product = Product::where('id',$id)->where('creater',$user->id)->first();	
			$product->delete();
			$products = Product::where('creater',$user->id)->orderBy('created_at','DESC')->get();
			foreach ($products as $key => $product) {
				$products[$key]->creater = $product->createrinfo;
			}

			return array('success'=>true,'products'=>$products);
		}
		else
		{
			return array('success'=>false);
		}
		
	}

	public function addtocart(Request $request)
	{
		$token = $request->input('token');
		$cartinfo = $request->input('cartinfo');

		$user = User::where('token',$token)->first();

		if($user)
		{
			$cartinfo['creater'] = $user->id;
			Cart::create($cartinfo);

			return array('success'=>true);
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function getcart(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();
		
		if($user)
		{
			$carts = Cart::where('creater',$user->id)->orderBy('created_at','DESC')->get();
			$array = array();
			foreach ($carts as $key => $cart) {
				if($cart->product)
				{
					$cart->product = $cart->product;
					$cart->product->createrinfo = $cart->product->createrinfo;
					array_push($array,$cart);
				}
				
			}

			return array('success'=>true,'carts'=>$array);
		}
		else
		{
			return array('success'=>false);
		}	
	}

	public function deletecart(Request $request)
	{
		$token = $request->input('token');
		$id = $request->input('id');
		$user = User::where('token',$token)->first();

		if($user)
		{
			Cart::where('id',$id)->delete();
			$carts = Cart::where('creater',$user->id)->orderBy('created_at','DESC')->get();
			$array = array();
			foreach ($carts as $key => $cart) {
				if($cart->product)
				{
					$cart->product = $cart->product;
					$cart->product->createrinfo = $cart->product->createrinfo;
					array_push($array,$cart);
				}
				
			}

			return array('success'=>true,'carts'=>$array);
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function updatecart(Request $request)
	{
		$token = $request->input('token');
		$id = $request->input('id');
		$cartinfo = $request->input('cartinfo');
		$user = User::where('token',$token)->first();

		if($user)
		{
			Cart::where('id',$id)->update($cartinfo);
			$carts = Cart::where('creater',$user->id)->orderBy('created_at','DESC')->get();
			$array = array();
			foreach ($carts as $key => $cart) {
				if($cart->product)
				{
					$cart->product = $cart->product;
					$cart->product->createrinfo = $cart->product->createrinfo;
					array_push($array,$cart);
				}
				
			}

			return array('success'=>true,'carts'=>$array);
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function clearcart(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();

		if($user)
		{
			Cart::where('creater',$user->id)->delete();
			return array('success'=>true);
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function notification(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();

		if($user)
		{
			$notifications = Notification::where('createdby',$user->id)->orderBy('created_at','DESC')->get();
			Notification::where('createdby',$user->id)->update(['is_read'=>1]);
			return ['success'=>true,'notifications'=>$notifications];
		}
		else
		{
			return ['success'=>false];
		}
	}

	public function sendmessage(Request $request,NotificationService $notificationservice)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();

		if($user)
		{
			$notificationservice->sendmessage("Test","This is test",$user->noti_token);
		}
		else
		{
			return ['success'=>false];
		}
	}
}