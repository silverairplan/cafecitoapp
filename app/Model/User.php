<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //
    protected $table = "user";
    protected $fillable = ['username','email','password','fullname','role','token','profile','bio','birthdate','address','city','state','country','post_code','description','videoprice','cafecitoprice','job','active','isonline','direct_chat','share_location','notification','latitude','longitude','noti_token','idpath'];
}
