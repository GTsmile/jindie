<?php
 namespace App\Models;

 use Illuminate\Database\Eloquent\Model;

 class Admin extends Model
 {
    public static function check_login($user)
    {
        dump(132);
        $result = DB::table('system_users')->where('')->first();
    }
 }