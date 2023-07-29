<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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

Route::namespace('App\\Http\\Controllers\\API')->group(function(){
 
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');

    Route::group(['middleware'=>'auth:api'], function() {
        
        Route::apiResource('domains', DomainController::class);
        Route::apiResource('measures', MeasureController::class);
        Route::apiResource('controls', ControlController::class);
        Route::apiResource('attributes', AttributeController::class);
        Route::apiResource('documents', DocumentController::class);
 
    }); 
});
