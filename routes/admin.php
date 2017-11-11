<?php
Route::group(['middleware' => 'login.check'],function (){
    //Route::post('/check','Admin\LoginController@check');
});

