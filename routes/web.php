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

/* Measures */
Route::get('/measure/activate', 'MeasureController@activate');
Route::get('/measure/disable', 'MeasureController@disable');

/* Controls */
Route::get('/control/show/{id}', 'ControlController@show');
Route::get('/control/make/{id}', 'ControlController@make');
Route::get('/control/edit/{id}', 'ControlController@edit');
Route::get('/control/template/{id}', 'ControlController@template');
Route::get('/control/delete/{id}', 'ControlController@destroy');
Route::post('/control/make', 'ControlController@doMake');
Route::post('/control/plan', 'ControlController@doPlan');
Route::post('/control/draft', 'ControlController@draft');
Route::post('/control/save', 'ControlController@save');
Route::get('/control/radar', 'ControlController@radar');
Route::get('/control/history', 'ControlController@history');
Route::get('/control/upload/{id}', 'ControlController@upload');
Route::get('/control/plan/{id}', 'ControlController@plan');

/* Documents */
Route::post('/doc/store', 'DocumentController@store');
Route::get('/doc/delete/{id}', 'DocumentController@delete');
Route::get('/doc/show/{id}', 'DocumentController@get');
Route::get('/doc/stats', 'DocumentController@stats');
Route::get('/doc/check', 'DocumentController@check');

Route::get('/doc/templates', 'DocumentController@listTemplates');
Route::get('/doc/template', 'DocumentController@getTemplate');
Route::post('/doc/template', 'DocumentController@saveTemplate');

/* Other */
Route::resource('domains', 'DomainController');
Route::resource('measures', 'MeasureController');
Route::resource('controls', 'ControlController');
Route::resource('users', 'UserController');

/* actions */
Route::get('/actions', 'ActionplanController@index');
Route::get('/action/{id}', 'ActionplanController@show');
Route::post('/action/save', 'ActionplanController@save');

/* Reports */
Route::get('/reports/pilotage', 'ReportController@pilotage');

/* Exports */
Route::get('/exports', function () {
    return view('exports');
});
Route::get('/export/domains', 'DomainController@export');
Route::get('/export/measures', 'MeasureController@export');
Route::get('/export/controls', 'ControlController@export');

/* test chart */
Route::get('/testChart', 'ReportController@testChart');
