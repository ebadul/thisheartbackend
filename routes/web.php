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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/admin', function () {
    return view('admin.login');
});
Route::post('/dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard');


Route::get('/primary_user', "API\PrimaryUserController@primary_user");
Route::get('/beneficiary_user', "API\BeneficiaryUserController@beneficiary_user");
Route::get ("/primary_user_delete/{id}", "API\PrimaryUserController@deleteUserById")->name("primary_user_delete");
Route::get('/primary_user_edit', "API\PrimaryUserController@updateUserById")->name("primary_user_edit");


Route::get('/datatable', function () {
    return view('admin.datatable');
});


Route::get('/modified-template', function () {
    return view('admin.modified-template');
});


