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

//Forget password
Route::post('password/email', 'API\PasswordResetController@getResetToken');
Route::get('password/find/{token}', 'API\PasswordResetController@findToken');
Route::post('password/reset', 'API\PasswordResetController@reset');
//Memories
Route::post('upload/image', 'API\MemoriesController@storeImage');

//Medical Info
Route::middleware('auth:api')->group( function () {
	Route::post('diagnosis/store', 'API\MedicalHistoryController@addDiagnosisName');
	Route::get('diagnosis/getAll', 'API\MedicalHistoryController@getAllDiagnosisName');
	Route::get('diagnosis/getBy/{id}', 'API\MedicalHistoryController@getDiagnosisNameById');
	Route::post('diagnosis/updateBy/{id}', 'API\MedicalHistoryController@updateDiagnosisById');
	Route::post('diagnosis/deleteBy/{id}', 'API\MedicalHistoryController@deleteDiagnosisById');

	//
	Route::post('medichist/store', 'API\MedicalHistoryController@addDiagnosisNameForPartner');
	Route::post('medichist/deleteBy/{id}', 'API\MedicalHistoryController@deleteHistoryById');
	Route::get('diagnosis/getAllBy/{id}', 'API\MedicalHistoryController@getHistoryByUserId');
});

