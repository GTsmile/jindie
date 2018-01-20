<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DB;

class TimeController extends Controller
{
    public static function start()
    {
        //echo 'hello world!<br>';
        //dd('hello world!');
        //dump('hell world!');
        //return 'hello world!';
        $result = DB::table('system_users')->select('sex')->where('id',2)->first();
        dump($result);
    }
}