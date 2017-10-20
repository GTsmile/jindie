<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
class LoginController extends Controller{
    public function check(Request $request){
    	$where['loginid']=$request->input('username');
    	$where['password']=md5($request->input('password'));
        $select_rows = DB::table('system_users')->where($where)->first();
        if($select_rows){
        	return response()->json("true");
        }
    	return response()->json("false");
    }
}