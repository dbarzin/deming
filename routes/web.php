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

Auth::routes();

/* Index */
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::get('/index', 'HomeController@index');

/* Global-search engine */
Route::get('global-search', 'GlobalSearchController@search')->name('globalSearch');

/* Profile */
Route::get('/profile', 'ProfileController@index')->name('profile');
Route::post('/profile/update', 'ProfileController@updateProfile')->name('profile.update');
Route::get('/profile/avatar/{id}', 'ProfileController@avatar');

/* Controls */
Route::get('/controls/activate', 'ControlController@activate');
Route::get('/controls/disable', 'ControlController@disable');

/* Measurements */
Route::get('/measurement/show/{id}', 'MeasurementController@show');
Route::get('/measurement/make/{id}', 'MeasurementController@make');
Route::get('/measurement/plan/{id}', 'MeasurementController@plan');
Route::get('/measurement/edit/{id}', 'MeasurementController@edit');
Route::get('/measurement/template/{id}', 'MeasurementController@template');
Route::get('/measurement/delete/{id}', 'MeasurementController@destroy');
Route::post('/measurement/make', 'MeasurementController@doMake');
Route::post('/measurement/plan', 'MeasurementController@doPlan');
Route::post('/measurement/save', 'MeasurementController@save');
Route::get('/measurement/radar', 'MeasurementController@radar');
Route::get('/measurement/history', 'MeasurementController@history');
Route::get('/measurement/upload/{id}', 'MeasurementController@upload');


/* Documents */
Route::post('/doc/store','DocumentController@store');
Route::get('/doc/delete/{id}','DocumentController@delete');
Route::get('/doc/show/{id}','DocumentController@get');
Route::get('/doc/stats','DocumentController@stats');
Route::get('/doc/check','DocumentController@check');

Route::get('/doc/templates','DocumentController@listTemplates');
Route::get('/doc/template','DocumentController@getTemplate');
Route::post('/doc/template','DocumentController@saveTemplate');

/* Other */
Route::resource('domains', 'DomainController');
Route::resource('controls', 'ControlController');
Route::resource('measurements', 'MeasurementController');
Route::resource('users', 'UserController');

/* actions */
Route::get('/actions', 'ActionplanController@index');
Route::get('/action/{id}', 'ActionplanController@show');
Route::post('/action/save', 'ActionplanController@save');

/* Reports */
Route::get('/reports/pilotage', 'ReportController@pilotage');

/* Exports */
Route::get('/exports', function () { return view("exports"); });
Route::get('/export/domains', 'DomainController@export');
Route::get('/export/controls', 'ControlController@export');
Route::get('/export/measurements', 'MeasurementController@export');

/* test chart */
Route::get('/testChart', 'ReportController@testChart');

/* Generate test data */
Route::get('/generateTests', 'ReportController@generateTests');
