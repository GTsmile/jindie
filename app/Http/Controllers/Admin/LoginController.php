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
        }else if($result->password == $where['password']){
            session('user',$result);
            return responseToJson(0,'success','登陆成功');
        }else if($result->password != $where['password']){
            return responseToJson(1,'error','密码错误');
        }else {
            return responseToJson(2,'error','用户名不存在');
        }

    }
    public function logout(Request $request)
    {
        session(null);
        return responseToJson(0,'success','退出成功');
    }
    public function test()
    {

    }
}