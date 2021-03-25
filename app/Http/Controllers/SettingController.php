<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Model\Setting;

class SettingController extends Controller
{
	public function __construct()
	{

	}

	public function getsetting(Request $request)
	{
		$settings = Setting::all();
		$set = array();
		foreach ($settings as $setting) {
			$set[$setting->keyword] = $setting->value;
		}

		return $set;
	}
}