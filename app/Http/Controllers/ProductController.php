<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Model\Product;
use App\Model\User;

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
			if($user->role == 'influencer')
			{
				return array('success'=>true,'product'=>Product::where('creater',$user->id)->get());
			}
			else
			{
				return array('success'=>true,'product'=>Product::all());
			}
		}
	}

	public function deleteproduct(Request $request)
	{
		$token = $request->input('token');
		$id = $request->input('id');

		$product = Product::where('id',$id)->first();
	}
}