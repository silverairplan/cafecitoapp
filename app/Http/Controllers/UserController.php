<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use App\Model\User;
use App\Model\Review;
use Illuminate\Support\Str;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;
use App\Mail\forgotpassword;

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
			$userinfo = User::create($user);
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

	public function loginwithsocial(Request $request)
	{
		$user = $request->input('user');
		$userinfo = User::where('email',$user['email'])->first();
		$credential = Str::random(60);

		if($userinfo)
		{
			$userinfo->update(['token'=>$credential]);
		}
		else
		{
			$user['token'] = $credential;
			$userinfo = User::create($user);
		}
		return array('success'=>true,'token'=>$credential,'userinfo'=>$userinfo);
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
			$reviews = Review::where('influencerid',$userinfo->id)->orderBy('created_at','DESC')->get();
			$list = array();
			foreach ($reviews as $review) {
				if($review->customer && $review->influencer)
				{
					$review->customer->reviews = count(Review::where('customerid',$review->customerid)->get());
					array_push($list, $review);
				}
			}
			return array('success'=>true,'reviews'=>$list);
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
			$influencers = User::where('role','influencer')->where('active',true)->orderBy('created_at','DESC')->get();
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
				if($user && $userinfo->id != $user->id)
				{
					return array('success'=>false,'message'=>'Email is already exist');
				}
			}

			if(isset($data['username']))
			{
				$user = User::where('username',$data['username'])->first();
				if($user && $userinfo->id != $user->id)
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

	public function submitreview(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();
		$reviewinfo = $request->input('reviewinfo');

		if($user)
		{

			$reviewinfo['customerid'] = $user->id;
			
			$review = Review::where('influencerid',$reviewinfo['influencerid'])->where('customerid',$reviewinfo['customerid'])->first();

			if(!$review)
			{
				$review = Review::create($reviewinfo);	
			}
			else
			{
				$review->update($reviewinfo);
			}

			if($review->customer && $review->influencer)
			{
				return ['success'=>true,'reviewinfo'=>$review];	
			}
			else
			{
				return ['success'=>false];
			}
			
		}
		else
		{
			return ['success'=>false];
		}
	}

	public function getreview(Request $request)
	{
		$token = $request->input('token');
		$id = $request->input('userid');
		$user = User::where('token',$token)->first();
		if($user)
		{
			$reviews = Review::where('influencerid',$id)->orderBy('created_at','DESC')->get();

			$list = array();

			foreach ($reviews as $review) {
				if($review->customer && $review->influencer)
				{
					$customerreviews = Review::where('customerid',$review->customerid)->get();
					$review->customer->reviews = count($customerreviews);
					array_push($list,$review);
				}
			}
			return ['success'=>true,'reviews'=>$list];
		}
		else
		{
			return ['success'=>false];
		}
	}

	public function submitreply(Request $request,NotificationService $notificationservice)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();
		$id = $request->input('id');
		$reply = $request->input('reply');

		if($user)
		{
			$review = Review::where('id',$id)->first();
			$review->update(
				['reply'=>$reply]
			);

			$notification = Notification::create(
				[
					'title'=>$user->fullname . ' has replied for ' . $review->customer->fullname . ' review',
					'description'=>$user->fullname . ' has replied with "' . $reply . '"',
					'createdby'=>$review->customerid
				]
			);

			$notificationservice->sendmessage($notification->title,$notification->description,$review->customer->noti_token);

			return ['success'=>true];
		}
		else
		{
			return ['success'=>false];
		}
	}

	public function notifytoken(Request $request)
	{
		$token = $request->input('token');
		$notifytoken = $request->input('notifytoken');

		$user = User::where('token',$token)->first();

		if($user)
		{
			$user->update(['noti_token'=>$notifytoken]);
			return ['success'=>true];
		}
		else
		{
			return ['success'=>false];
		}
	}

	public function forgotpassword(Request $request)
	{
		$email = $request->input('email');

		$user = User::where('email',$email)->first();

		if($user)
		{
			Mail::to($user->email)->send(new forgotpassword($user));
		}
		else
		{
			return ['success'=>false,'message'=>"This user doesn't exist"];
		}
	}

	public function identification_upload(Request $request)
	{
		$email = $request->input('email');
		$user = User::where('email',$email)->first();

		if($user)
		{
			$data = array();
			$profile = $request->file('profile');
			$idcard = $request->file('idcard');
			if($profile)
			{
				$profileupload = "public/profile";
				$profile->move($profileupload,$profile->getClientOriginalName());
				$data['profile'] = $profileupload . '/' . $profile->getClientOriginalName();
			}

			if($idcard)
			{
				$idupload = "public/idcard";
				$idcard->move($idupload,$idcard->getClientOriginalName());
				$data['idpath'] = $idupload . '/' . $idcard->getClientOriginalName();
			}

			$user->update($data);
			return ['success'=>true,'message'=>'You have successfully upload your identification'];
		}
		else
		{
			return ['success'=>false];
		}
	}
}

?>