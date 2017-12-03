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
        $errCount=$request->errCount;
        if($request->isMethod('post')){
            if (session('captcha') == $captcha||$errCount<=3) {
                //用户输入验证码正确
                $password = md5($request->password);
                $result = Admin::check_login($user);
                if($result){
                    if($password == $result->password){
                        session(['user' => $result]);
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
        $result = $request->session()->flush();
        return responseToJson(0,'success','退出成功');
    }
    public function test()
    {
        // dump(get_session_user_id());
        $userOfPosition=DB::reconnect('sqlsrv')->table('system_position')
        ->join('system_positionmember','system_position.id','=','system_positionmember.position_id')
        ->selectRaw("max(system_positionmember.user_id) as user_id,system_position.id")
        ->groupBy('system_positionmember.position_id')
        ->get();
       
        // $userOfPosition=DB::reconnect('sqlsrv')->table('system_user_role')
        // ->

        dump($userOfPosition);
    }
}