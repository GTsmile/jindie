<?php
//登陆中间件
Route::group(['middleware' => 'login.check'],function (){
    //Route::post('/check','Admin\LoginController@check');
});