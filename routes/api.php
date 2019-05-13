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
//Route::middleware('auth:api')->group( function () {
	Route::post('image/upload', 'API\MemoriesController@storeImage');
	Route::get('image/getAll/{user_id}', 'API\MemoriesController@getAllImagesById');
	Route::get('image/getRecentByDay/{user_id}/{day}', 'API\MemoriesController@getRecentImagesByDay');
	Route::post('image/delete/{id}', 'API\MemoriesController@deleteImageById');

	Route::post('video/upload', 'API\MemoriesController@storeVideo');
	Route::get('video/getAll/{user_id}', 'API\MemoriesController@getAllVideoById');
	Route::get('video/getRecentByDay/{user_id}/{day}', 'API\MemoriesController@getRecentVideoByDay');
	Route::post('video/delete/{id}', 'API\MemoriesController@deleteVideoById');
//});

//Medical Info
//Route::middleware('auth:api')->group( function () {
	Route::post('diagnosis/store', 'API\MedicalHistoryController@addDiagnosisName');
	Route::get('diagnosis/getAll', 'API\MedicalHistoryController@getAllDiagnosisName');
	Route::get('diagnosis/getBy/{id}', 'API\MedicalHistoryController@getDiagnosisNameById');
	Route::post('diagnosis/updateBy/{id}', 'API\MedicalHistoryController@updateDiagnosisById');
	Route::post('diagnosis/deleteBy/{id}', 'API\MedicalHistoryController@deleteDiagnosisById');

	//
	Route::post('medichistory/store', 'API\MedicalHistoryController@saveMedicalHistory');
	Route::post('medichistory/deleteBy/{id}', 'API\MedicalHistoryController@deleteHistoryById');
	Route::get('medichistory/getAllById/{id}', 'API\MedicalHistoryController@getHistoryByUserId');
	Route::get('medichistory/getAllByType/{type}/{id}', 'API\MedicalHistoryController@getHistoryByMemberType');
	Route::get('medichistory/getAllById/{id}', 'API\MedicalHistoryController@getAllTypeHistoryByUser');
//});

//Letters info
//Route::middleware('auth:api')->group( function () {
	Route::post('letter/store', 'API\LettersController@addLetter');
	Route::get('letter/getById/{user_id}', 'API\LettersController@getLettersById');
	Route::post('letter/updateBy/{id}', 'API\LettersController@updateLetterById');
	Route::post('letter/deleteBy/{id}', 'API\LettersController@deleteLetterById');
	//});

