<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Model\User;
use App\Model\Message;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
	public function __construct()
	{

	}

	public function create(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();
		$message = $request->input('message');
		$attached = $request->file('attached');
		$livestreamid = $request->input('livestreamid');
		$to = $request->input('to');

		if($user)
		{
			$array = array('from'=>$user->id);
			if($attached)
			{
				$uploaded_attached = "public/attach";
				$attached->move($uploaded_attached,$attached->getClientOriginalName());
				$array['attached'] = $uploaded_attached . '/' . $attached->getClientOriginalName();
			}

			if($message)
			{
				$array['message'] = $message;
			}

			if($livestreamid)
			{
				$array['livestreamid'] = $livestreamid;
				$array['status'] = 'livestream';
			}

			if($to)
			{
				$array['to'] = $to;
				$array['status'] = 'pending';
			}

			$message = Message::create($array);
			$message->from = $message->fromuser;
			$message->to = $message->touser;

			return array('success'=>true,'message'=>$message);
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function getlivestreammessage(Request $request)
	{
		$livestreamid = $request->input('livestreamid');
		$messages = Message::where('livestreamid',$livestreamid)->orderBy('created_at','ASC')->get();

		foreach ($messages as $key => $message) {
			$messages[$key]->from = $message->fromuser;
			$messages[$key]->to = $message->touser;
		}

		return array('success'=>true,'message'=>$messages);
	}

	public function getusers(Request $request)
	{
		$user = $request->input('user');
		$token = $request->input('token');

		$userinfo = User::where('token',$token)->orderBy('created_at','DESC')->first();

		$userlist = array();
		$idlist = array();
		$users = array();
		if($user)
		{
			$users = User::where('fullname','like', '%' . $user . '%')->orderBy('created_at','DESC')->get();
		}

		if($userinfo)
		{
			$messages = Message::where('to',$userinfo->id)->orwhere('from',$userinfo->id)->orderBy('created_at','DESC')->get();
			foreach ($messages as $message) {
				if($message->status != 'livestream')
				{
					if(($message->to == $userinfo->id && $message->fromuser) && !in_array($message->from, $idlist))
					{
						$messagearray = Message::where('from',$message->from)->where('to',$message->to)->where('status','active')->orderBy('created_at','DESC')->get();
						$fromuser = $message->fromuser;
						if(count($messagearray) > 0)
						{
							$fromuser->is_invite = false;	
						}
						else
						{
							$fromuser->is_invite = true;
						}
						
						$fromuser->message = $message->message;
						$fromuser->status = $message->status;
						$fromuser->sended = $message->created_at;
						array_push($idlist,$fromuser->id);
						array_push($userlist,$fromuser);
					}
					else if(($message->from == $userinfo->id && $message->touser) && !in_array($message->to, $idlist))
					{
						$touser = $message->touser;
						$touser->message = $message->message;
						$touser->status = $message->status;
						$touser->sended = $message->created_at;
						$touser->is_invite = false;
						array_push($idlist,$touser->id);
						array_push($userlist,$touser);
					}	
				}
				
			}

			if($user)
			{
				foreach ($users as $useritem) {
					if(!in_array($useritem->id,$idlist) && $userinfo->id != $useritem->id)
					{
						array_push($userlist,$useritem);
						array_push($idlist,$useritem->id);
					}
				}
			}

			return array('success'=>true,'data'=>$userlist);
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function getmessages(Request $request)
	{
		$user = $request->input('user');
		$token = $request->input('token');

		$userinfo = User::where('token',$token)->first();

		if($userinfo)
		{
			Message::where('to',$userinfo->id)->where('from',$user)->where('status','!=','livestream')->update(['status'=>'active']);
			$message = Message::where(function($query) use ($userinfo,$user){
				$query->where('from',$userinfo->id)->where('to',$user)->where('status','!=','livestream');
			})->orWhere(function($query) use ($userinfo,$user){
				$query->where('to',$userinfo->id)->where('from',$user)->where('status','!=','livestream');
			})->orderBy('created_at','DESC')->get();

			

			return array('success'=>true,'message'=>$message);
		}	
		else
		{
			return array('success'=>false);
		}
	}

	public function accept(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();
		$userid = $request->input('userid');

		if($user)
		{
			Message::where('to',$user->id)->where('from',$userid)->where('status','!=','active')->update(['status'=>'active']);
			return ['success'=>true];
		}
		else
		{
			return ['success'=>false];
		}
	}
}