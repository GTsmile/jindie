<?php
 namespace App\Models;

 use Illuminate\Database\Eloquent\Model;
use DB;
 class Admin extends Model
 {
    public static function check_login($user)
    {
        $result = DB::table('system_users')->where('loginid',$user)->first();
        return $result ? $result : 0;
    }
 }