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

Route::post('login', [AuthController::class, 'login']);

Route::group(['namespace' => 'API'], function () {
    Route::apiResource('domains', DomainController::class);
    /*
    Route::apiResource('measures', API\MeasureController::class);
    Route::apiResource('controls', API\ControlController::class);
    Route::apiResource('attbutes', API\AttributeController::class);
    Route::apiResource('documents', API\DocumentController::class);
    */
});
