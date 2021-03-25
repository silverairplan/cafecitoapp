<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Model\Notification;
use App\Model\User;

class NotificationController extends Controller
{
	public function __construct()
	{

	}

	public function getnotification(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();

		if($user)
		{
			$notifications = Notification::where('createdby',$user->id)->orderBy('created_at','DESC')->get();

			return array('success'=>true,'notification'=>$notifications);
		}
		else
		{
			return array(
				'success'=>false
			);
		}
	}
}