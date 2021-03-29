<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use App\Model\User;
use App\Model\Review;
use Illuminate\Support\Str;

class UserController extends Controller
{
	public function __construct()
	{

	}

	public function create(Request $request)
	{
		$user = $request->input();

		$userinfo = User::where('email',$user['email'])->first();
		
		if(!$userinfo)
		{
			$userinfo = User::where('username',$user['username'])->first();
		}

		if($user['role'] == 'customer')
		{
			$user['active'] = true;
		}

		if($userinfo)
		{
			return array('success'=>false,'message'=>'This user is already registered');
		}
		else
		{
			$user['password'] = bcrypt($user['password']);
			$userinfo = new User($user);
			$userinfo->save();
			return array('success'=>true);
		}
	}

	public function login(Request $request)
	{
		$user = $request->input();

		$userinfo = User::where('email',$user['email'])->first();

		if(!$userinfo)
		{
			$userinfo = User::where('username',$user['email'])->first();
		}

		if($userinfo)
		{
			if(Hash::check($user['password'],$userinfo->password))
			{
				$credential = Str::random(60);
				$userinfo->update(['token'=>$credential]);
				return array('success'=>true,'token'=>$credential,'userinfo'=>$userinfo);
			}
			else
			{
				return array('success'=>false,'message'=>'Password is not correct');
			}
		}
		else
		{
			return array('success'=>false,'message'=>'User with this email or username is not exist');
		}
	}

	public function get(Request $request)
	{
		$token = $request->input('token');
		$userinfo = User::where('token',$token)->first();

		if($userinfo)
		{
			return array('success'=>true,'userinfo'=>$userinfo);
		}	
		else
		{
			return array('success'=>false);
		}
	}

	public function setuserinfo(Request $request)
	{
		$token = $request->input('token');
		$userinfo = $request->input('userinfo');

		$user = User::where('token',$token)->first();

		if($user)
		{
			$user->update($userinfo);
			return array('success'=>true);
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function getreviews(Request $request)
	{
		$token = $request->input('token');
		$userinfo = User::where('token',$token)->first();

		if($userinfo)
		{
			$reviews = Review::where('influencerid',$userinfo->id)->get();
			return array('success'=>true,'reviews'=>$reviews);
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function getinfluencers(Request $request)
	{
		$token = $request->input('token');
		$userinfo = User::where('token',$token)->first();

		if($userinfo)
		{
			$influencers = User::where('role','influencer')->where('active',true)->get();
			$array = array();

			foreach ($influencers as $influencer) {
				$reviews = Review::where('influencerid',$influencer->id)->get();
				$influencer->reviews = $reviews;
			}

			return array('success'=>true,'data'=>$influencers);
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function update(Request $request)
	{
		$data = $request->input();
		$userinfo = User::where('token',$data['token'])->first();

		if($userinfo)
		{
			$profile = $request->file('profile');

			if(isset($data['email']))
			{
				$user = User::where('email',$data['email'])->first();
				if($userinfo->id != $user->id)
				{
					return array('success'=>false,'message'=>'Email is already exist');
				}
			}

			if(isset($data['username']))
			{
				$user = User::where('username',$data['username'])->first();
				if($userinfo->id != $user->id)
				{
					return array('success'=>false,'message'=>'Username is already exist');
				}
			}

			if($profile)
			{
				$profileupload = "public/profile";
				$profile->move($profileupload,$profile->getClientOriginalName());
				$data['profile'] = $profileupload . '/' . $profile->getClientOriginalName();
			}

			$userinfo->update($data);

			return array('success'=>true,'user'=>$userinfo);
		}
		else
		{
			return array('success'=>false);
		}
	}


	public function updatepassword(Request $request)
	{
		$token = $request->input('token');
		$data = $request->input('data');

		$user = User::where('token',$token)->first();

		if($user)
		{
			if(Hash::check($data['old'],$user->password))
			{
				$user->update([
					'password'=>bcrypt($data['new'])
				]);
				return array('success'=>true);
			}
			else
			{
				return array('success'=>false,'message'=>'Current Password is not correct');
			}
		}
		else
		{
			return array('success'=>false);
		}
	}
}

?>