<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DB;

class TestController extends Controller
{
    public static function start()
    {

        /*$syn = new LoginController();
        $syn->tongbuERP();
        dump('success');*/
        /*$result = DB::table('system_users')->select('sex')->where('id',2)->get();
        dump($result);*/
        $tb = new LoginController();
        $result = $tb->tongbuERP();
        dump($result);
    }
}