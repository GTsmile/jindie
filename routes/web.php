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

Route::post('/check','Admin\LoginController@check');
Route::get('/check','Admin\LoginController@check');
Route::post('/logout','Admin\LoginController@logout');
Route::get('/test','Admin\LoginController@test');

Route::get('/', function () {
    return view('index');
});

