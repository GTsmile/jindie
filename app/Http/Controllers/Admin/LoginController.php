<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DB;
use App\Models\Admin;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;
use Session;
class LoginController extends Controller
{
    public function check(Request $request)
    {
        $user = $request->user;
        $captcha = $request->captcha;
        if($request->isMethod('post')){
            if (session('captcha') == $captcha) {
                //用户输入验证码正确
                $password = md5($request->password);
                $result = Admin::check_login($user);
                if($user){
                    if($password == $result->password){
                        session('id',$result->id);
                        session('user',$result);
                        return responseToJson(0,'success','登录成功');
                    }else{
                        return responseToJson(1,'error','密码错误');
                    }
                }else{
                    return responseToJson(2,'error','该用户不存在');
                }
            } else {
                //用户输入验证码错误
                return responseToJson(3, $captcha, session('captcha'));
            }
        }
    }

    /**
     * 获取验证码
     */
    public function get_captcha()
    {
        $builder=new CaptchaBuilder();
        $builder ->build(100,40);
        $phrase = $builder->getPhrase();  // 获取验证码内容
        Session::flash('captcha', $phrase);
        return response($builder->output())->header('Content-type','image/jpeg');
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