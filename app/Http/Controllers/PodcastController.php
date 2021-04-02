<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Vedmant\FeedReader\Facades\FeedReader;
use App\Model\User;
use App\Model\Podcast;
use App\Model\Episode;

class PodcastController extends Controller
{
	public function __construct()
	{

	}

	public function addfeed(Request $request)
	{
		$url = $request->input('url');
		$token = $request->input('token');

		$user = User::where('token',$token)->first();

		if($user)
		{
			$f = FeedReader::read($url);

			$author = '';

			if($f->get_author())
			{
				$author = $f->get_author()->get_name();
			}
			$podcast = new Podcast(
				[
					'title'=>$f->get_title(),
					'description'=>$f->get_description(),
					'image'=>$f->get_image_url(),
					'feedurl'=>$url,
					'creator'=>$user->id,
					'author'=>$author
				]
			);

			$podcast->save();

			$items = $f->get_items();

			$list = array();

			foreach ($items as $item) {
				$episode = new Episode(
					[
						'title'=>$item->get_title(),
						'description'=>$item->get_description(),
						'audiourl'=>$item->get_permalink(),
						'podcast'=>$podcast->id,
						'duration'=>$item->get_enclosure()->get_duration(true)
					]
				);
				
				$episode->save();
			}

			return [
				'success'=>true,
				'id'=>$podcast->id
			];

		}
		else
		{
			return array('success'=>false);
		}

		
	}

	public function getpodcastbyid(Request $request)
	{
		$id = $request->input('id');
		$token = $request->input('token');

		$user = User::where('token',$token)->first();

		if($user)
		{	
			$podcasts = Podcast::where('id',$id)->first();
			$episodes = Episode::where('podcast',$id)->orderBy('created_at','DESC')->get();

			$list = array();

			foreach ($episodes as $episode) {
				array_push($list,array(
					'id'=>$episode->id,
					'title'=>$episode->title,
					'description'=>$episode->description,
					'audiourl'=>$episode->audiourl,
					'duration'=>$episode->duration
				));
			}

			return array('success'=>true,'podcast'=>$podcasts,'episodes'=>$list);

		}
		else
		{
			return array('success'=>false);
		}
	}

	public function getpodcasts(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();

		if($user)
		{
			$list = array();
			$podcasts = Podcast::orderBy('created_at','DESC')->get();
			return array('success'=>true,'data'=>$podcasts);
		}
		else
		{
			return array('success'=>false);
		}
	}

	public function getepisode(Request $request)
	{
		$token = $request->input('token');
		$user = User::where('token',$token)->first();
		$id = $request->input('id');
		if($user)
		{
			$episode = Episode::where('id',$id)->first();
			$data = array(
				'id'=>$episode->id,
				'title'=>$episode->title,
				'audiourl'=>$episode->audiourl,
				'description'=>$episode->description,
				'duration'=>$episode->duration,
				'podcast'=>$episode->podcastinfo
			);
			return array('success'=>true,'data'=>$data);
		}
		else
		{
			return array('success'=>false);
		}

	}

	

}