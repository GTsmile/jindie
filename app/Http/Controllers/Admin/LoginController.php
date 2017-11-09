<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
class LoginController extends Controller{
    public function check(Request $request)
    {
    	$where['user']=$request->input('username');
    	$where['password']=md5($request->input('password'));
        $result = Admin::check_login($where['user']);
        if(!$result){
            return responseToJson(1,'error','用户不存在');
        }else if($result->user == $where['password']){
            return responseToJson(0,'success','登陆成功');
        }
    }
}