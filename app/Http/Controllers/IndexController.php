<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class IndexController extends Controller
{
    public function index(){
    	$select_rows = DB::table('system_users')->get();
    	return $select_rows;
    }
}
