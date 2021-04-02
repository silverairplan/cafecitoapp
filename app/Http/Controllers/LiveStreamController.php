<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Model\User;
use App\Model\LiveStream;

class LiveStreamController extends Controller
{
	public function __construct()
	{

	}

	public function create(Request $request)
	{
		$data = $request->input();

		$user = User::where('token',$data['token'])->first();

		if($user)
		{
			$array = array(
				'title'=>$data['title'],
				'memberlimit'=>$data['memberlimit'],
				'livemethod'=>$data['livemethod'],
				'creater'=>$user->id
			);
			
			$coverimage = $request->file('coverimage');
			$upload = "public/livestream";
			$coverimage->move($upload,$coverimage->getClientOriginalName());

			$array['coverimage'] = $upload . '/' . $coverimage->getClientOriginalName();
			$livestream = LiveStream::create($array);

			$livestream->creater = $livestream->user;
			
			return array('success'=>true,'livestream'=>$livestream);
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function get(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();

		if($user)
		{
			$livestreams = LiveStream::orderBy('created_at','DESC')->get();
			foreach ($livestreams as $key => $livestream) {
				$livestreams[$key]->creater = $livestream->user;
			}

			return array('success'=>true,'livestream'=>$livestreams);
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function getusers(Request $request)
	{
		$users = $request->input('users');
		$userlist = array();
		//var_dump($request->input());exit;
		foreach ($users as $user) {
			$userinfo = User::where('id',$user)->first();
			array_push($userlist, $userinfo);
		}

		return array('success'=>true,'users'=>$userlist);
	}
}