<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;

class IndexController extends Controller{

    public function getUser(){
        $select_row=DB::table("system_users")->get();
        dump($select_row);
    }


}