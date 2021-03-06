<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/user/create",'UserController@create');
Route::post("/user/login",'UserController@login');
Route::post("/user/loginwithsocial",'UserController@loginwithsocial');
Route::post('/user/set','UserController@setuserinfo');
Route::get("/user/get","UserController@get");
Route::get("/user/review",'UserController@getreviews');
Route::get("/user/influencer",'UserController@getinfluencers');
Route::post('/user/update','UserController@update');
Route::post('/user/updatepassword','UserController@updatepassword');
Route::post('/user/notifytoken','UserController@notifytoken');
Route::post('/user/forgot','UserController@forgotpassword');
Route::post('/user/idcard','UserController@identification_upload');

Route::post("/podcast/create",'PodcastController@addfeed');
Route::get('/podcast/getbyid','PodcastController@getpodcastbyid');
Route::get('/podcast/get','PodcastController@getpodcasts');

Route::get('/episode/get','PodcastController@getepisode');

Route::post('/video/create','VideoController@create');
Route::post('/video/like','VideoController@likevideo');
Route::post('/video/delete','VideoController@deletevideo');
Route::get('/video/islike','VideoController@islike');
Route::get('/video/get','VideoController@getvideos');
Route::get('/video/user','VideoController@getuservideos');


Route::post('/product/create','ProductController@create');
Route::post('/product/update','ProductController@update');
Route::get('/product/get','ProductController@getproducts');
Route::post('/product/delete','ProductController@deleteproduct');
Route::post('/product/cart/create','ProductController@addtocart');
Route::get('/product/cart/get','ProductController@getcart');
Route::post('/product/cart/delete','ProductController@deletecart');
Route::post('/product/cart/update','ProductController@updatecart');
Route::post('/product/cart/clear','ProductController@clearcart');


Route::post('/card/create','PaymentController@create');
Route::get('/card/get','PaymentController@getpaymentmethod');
Route::post('/card/payment','PaymentController@payment');
Route::get('/card/history','PaymentController@history');
Route::get('/request/get','PaymentController@getrequest');
Route::post('/request/status','PaymentController@requeststatus');

Route::post('/livestream/create','LiveStreamController@create');
Route::get('/livestream/get','LiveStreamController@get');
Route::post('/livestream/getuser','LiveStreamController@getusers');

Route::post('/message/create','MessageController@create');
Route::get('/message/livestream','MessageController@getlivestreammessage');
Route::get("/message/user",'MessageController@getusers');
Route::get('/message/get','MessageController@getmessages');
Route::post('/message/accept','MessageController@accept');
Route::post('/message/reject','MessageController@reject');

Route::get('/notification/get','NotificationController@getnotification');
Route::get('/post/get','PostController@getpost');
Route::get('/post/settings','PostController@getsetting');

Route::post('/review/submit','UserController@submitreview');
Route::get('/review/get','UserController@getreview');
Route::post('/review/reply','UserController@submitreply');

Route::get('/notification/get','ProductController@notification');
Route::post('/notification/send','ProductController@sendmessage');

Route::post('/feedback/create','FeedbackController@createfeedback');




