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

Route::get('/', function () {
    return redirect('/login');
});

// Route::get('/admin', function () {
//     return view('admin.login');
// })->name('admin');

 

// Route::get('/dashboard', function () {
//     return view('admin.dashboard');
// })->name('dashboard')->middleware('auth');;

 

// Route::get('/login', "API\PrimaryUserController@loginAdmin")->name('login');
Route::get('/login', [ 'as' => 'login', 'uses' => 'API\PrimaryUserController@loginAdmin'])->name('login');
Route::get('/logout', ['as'=>'logout','uses'=>"API\PrimaryUserController@adminLogout"])->name('logout')->middleware('auth');
Route::get('/admin', "API\PrimaryUserController@adminUser")->name('admin');
Route::post('/primary_user_login',[ 'as'=>'primary_user_login',
        "uses"=> "API\PrimaryUserController@primary_user_login"]);
Auth::routes();
Route::get('/home', "API\PrimaryUserController@dashboard")->name('home')->middleware('auth');
Route::get('/dashboard', "API\PrimaryUserController@dashboard")->name('dashboard')->middleware('auth');
Route::get('/email-test', "API\PrimaryUserController@emailTest")->name('email-test')->middleware('auth');

Route::get('/primary_user', "API\PrimaryUserController@primary_user");
Route::post('/primary_user_edit', "API\PrimaryUserController@updateUserById")->name("primary_user_edit");
Route::post('/user_status', "API\PrimaryUserController@changeStatus")->name("user_status");
Route::get('/delete_primary_user/{user_id}', "API\PrimaryUserController@delete_primary_user");
Route::get('/delete_beneficiary_user/{user_id}', "API\PrimaryUserController@delete_beneficiary_user");


Route::get('/beneficiary_user', "API\BeneficiaryUserController@beneficiary_user");
Route::post('/beneficiary_user_edit', "API\BeneficiaryUserController@updateBnUserById")->name("beneficiary_user_edit");
Route::post('/bnuser_status', "API\BeneficiaryUserController@changeStatus")->name("bnuser_status");

//package list
Route::get('/diagnosis_info', "API\MedicalHistoryController@diagnosis_info");
Route::any('/diagnosis_info_add', "API\MedicalHistoryController@diagnosis_info_add");
Route::post('/diagnosis_info_edit', "API\MedicalHistoryController@diagnosis_info_edit");
Route::get('/delete_diagnosis_info/{diagnosis_id}', "API\MedicalHistoryController@delete_diagnosis_info");
Route::get('/package_info', "API\PackagesController@package_info");
Route::get('/delete_package_info/{user_id}', "API\PackagesController@delete_package_info");
Route::post('/package_info_edit', "API\PackagesController@package_info_edit");
Route::any('/package_info_add', "API\PackagesController@package_info_add");
Route::get('/package_entity/{package_id}', "API\PackagesController@package_entities");
Route::get('/user_package', "API\PackagesController@user_package");
Route::post('/user_package_edit', "API\PackagesController@user_package_edit");
Route::get('/user_package_delete/{id}', "API\PackagesController@user_package_delete");
Route::get('/package_entities_info', "API\PackagesController@package_entities_info");
Route::any('/package_entities_info_add', "API\PackagesController@package_entities_info_add");
Route::post('/package_entities_info_edit', "API\PackagesController@package_entities_info_edit");
Route::any('/package_entities_info_delete/{id}', "API\PackagesController@package_entities_info_delete");
Route::get('/package_entities', "API\PackagesController@package_entities");
Route::any('/package_entities_add', "API\PackagesController@package_entities_add");
Route::post('/package_entities_edit', "API\PackagesController@package_entities_edit");
Route::any('/package_entities_delete/{id}', "API\PackagesController@package_entities_delete");
Route::get('/user_activities', "API\PrimaryUserController@user_activities");
Route::get('/free_account/{free_account_status}', "API\PrimaryUserController@free_account");
Route::post('/free_user_package_edit', "API\PrimaryUserController@free_user_package_edit");
Route::get('/user_activities_delete/{id}', "API\PrimaryUserController@user_activities_delete");
Route::get('/inactive_primary_users', "API\PrimaryUserController@inactive_primary_users")->middleware('auth');
Route::post('/inactive_user_notify_edit', "API\PrimaryUserController@inactive_user_notify_edit")->middleware('auth');
Route::get('/inactive_beneficiary_users', "API\PrimaryUserController@inactive_beneficiary_users")->middleware('auth');
Route::post('/inactive_user/send_email', "API\PrimaryUserController@inactive_user_send_email")->middleware('auth');
Route::post('/inactive_user/send_email_automation', "API\PrimaryUserController@inactive_user_send_email_automation")->middleware('auth');
Route::get('/unsubscribed_user/{subscribed_status}', "API\PrimaryUserController@unsubscribed_user")->middleware('auth');
Route::get('/billing_details', "API\PrimaryUserController@billing_details")->middleware('auth');
Route::get('/payment_charging/{user_id}', "API\PrimaryUserController@payment_charging")->middleware('auth');
Route::post('/admin_payment_charging', "API\PrimaryUserController@admin_payment_charging")->middleware('auth');



Route::get('/datatable', function () {
    return view('admin.datatable');
});


Route::get('/modified-template', function () {
    return view('admin.modified-template');
});


