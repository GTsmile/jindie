<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

include 'admin.php';
include 'api.php';


Route::get('/', function () {
    return view('login');
});
Route::post('/check','Admin\LoginController@check');
Route::get('/captcha/get','Admin\LoginController@get_captcha');
Route::post('/logout','Admin\LoginController@logout');

Route::get('/login', function () {
    return view('login');
});

Route::group(['middleware' => 'login.check'],function (){
    Route::get('/', function () {
        return view('index');
    });

});

Route::group(['middleware' => 'login.check'],function (){
});
Route::get('/captcha/{tmp}', 'Admin\LoginController@captcha');
/*Route::post('/login','Admin\LoginController@check');
Route::get('/login','Admin\LoginController@check');*/
Route::get('/index/captcha/{tmp}', 'Admin\LoginController@captcha');

//测试
Route::get('/test','Admin\LoginController@test');
Route::get('/start','Admin\TestController@start');