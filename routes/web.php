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

Route::namespace('App\\Http\\Controllers')->group(function () {
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
    Route::get('/measure/plan/{id}', 'MeasureController@plan');
    Route::post('/measure/activate/{id}', 'MeasureController@activate');
    Route::get('/measure/import', function () {
        return view('measures/import');
    });
    Route::post('/measure/import', 'MeasureController@import');

    /* Controls */
    Route::get('/control/show/{id}', 'ControlController@show');
    Route::get('/control/make/{id}', 'ControlController@make');
    Route::get('/control/edit/{id}', 'ControlController@edit');
    Route::get('/control/template/{id}', 'ControlController@template');
    Route::get('/control/delete/{id}', 'ControlController@destroy');
    Route::post('/control/make', 'ControlController@doMake');
    Route::post('/control/plan', 'ControlController@doPlan');
    Route::post('/control/unplan', 'ControlController@unplan');
    Route::post('/control/draft', 'ControlController@draft');
    Route::post('/control/save', 'ControlController@save');
    Route::get('/control/history', 'ControlController@history');
    Route::get('/control/upload/{id}', 'ControlController@upload');
    Route::get('/control/plan/{id}', 'ControlController@plan');

    /* Radars */
    Route::get('/control/radar/domains', 'ControlController@domains');
    Route::get('/control/radar/measures', 'ControlController@measures');
    Route::get('/control/radar/attributes', 'ControlController@attributes');

    /* Documents */
    Route::post('/doc/store', 'DocumentController@store');
    Route::get('/doc/delete/{id}', 'DocumentController@delete');
    Route::get('/doc/show/{id}', 'DocumentController@get');

    Route::get('/doc', 'DocumentController@index');
    Route::get('/doc/check', 'DocumentController@check');
    Route::get('/doc/template', 'DocumentController@getTemplate');
    Route::post('/doc/template', 'DocumentController@saveTemplate');

    Route::get('/config', 'ConfigurationController@index');
    Route::post('/config/save', 'ConfigurationController@save');

    /* Other */
    Route::resource('domains', 'DomainController');
    Route::resource('attributes', 'AttributeController');
    Route::resource('measures', 'MeasureController');
    Route::resource('controls', 'ControlController');
    Route::resource('users', 'UserController');

    /* actions */
    Route::get('/actions', 'ActionplanController@index');
    Route::get('/action/{id}', 'ActionplanController@show');
    Route::post('/action/save', 'ActionplanController@save');

    /* Reports */
    Route::get('/reports/pilotage', 'ReportController@pilotage');
    Route::get('/reports/soa', 'ReportController@soa');

    /* Exports */
    Route::get('/reports', function () {
        return view('reports');
    });

    Route::get('/export/domains', 'DomainController@export');
    Route::get('/export/attributes', 'AttributeController@export');
    Route::get('/export/measures', 'MeasureController@export');
    Route::get('/export/controls', 'ControlController@export');

});
