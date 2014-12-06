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

Route::get('/', array('as'=>'home','uses'=>'HomeController@home'));

# ------------------ Api -----------------------------------
Route::group(array('prefix' => 'api'), function()
{
	Route::get('install', 'ApiController@install');
  Route::get('search', 'ApiController@search');
  Route::get('token_test', 'ApiController@token_test');
	Route::get('test', 'ApiController@test');
	Route::post('register', 'ApiController@register');
  Route::post('login', 'ApiController@login');
	Route::post('logout', 'ApiController@logout');
  Route::post('proposal', 'ApiController@proposal');
});


# ------------------ default -------------------------------
Route::get('login', array('as'=>'login', 'uses'=>'AuthController@login'));
Route::get('logout', array('as'=>'logout', 'uses'=>'AuthController@logout'));
Route::post('login-post', array('as'=>'login-post', 'before' => 'csrf', 'uses'=>'AuthController@loginPost'));
Route::get('search', array('as'=>'search', 'uses'=>'HomeController@search'));
Route::get('about', array('as'=>'about', 'uses'=>'HomeController@about'));
Route::get('docs', array('as'=>'api-docs', 'uses'=>'HomeController@docs'));

# ------------------ Apps ----------------------------------
Route::get('apps.android', array('as'=>'android', 'uses'=>'UploadController@android'));
Route::get('apps.ios', array('as'=>'ios', 'uses'=>'UploadController@ios'));
Route::get('apps', array('as'=>'android', 'uses'=>'UploadController@android'));

Route::post('upload','UploadController@handle');
Route::get('upload', 'UploadController@handle');
Route::delete('/', 'UploadController@delete');
