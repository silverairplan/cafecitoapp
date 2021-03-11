<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

use App\Model\User;
use App\Model\Video;
use App\Model\Likes;

class VideoController extends Controller
{
	public function __construct()
	{

	}

	public function create(Request $request)
	{
		$title = $request->input('title');
		$token = $request->input('token');

		$user = User::where('token',$token)->first();

		if($user)
		{
			$coverimage = $request->file('coverimage');
			$upload_coverimage = "public/video/image";
			$coverimage->move($upload_coverimage,$coverimage->getClientOriginalName());

			$source = $request->file('video');
			$upload_source = "public/video/video";

			$source->move($upload_source,$source->getClientOriginalName());



			$video = Video::create([
				'title'=>$title,
				'creater'=>$user->id,
				'coverimage'=>'video/image/' . $coverimage->getClientOriginalName(),
				'source'=>'video/video/' . $source->getClientOriginalName()
			]);

			return array('success'=>true);

		}
		else
		{
			return array('success'=>false);
		}
	}

	public function getvideos(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();
		$me = $request->input('me');
		if($user)
		{
			$videos = Video::all();

			if($me || $user->role == 'influencer')
			{
				$videos = Video::where('creater',$user->id)->get();
			}

			$array = array();
			foreach ($videos as $video) {
				$like = count(Likes::where('liked',$video->id)->where('type','VIDEO')->get());
				array_push($array,array(
					'id'=>$video->id,
					'title'=>$video->title,
					'coverimage'=>$video->coverimage,
					'creater'=>$video->user,
					'source'=>$video->source,
					'likes'=>$like
				));
			}

			return array('success'=>true,'video'=>$array);

		}
		else
		{
			return array('success'=>false);
		}
	}

	public function likevideo(Request $request)
	{
		$token = $request->input('token');
		$id = $request->input('id');

		$user = User::where('token',$token)->first();

		if($user)
		{
			$like = Likes::where('liked',$id)->where('type','VIDEO')->where('user',$user->id)->first();

			if($like)
			{
				$like->delete();
				return array('success'=>true,'liked'=>false);
			}
			else
			{
				Likes::create([
					'liked'=>$id,
					'type'=>'VIDEO',
					'user'=>$user->id
				]);

				return array('success'=>true,'liked'=>true);
			}
		}
		else
		{
			return array('success'=>true);
		}
	}

	public function islike(Request $request)
	{
		$token = $request->input('token');
		$id = $request->input('id');

		$user = User::where('token',$token)->first();

		if($user)
		{
			$likes = Likes::where('user',$user->id)->where('liked',$id)->first();

			if($likes)
			{
				return array('success'=>true,'liked'=>true);
			}
			else
			{
				return array('success'=>true,'liked'=>false);
			}
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function deletevideo(Request $request)
	{
		$token = $request->input('token');
		$id = $request->input('id');

		$user = User::where('token',$token)->first();
		$video = Video::where('id',$id)->first();

		if($user && $video)
		{
			if($video->creater == $user->id)
			{
				$video->delete();
				return array('success'=>true);
			}
			else
			{
				return array('success'=>false);
			}
		}
		else
		{
			return array('success'=>false);
		}
	}
}