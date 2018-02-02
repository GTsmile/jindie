<?php

// use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'login.check'],function (){
    //Route::post('/check','Admin\LoginController@check');
    Route::get('/getUser','Api\IndexController@getUser');

    Route::post('/getUser','Api\IndexController@getUser');
    Route::get('/exportUser','Api\IndexController@exportUser');
    Route::get('/getRole','Api\IndexController@getRole');

    Route::post('/getRole','Api\IndexController@getRole');
    Route::any('/getPositionUnit','Api\IndexController@getPositionUnit');

    Route::any('/updateRole','Api\IndexController@updateRole');
});




