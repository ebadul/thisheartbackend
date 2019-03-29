<?php

use Illuminate\Http\Request;

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

Route::post('login', 'API\AuthenticationController@login');
// Route::middleware('auth:api')->group( function () {
// 	Route::get('logout', 'API\AuthenticationController@logout');
// });
Route::post('register', 'API\AuthenticationController@register');

Route::group([
	'middleware' => 'auth:api'
  ], function() {
	  Route::get('logout', 'API\AuthenticationController@logout');
  });

Route::middleware('auth:api')->group( function () {
	Route::resource('accounts', 'API\AccountController');
});

Route::post('password/email', 'API\PasswordResetController@getResetToken');
Route::get('password/find/{token}', 'API\PasswordResetController@findToken');
Route::post('password/reset', 'API\PasswordResetController@reset');

Route::post('upload/image', 'API\MemoriesController@storeImage');
