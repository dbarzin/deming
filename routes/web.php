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

    /* Socialite (auth) */
    Route::get('auth/redirect/{driver}', 'SocialiteController@redirect')->name('socialite.redirect');
    Route::get('auth/callback/{driver}', 'SocialiteController@callback')->name('socialite.callback');

    /* Testing */
    Route::get('/test', 'HomeController@test');

    /* Global-search engine */
    Route::get('global-search', 'GlobalSearchController@search');

    /* Profile */
    Route::get('/profile', 'ProfileController@index')->name('profile');
    Route::post('/profile/update', 'ProfileController@updateProfile')->name('profile.update');
    Route::get('/profile/avatar/{id}', 'ProfileController@avatar');

    /* About */
    Route::get('/about', 'HomeController@test');
    Route::view('/about', 'about');

    /* Measures */
    Route::get('/alice/index', 'MeasureController@index');
    Route::get('/alice/create', 'MeasureController@create');
    Route::post('/alice/store', 'MeasureController@store');
    Route::post('/alice/save/{id}', 'MeasureController@update');
    Route::get('/alice/{id}/edit', 'MeasureController@edit');
    Route::get('/alice/plan/{id}', 'MeasureController@plan');
    Route::get('/alice/show/{id}', 'MeasureController@show');
    Route::get('/alice/clone/{id}', 'MeasureController@clone');
    Route::post('/alice/delete/{id}', 'MeasureController@destroy');
    Route::post('/alice/activate/{id}', 'MeasureController@activate');
    Route::get('/alice/import', 'MeasureImportController@show');
    Route::post('/alice/import', 'MeasureImportController@import');

    /* Controls */
    Route::get('/bob/index', 'ControlController@index');
    Route::get('/bob/create', 'ControlController@create');
    Route::post('/bob/store', 'ControlController@store');
    Route::get('/bob/show/{id}', 'ControlController@show');
    Route::get('/bob/make/{id}', 'ControlController@make');
    Route::get('/bob/edit/{id}', 'ControlController@edit');
    Route::post('/bob/template/{id}', 'ControlController@template');
    Route::get('/bob/clone/{id}', 'ControlController@clone');
    Route::get('/bob/delete/{id}', 'ControlController@destroy');
    Route::post('/bob/make', 'ControlController@doMake');
    Route::post('/bob/accept', 'ControlController@accept');
    Route::post('/bob/reject', 'ControlController@reject');
    Route::post('/bob/plan', 'ControlController@doPlan');
    Route::post('/bob/unplan', 'ControlController@unplan');
    Route::post('/bob/draft', 'ControlController@draft');
    Route::post('/bob/save', 'ControlController@save');
    Route::get('/bob/history', 'ControlController@history');
    Route::get('/bob/upload/{id}', 'ControlController@upload');
    Route::get('/bob/plan/{id}', 'ControlController@plan');

    /* Radars */
    Route::get('/radar/domains', 'ControlController@domains');
    Route::get('/radar/alice', 'ControlController@measures');
    Route::get('/radar/attributes', 'ControlController@attributes');
    Route::get('/radar/bob', 'ControlController@tempo');

    /* Documents */
    Route::post('/doc/store', 'DocumentController@store');
    Route::get('/doc/delete/{id}', 'DocumentController@delete');
    Route::get('/doc/show/{id}', 'DocumentController@get');

    Route::get('/doc', 'DocumentController@index');
    Route::get('/doc/check', 'DocumentController@check');
    Route::get('/doc/template', 'DocumentController@getTemplate');
    Route::post('/doc/template', 'DocumentController@saveTemplate');

    /* Configuration */
    Route::get('/config', 'ConfigurationController@index');
    Route::post('/config/save', 'ConfigurationController@save');

    /* Other */
    Route::resource('domains', 'DomainController');
    Route::resource('attributes', 'AttributeController');
    Route::resource('users', 'UserController');

    /* Actions */
    Route::get('/actions', 'ActionController@index');
    Route::get('/action/show/{id}', 'ActionController@show');
    Route::get('/action/create', 'ActionController@create');
    Route::get('/action/edit/{id}', 'ActionController@edit');
    Route::get('/action/close/{id}', 'ActionController@close');

    Route::post('/action/store', 'ActionController@store');
    Route::post('/action/update', 'ActionController@update');
    Route::post('/action/save', 'ActionController@save');
    Route::post('/action/close', 'ActionController@doClose');
    Route::post('/action/delete', 'ActionController@delete');

    /* Reports */
    Route::get('/reports', 'ReportController@show');
    Route::get('/reports/pilotage', 'ReportController@pilotage');
    Route::get('/reports/soa', 'ReportController@soa');

    // Audit Logs
    Route::get('/logs', 'AuditLogsController@index');
    Route::get('/logs/show/{id}', 'AuditLogsController@show');
    Route::get('/logs/history/{id}', 'AuditLogsController@history');

    /* Exports */
    Route::get('/export/domains', 'DomainController@export');
    Route::get('/export/attributes', 'AttributeController@export');
    Route::get('/export/alices', 'MeasureController@export');
    Route::get('/export/bobs', 'ControlController@export');
    Route::get('/export/actions', 'ActionController@export');
});
