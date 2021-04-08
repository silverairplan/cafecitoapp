<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Model\Feedback;
use App\Model\User;

class FeedbackController extends Controller
{
	public function __construct()
	{

	}

	public function createfeedback(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();

		if($user)
		{
			$data = $request->input('data');
			$data['userid'] = $user->id;
			Feedback::create($data);
			return ['success'=>true];
		}
		else
		{
			return ['success'=>false];
		}
	}
}