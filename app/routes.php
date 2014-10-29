<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

# ------------------ Api -----------------------------------
Route::group(array('prefix' => 'api'), function()
{
	Route::get('install', 'ApiController@install');
	Route::get('register', 'ApiController@register');
    Route::get('login', 'ApiController@login');
	Route::get('logout', 'ApiController@logout');
	Route::get('search', 'ApiController@search');
	Route::get('token_test', 'ApiController@token_test');
	Route::get('test', 'ApiController@test');
});
