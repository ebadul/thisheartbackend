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

//Normal User
Route::post('login', 'API\AuthenticationController@login');
Route::post('register', 'API\AuthenticationController@register');

//Beneficiary User
Route::post('beneficiary/login', 'API\AuthenticationController@loginBeneficiaryUser');
Route::post('beneficiary/register', 'API\AuthenticationController@registerBeneficiaryUser');

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

//Memories,,
//Route::middleware('auth:api')->group( function () {
	Route::post('image/upload', 'API\MemoriesController@storeImage');
	Route::get('image/getAll/{user_id}', 'API\MemoriesController@getAllImagesById');
	Route::get('image/getRecentByDay/{user_id}/{day}', 'API\MemoriesController@getRecentImagesByDay');
	Route::post('image/delete/{id}', 'API\MemoriesController@deleteImageById');

	Route::post('video/upload', 'API\MemoriesController@storeVideo');
	Route::get('video/getAll/{user_id}', 'API\MemoriesController@getAllVideoById');
	Route::get('video/getRecentByDay/{user_id}/{day}', 'API\MemoriesController@getRecentVideoByDay');
	Route::post('video/delete/{id}', 'API\MemoriesController@deleteVideoById');

	Route::post('record/upload', 'API\MemoriesController@storeAudioRecord');
	Route::get('record/getAll/{user_id}', 'API\MemoriesController@getAllAudioRecordById');
	Route::get('record/getRecentByDay/{user_id}/{day}', 'API\MemoriesController@getRecentAudioRecordByDay');
	Route::post('record/delete/{id}', 'API\MemoriesController@deleteAudioRecordById');
	Route::get('memories/getContentDataCount/{user_id}', 'API\MemoriesController@getContentDataCountById');
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
	Route::get('medichistory/getPersonTypeDataCount/{user_id}', 'API\MedicalHistoryController@getPersonTypeDataCountById');
//});

//Letters info
//Route::middleware('auth:api')->group( function () {
	Route::post('letter/store', 'API\LettersController@addLetter');
	Route::get('letter/getById/{user_id}', 'API\LettersController@getLettersById');
	Route::post('letter/updateBy/{id}', 'API\LettersController@updateLetterById');
	Route::post('letter/deleteBy/{id}', 'API\LettersController@deleteLetterById');
	//});

//Beneficiaries info
//Route::middleware('auth:api')->group( function () {
	Route::post('beneficiary/store', 'API\BeneficiaryController@addBeneficiary');
	Route::get('beneficiary/getById/{user_id}', 'API\BeneficiaryController@getBeneficiaryById');
	Route::post('beneficiary/updateBy/{id}', 'API\BeneficiaryController@updateBeneficiaryById');
	Route::post('beneficiary/deleteBy/{id}', 'API\BeneficiaryController@deleteBeneficiaryById');
	Route::post('beneficiary/resetCode/{id}', 'API\BeneficiaryController@resetBeneficiaryCode');
	Route::post('beneficiary/sendNewCode/{id}', 'API\BeneficiaryController@sendNewBeneficiaryCode');
	Route::post('beneficiary/validateCode', 'API\BeneficiaryController@validateCode');
	Route::post('beneficiary/validateLast4Social', 'API\BeneficiaryController@validateLast4Social');
	//});

	//Accounts info
//Route::middleware('auth:api')->group( function () {
	Route::post('account/store', 'API\AccountController@addAccount');
	Route::get('account/getByUserId/{user_id}', 'API\AccountController@getAccountByUserId');
	Route::post('account/updateBy/{id}', 'API\AccountController@updateAccountById');
	Route::post('account/deleteBy/{id}', 'API\AccountController@deleteAccountById');
	//});

//Route::middleware('auth:api')->group( function () {
	Route::get('/getQRCode/{user_id}','API\PasswordSecurityController@getQRCode');
	Route::post('/getQRCode','API\PasswordSecurityController@getQRCodePost');//user_id
	Route::post('/generate2faSecret','API\PasswordSecurityController@generate2faSecret');//user_id
	Route::post('/enable2fa','API\PasswordSecurityController@enable2fa');//user_id, verify_code
	Route::post('/disable2fa','API\PasswordSecurityController@disable2fa');//user_id, password
	//});


