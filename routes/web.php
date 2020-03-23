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
    return view('welcome');
});

// Route::get('/admin', function () {
//     return view('admin.login');
// })->name('admin');

 

// Route::get('/dashboard', function () {
//     return view('admin.dashboard');
// })->name('dashboard')->middleware('auth');;

 

Route::get('/login', "API\PrimaryUserController@loginAdmin")->name('login');
Route::get('/logout', "API\PrimaryUserController@adminLogout")->name('logout')->middleware('auth');
Route::get('/admin', "API\PrimaryUserController@adminUser")->name('admin');
Auth::routes();
Route::get('/dashboard', "API\PrimaryUserController@dashboard")->name('dashboard')->middleware('auth');
Route::post('/primary_user_login', "API\PrimaryUserController@primary_user_login")->name('primary_user_login');
Route::get('/primary_user', "API\PrimaryUserController@primary_user");
Route::post('/primary_user_edit', "API\PrimaryUserController@updateUserById")->name("primary_user_edit");
Route::post('/user_status', "API\PrimaryUserController@changeStatus")->name("user_status");
Route::get('/delete_primary_user/{user_id}', "API\PrimaryUserController@delete_primary_user");
Route::get('/delete_beneficiary_user/{user_id}', "API\PrimaryUserController@delete_beneficiary_user");


Route::get('/beneficiary_user', "API\BeneficiaryUserController@beneficiary_user");
Route::post('/beneficiary_user_edit', "API\BeneficiaryUserController@updateBnUserById")->name("beneficiary_user_edit");
Route::post('/bnuser_status', "API\BeneficiaryUserController@changeStatus")->name("bnuser_status");

//package list
Route::get('/package_info', "API\PackagesController@package_info");
Route::get('/package_entity/{package_id}', "API\PackagesController@package_entities");
Route::get('/user_package', "API\PackagesController@user_package");
Route::get('/user_activities', "API\PrimaryUserController@user_activities");
Route::get('/inactive_primary_users', "API\PrimaryUserController@inactive_primary_users")->middleware('auth');
Route::get('/inactive_beneficiary_users', "API\PrimaryUserController@inactive_beneficiary_users")->middleware('auth');
Route::post('/inactive_user/send_email', "API\PrimaryUserController@inactive_user_send_email")->middleware('auth');
Route::post('/inactive_user/send_email_automation', "API\PrimaryUserController@inactive_user_send_email_automation")->middleware('auth');



Route::get('/datatable', function () {
    return view('admin.datatable');
});


Route::get('/modified-template', function () {
    return view('admin.modified-template');
});


