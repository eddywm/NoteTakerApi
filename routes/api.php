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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => '/v1/'], function () {

	Route::group(['middleware' => 'cors'], function(){



		Route::post('authenticate', 'api\v1\AuthenticateController@authenticate');

    	Route::post('register', 'api\v1\AuthenticateController@register');

        Route::put('refreshToken', 'api\v1\AuthenticateController@refreshToken');


    Route::group(['middleware' => ['jwt.auth']  ], function () {



        Route::resource('/notes', 'api\v1\NoteController');

     });

	});


    
});
