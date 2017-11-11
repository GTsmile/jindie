<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DB;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller{
    public function check(Request $request){

//        $usernane =  $request->input("username");
//        return responseToJson(0,"success",$usernane);
    	$where['loginid']=$request->input('username');
    	$where['password']=md5($request->input('password'));
        $select_rows = DB::table('system_users')->where($where)->first();
        if($select_rows){
           return responseToJson(0,"success",'true');
        }
        return responseToJson(0,"success",'false');
    }
    public function captcha($tmp){
         return captcha_src("flat");
    }
}