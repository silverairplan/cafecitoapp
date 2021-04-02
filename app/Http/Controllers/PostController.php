<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Model\Post;
use App\Model\Setting;

class PostController extends Controller
{
	public function __construct()
	{

	}

	public function getpost(Request $request)
	{
		$type = $request->input('type');
		$posts = Post::where('type',$type)->orderBy('created_at','DESC')->get();

		return array(
			'post'=>$posts
		);
	}

	public function getsetting(Request $request)
	{
		$settings = Setting::all();

		$array = array();

		foreach ($settings as $setting) {
			$array[$setting->keyword] = $setting->value;
		}

		return $array;
	}
}