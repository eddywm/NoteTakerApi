<?php

use Illuminate\Http\Request;

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




Route::group(['prefix' => '/v1/'], function () {



        Route::post('authenticate', 'api\v1\AuthenticateController@authenticate');

        Route::post('register', 'api\v1\AuthenticateController@register');

        Route::post('refreshToken', 'api\v1\AuthenticateController@refreshToken');


        Route::group(['middleware' => ['auth.jwt.custom']], function () {


            Route::resource('/notes', 'api\v1\NoteController');

        });

});
