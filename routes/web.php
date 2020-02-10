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
Route::get('/dashboard', "API\PrimaryUserController@dashboard")->name('dashboard')->middleware('auth');
Route::post('/primary_user_login', "API\PrimaryUserController@primary_user_login")->name('primary_user_login');
Route::get('/primary_user', "API\PrimaryUserController@primary_user");
Route::post('/primary_user_edit', "API\PrimaryUserController@updateUserById")->name("primary_user_edit");
Route::post('/user_status', "API\PrimaryUserController@changeStatus")->name("user_status");


Route::get('/beneficiary_user', "API\BeneficiaryUserController@beneficiary_user");
Route::post('/beneficiary_user_edit', "API\BeneficiaryUserController@updateBnUserById")->name("beneficiary_user_edit");
Route::post('/bnuser_status', "API\BeneficiaryUserController@changeStatus")->name("bnuser_status");

//package list
Route::get('/package_info', "API\PackagesController@package_info");
Route::get('/package_entity/{package_id}', "API\PackagesController@package_entities");
Route::get('/user_package', "API\PackagesController@user_package");



Route::get('/datatable', function () {
    return view('admin.datatable');
});


Route::get('/modified-template', function () {
    return view('admin.modified-template');
});


