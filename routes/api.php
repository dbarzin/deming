<?php

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

Route::namespace('App\\Http\\Controllers\\API')->group(function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::apiResource('domains', 'DomainController');
        Route::apiResource('measures', 'MeasureController');
        Route::apiResource('controls', 'ControlController');
        Route::apiResource('attributes', 'AttributeController');
        Route::apiResource('documents', 'DocumentController');
        Route::apiResource('users', 'UserController');
    });
});
