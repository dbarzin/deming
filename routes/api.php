<?php

use App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
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

Route::group(['namespace'=>'App\Http\Controllers\API', 'prefix'=>'/api' ], function()
{

    Route::post('login', [AuthController::class, 'login']);

    Route::apiResource('domains', DomainController::class);
    /*
    Route::apiResource('measures', API\MeasureController::class);
    Route::apiResource('controls', API\ControlController::class);
    Route::apiResource('attbutes', API\AttributeController::class);
    Route::apiResource('documents', API\DocumentController::class);
    */
});
