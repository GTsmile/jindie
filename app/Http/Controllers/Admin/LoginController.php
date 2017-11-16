<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DB;
use Dotenv\Validator;
use Gregwar\Captcha\CaptchaBuilder;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function check(Request $request)
    {
        $rules = ['checkCode' => 'required|captcha'];
        $validator = \Validator::make($request->all(), $rules,['captcha'=>"验证码错误"]);

        if (!$validator->fails()) {
            //用户输入验证码正确
            $where['loginid'] = $request->input('username');
            $where['password'] = md5($request->input('password'));
            $select_rows = DB::table('system_users')->where($where)->first();
            if ($select_rows) {
                return responseToJson(0, "success", 'true');
            }
            return responseToJson(1, "error", 'false');
        } else {
            //用户输入验证码错误
            return responseToJson(2, "checkCodeError", 'false');
        }
    }

    public function captcha($tmp)
    {
        return captcha_src("flat");
    }

}